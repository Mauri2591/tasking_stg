<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpSpreadsheet\IOFactory as ExcelIOFactory;

class ExtractorDocumentos
{
    public static function extraer(string $ruta): array
    {
        if (!file_exists($ruta)) {
            return ['ok' => false, 'texto' => '', 'error' => 'Archivo no encontrado: ' . $ruta];
        }

        $ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));

        try {
            switch ($ext) {
                case 'docx':
                case 'doc':
                    return self::extraerDocx($ruta);

                case 'pdf':
                    return self::extraerPdf($ruta);

                case 'xlsx':
                case 'xls':
                    return self::extraerXlsx($ruta);

                default:
                    return ['ok' => false, 'texto' => '', 'error' => "Formato no soportado: .$ext"];
            }
        } catch (\Throwable $e) {
            return ['ok' => false, 'texto' => '', 'error' => $e->getMessage()];
        }
    }

    private static function extraerDocx(string $ruta): array
    {
        $phpWord = WordIOFactory::load($ruta);
        $texto = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $texto .= $element->getText() . "\n";
                } elseif (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $child) {
                        if (method_exists($child, 'getText')) {
                            $texto .= $child->getText();
                        }
                    }
                    $texto .= "\n";
                }
            }
        }

        return ['ok' => true, 'texto' => trim($texto), 'error' => null];
    }

    private static function extraerPdf(string $ruta): array
    {
        $parser = new PdfParser();
        $pdf    = $parser->parseFile($ruta);
        $texto  = $pdf->getText();

        return ['ok' => true, 'texto' => trim($texto), 'error' => null];
    }

    private static function extraerXlsx(string $ruta): array
    {
        $spreadsheet = ExcelIOFactory::load($ruta);
        $texto = '';

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $texto .= "Hoja: " . $sheet->getTitle() . "\n";

            foreach ($sheet->getRowIterator() as $row) {
                $valores = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    $valores[] = $cell->getFormattedValue();
                }

                $texto .= implode(" | ", $valores) . "\n";
            }
            $texto .= "\n";
        }

        return ['ok' => true, 'texto' => trim($texto), 'error' => null];
    }

    /**
     * Extrae el listado de vulnerabilidades de un xlsx como array estructurado
     * (en vez de texto plano), detectando dinámicamente las columnas por su
     * encabezado (no asume orden fijo). Pensado para hojas tipo "Listado de
     * Vulnerabilidades" con columnas: ID, Vulnerabilidad, Severidad, CVSS, etc.
     *
     * Deduplica por nombre de vulnerabilidad (la misma vulnerabilidad puede
     * repetirse en varias filas, una por cada target/host afectado), y cuenta
     * cuántos targets distintos la tienen.
     */
    public static function extraerVulnerabilidadesXlsx(string $ruta): array
    {
        if (!file_exists($ruta)) {
            return ['ok' => false, 'vulnerabilidades' => [], 'error' => 'Archivo no encontrado: ' . $ruta];
        }

        try {
            $spreadsheet = ExcelIOFactory::load($ruta);
            $sheet = $spreadsheet->getActiveSheet();

            $filas = $sheet->toArray(null, true, true, false);

            if (empty($filas)) {
                return ['ok' => false, 'vulnerabilidades' => [], 'error' => 'Hoja vacía'];
            }

            // Detectar índice de columnas por encabezado (case-insensitive)
            $encabezados = array_map(function ($h) {
                return mb_strtolower(trim((string) $h));
            }, $filas[0]);

            $idxVulnerabilidad = self::buscarIndiceColumna($encabezados, ['vulnerabilidad', 'vulnerability']);
            $idxSeveridad      = self::buscarIndiceColumna($encabezados, ['severidad', 'severity']);
            $idxCvss           = self::buscarIndiceColumna($encabezados, ['cvss']);
            $idxTarget         = self::buscarIndiceColumna($encabezados, ['target', 'host', 'hostname', 'ip']);

            if ($idxVulnerabilidad === null) {
                return ['ok' => false, 'vulnerabilidades' => [], 'error' => 'No se encontró columna de Vulnerabilidad en la hoja'];
            }

            $agrupadas = []; // nombre => ['nombre'=>, 'severidad'=>, 'cvss'=>, 'targets'=>[]]

            foreach (array_slice($filas, 1) as $fila) {
                $nombre = trim((string) ($fila[$idxVulnerabilidad] ?? ''));
                if ($nombre === '') continue;

                $clave = mb_strtolower($nombre);

                if (!isset($agrupadas[$clave])) {
                    $agrupadas[$clave] = [
                        'nombre'    => $nombre,
                        'severidad' => $idxSeveridad !== null ? trim((string) ($fila[$idxSeveridad] ?? '')) : null,
                        'cvss'      => $idxCvss !== null ? trim((string) ($fila[$idxCvss] ?? '')) : null,
                        'targets'   => [],
                    ];
                }

                if ($idxTarget !== null) {
                    $target = trim((string) ($fila[$idxTarget] ?? ''));
                    if ($target !== '' && !in_array($target, $agrupadas[$clave]['targets'], true)) {
                        $agrupadas[$clave]['targets'][] = $target;
                    }
                }
            }

            $resultado = array_values(array_map(function ($v) {
                $v['cantidad_targets'] = count($v['targets']);
                unset($v['targets']); // no hace falta exponer la lista completa, solo el conteo
                return $v;
            }, $agrupadas));

            return ['ok' => true, 'vulnerabilidades' => $resultado, 'error' => null];

        } catch (\Throwable $e) {
            return ['ok' => false, 'vulnerabilidades' => [], 'error' => $e->getMessage()];
        }
    }

    private static function buscarIndiceColumna(array $encabezados, array $posiblesNombres): ?int
    {
        foreach ($encabezados as $idx => $encabezado) {
            foreach ($posiblesNombres as $posible) {
                if (str_contains($encabezado, $posible)) {
                    return $idx;
                }
            }
        }
        return null;
    }
}