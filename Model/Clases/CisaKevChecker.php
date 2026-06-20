<?php

class CisaKevChecker
{
    private array $catalogo = [];
    private array $porCve = [];

    private const STOPWORDS = [
        'vulnerability',
        'vulnerabilities',
        'server',
        'remote',
        'the',
        'and',
        'for',
        'with',
        'in',
        'of',
        'a',
        'an',
        'its',
        'this',
        'that',
        'via',
        'using',
        'used',
        'code',
        'execution',
        'authentication',
        'bypass',
        'injection',
        'access',
        'without',
        'denial',
        'service',
        'command',
        'control',
        'privilege',
        'escalation',
        'disclosure',
        'information',
        'buffer',
        'overflow',
        'traversal',
        'arbitrary',
        'unauthenticated',
        'unauthorized',
        'multiple',
        'rce',
        'allows',
        'allow',
        'attacker',
        'attackers',
        'file',
        'write',
        'read',
        'request',
        'response',
        'forwarded',
        'accessible',
        'crafted',
    ];

    public function __construct(string $rutaCache)
    {
        if (!file_exists($rutaCache)) {
            return;
        }

        $json = file_get_contents($rutaCache);
        $data = json_decode($json, true);

        if (!$data || empty($data['vulnerabilities'])) {
            return;
        }

        $this->catalogo = $data['vulnerabilities'];

        foreach ($this->catalogo as $entrada) {
            if (!empty($entrada['cveID'])) {
                $this->porCve[strtoupper($entrada['cveID'])] = $entrada;
            }
        }
    }

    public function extraerCves(string $texto): array
    {
        preg_match_all('/CVE-\d{4}-\d{4,7}/i', $texto, $matches);
        return array_values(array_unique(array_map('strtoupper', $matches[0])));
    }

    public function buscarPorCve(string $cve): ?array
    {
        $cve = strtoupper(trim($cve));
        return $this->porCve[$cve] ?? null;
    }

    /**
     * Match aproximado por PALABRA COMPLETA contra vendorProject + product.
     * Reglas anti-falsos-positivos:
     * - Se descarta el contenido entre paréntesis antes de extraer palabras
     *   (suele ser un ID de advisory, ej: "(SA-CORE-2024-007)", no aporta).
     * - Si hay 1 sola palabra clave útil, alcanza con que matchee esa.
     * - Si hay 2+ palabras clave, se exige que coincidan AL MENOS 2,
     *   para evitar que una sola palabra genérica (ej: "agent") arrastre
     *   coincidencias casuales sin relación real con la vulnerabilidad.
     */
    public function buscarPorNombre(string $nombreVulnerabilidad, int $maxResultados = 3): array
    {
        // Sacar contenido entre paréntesis (IDs de advisory, no aportan al match)
        $nombreSinParentesis = preg_replace('/\([^)]*\)/', ' ', $nombreVulnerabilidad);

        $nombreLower = mb_strtolower($nombreSinParentesis);
        $palabrasClave = preg_split('/[\s\-\/]+/', $nombreLower, -1, PREG_SPLIT_NO_EMPTY);

        $palabrasClave = array_values(array_unique(array_filter($palabrasClave, function ($p) {
            return mb_strlen($p) >= 4 && !in_array($p, self::STOPWORDS, true) && !is_numeric($p);
        })));

        if (empty($palabrasClave)) {
            return [];
        }

        $minRequerido = count($palabrasClave) === 1 ? 1 : 2;

        $candidatos = [];

        foreach ($this->catalogo as $entrada) {
            $textoEntrada = mb_strtolower(
                ($entrada['vendorProject'] ?? '') . ' ' .
                    ($entrada['product'] ?? '')
            );

            $coincidencias = 0;
            foreach ($palabrasClave as $palabra) {
                $patron = '/\b' . preg_quote($palabra, '/') . '\b/u';
                if (preg_match($patron, $textoEntrada)) {
                    $coincidencias++;
                }
            }

            if ($coincidencias >= $minRequerido) {
                $candidatos[] = [
                    'entrada' => $entrada,
                    'score' => $coincidencias,
                ];
            }
        }

        usort($candidatos, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice(array_map(fn($c) => $c['entrada'], $candidatos), 0, $maxResultados);
    }

    public function analizarTexto(string $texto): array
    {
        $resultado = [
            'confirmados' => [],
            'aproximados' => [],
        ];

        $cves = $this->extraerCves($texto);
        foreach ($cves as $cve) {
            $match = $this->buscarPorCve($cve);
            if ($match) {
                $resultado['confirmados'][] = $match;
            }
        }

        return $resultado;
    }

    public function totalEntradas(): int
    {
        return count($this->catalogo);
    }

    /**
     * Cruza un array de vulnerabilidades (formato de extraerVulnerabilidadesXlsx)
     * contra el catálogo KEV y devuelve un bloque de texto formateado, listo
     * para inyectar como contexto verificado en el prompt de la IA.
     *
     * Prioridad de match por cada vulnerabilidad:
     * 1. Si el nombre trae un CVE explícito (ej: "... (CVE-2024-7347)") → match exacto.
     * 2. Si no, se intenta match aproximado por nombre de producto/vendor.
     * 3. Si no hay ninguna coincidencia, se omite (no se inventa relación).
     */
    public function generarContextoVerificado(array $vulnerabilidadesXlsx): string
    {
        $confirmados = [];
        $aproximados = [];

        foreach ($vulnerabilidadesXlsx as $vuln) {
            $nombre = $vuln['nombre'] ?? '';
            if ($nombre === '') continue;

            $cvesEnNombre = $this->extraerCves($nombre);
            $matcheado = false;

            foreach ($cvesEnNombre as $cve) {
                $match = $this->buscarPorCve($cve);
                if ($match) {
                    $confirmados[] = [
                        'hallazgo_original' => $nombre,
                        'severidad_reportada' => $vuln['severidad'] ?? null,
                        'cantidad_targets' => $vuln['cantidad_targets'] ?? null,
                        'kev' => $match,
                    ];
                    $matcheado = true;
                }
            }

            if ($matcheado) continue;

            $candidatos = $this->buscarPorNombre($nombre, 1);
            if (!empty($candidatos)) {
                $aproximados[] = [
                    'hallazgo_original' => $nombre,
                    'severidad_reportada' => $vuln['severidad'] ?? null,
                    'cantidad_targets' => $vuln['cantidad_targets'] ?? null,
                    'kev' => $candidatos[0],
                ];
            }
        }

        if (empty($confirmados) && empty($aproximados)) {
            return '';
        }

        $texto = "=== CONTEXTO VERIFICADO: Catálogo CISA KEV (Known Exploited Vulnerabilities) ===\n";
        $texto .= "Esta información proviene directamente del catálogo oficial de CISA, NO de tu conocimiento general. Es la fuente más confiable disponible sobre explotación activa real.\n\n";

        if (!empty($confirmados)) {
            $texto .= "--- Coincidencias CONFIRMADAS (CVE exacto presente en el catálogo) ---\n";
            foreach ($confirmados as $c) {
                $ransomware = $c['kev']['knownRansomwareCampaignUse'] ?? 'Unknown';
                $texto .= sprintf(
                    "- Hallazgo del informe: \"%s\" (severidad reportada: %s, %s host(s) afectado(s))\n  → CVE: %s | %s | Uso conocido en campañas de ransomware: %s\n\n",
                    $c['hallazgo_original'],
                    $c['severidad_reportada'] ?? 'N/D',
                    $c['cantidad_targets'] ?? '?',
                    $c['kev']['cveID'],
                    $c['kev']['vulnerabilityName'],
                    $ransomware
                );
            }
        }

        if (!empty($aproximados)) {
            $texto .= "--- Coincidencias APROXIMADAS (mismo producto/vendor en KEV, pero sin confirmar que sea exactamente la misma vulnerabilidad — verificar antes de afirmar explotación activa) ---\n";
            foreach ($aproximados as $a) {
                $texto .= sprintf(
                    "- Hallazgo del informe: \"%s\" (severidad reportada: %s)\n  → Posible relación con: %s (%s) del mismo proveedor (%s). NO está confirmado que sea la misma vulnerabilidad exacta.\n\n",
                    $a['hallazgo_original'],
                    $a['severidad_reportada'] ?? 'N/D',
                    $a['kev']['cveID'],
                    $a['kev']['vulnerabilityName'],
                    $a['kev']['vendorProject']
                );
            }
        }

        $texto .= "=== FIN CONTEXTO VERIFICADO ===\n\n";

        return $texto;
    }

    /**
     * Determina si un texto (prompt del usuario o contenido del documento) tiene
     * indicios suficientes de tratar sobre vulnerabilidades/CVEs/gestión de riesgo
     * como para que tenga sentido cruzarlo contra el catálogo CISA KEV. Se usa
     * únicamente en MODO PERSONALIZADO, donde el usuario puede subir cualquier
     * tipo de documento de ciberseguridad (no solo informes de vulnerabilidades).
     * En modo DEFAULT este chequeo no aplica: el prompt default ya está armado
     * para análisis de vulnerabilidades, sin importar el área/sector del proyecto.
     */
    public static function pareceContenidoDeVulnerabilidades(string $texto): bool
    {
        $textoLower = mb_strtolower($texto);

        $palabrasClave = [
            'vulnerabilidad',
            'vulnerabilidades',
            'vulnerability',
            'vulnerabilities',
            'cve-',
            'cwe-',
            'exploit',
            'cvss',
            'pentest',
            'penetration test',
            'ethical hacking',
            'análisis de vulnerabilidades',
            'parche',
            'patch',
            'severidad crítica',
            'severidad alta',
            'rce',
            'remote code execution',
            'gestión de riesgo',
            'gestion de riesgo',
            'riesgo cibernético',
            'riesgo informático',
            'incidente de seguridad',
            'amenaza',
            'malware',
            'ransomware',
        ];

        foreach ($palabrasClave as $palabra) {
            if (str_contains($textoLower, $palabra)) {
                return true;
            }
        }

        return false;
    }
}
