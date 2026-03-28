<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class Reportes
{
    private static function normalizarHoras($valor): string
    {
        if ($valor === null || $valor === '' || strtoupper((string)$valor) === 'NULL') {
            return '00:00';
        }

        $valor = trim((string)$valor);

        // HHH:MM
        if (preg_match('/^\d{1,4}:\d{2}$/', $valor)) {
            return $valor;
        }

        // Número → horas
        if (is_numeric($valor)) {
            return ((int)$valor) . ':00';
        }

        return '00:00';
    }

    private static function horasToMin(string $h): int
    {
        $h = self::normalizarHoras($h);
        [$hh, $mm] = array_map('intval', explode(':', $h));
        return ($hh * 60) + $mm;
    }

    private static function minToHoras(int $min): string
    {
        if ($min <= 0) {
            return '00:00';
        }

        $hh = intdiv($min, 60);
        $mm = $min % 60;
        return $hh . ':' . str_pad($mm, 2, '0', STR_PAD_LEFT);
    }

    public static function get_reporte_excel($data, $nombre)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //Encabezados
        $headers = [
            'ID',
            'CLIENTE',
            'CUIT',
            'TITULO',
            'REFERENCIA',
            'RECURRENTE',
            'RETEST DE PROYECTO',
            'FECHA VANTIVE',
            'FECHA INICIO',
            'FECHA FINALIZACION',
            'SECTOR',
            'PRODUCTO',
            'HORAS',
            'IPS',
            'URLS',
            'DISPOSITIVO',
            'AGENTE',
            'EQUIPO',
            'OTROS',
            'ESTADO'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        // 🔹 Crear mapa ID real → posición visual
        $id_to_pos = [];
        foreach ($data as $index => $fila) {
            $id_to_pos[$fila['id']] = $index + 1;
        }

        $rowNum = 2;

        // Mapa de colores
        $color_map = [];

        // 🔹 Mapa auxiliar para saber qué IDs tienen rechequeos asociados
        $ids_con_rechequeos = [];
        foreach ($data as $row) {
            if (!empty($row['rechequeo_de'])) {
                $ids_con_rechequeos[$row['rechequeo_de']] = true;
            }
        }

        // Recorrer y exportar filas
        foreach ($data as $key => $row) {
            if (!in_array($row['estado'], ["ELIMINADO", "CANCELADO"])) {

                // Resolver número de proyecto origen
                $num_rechequeo_de = '-';
                if (!empty($row['rechequeo_de'])) {
                    $num_rechequeo_de = isset($id_to_pos[$row['rechequeo_de']])
                        ? $id_to_pos[$row['rechequeo_de']]
                        : $row['rechequeo_de'];
                }

                // Insertar fila
                $sheet->fromArray([
                    $key + 1,
                    $row['cliente'] ?? '-',
                    $row['cuit'] ?? '-',
                    $row['titulo'] ?? '-',
                    $row['referencia'] ?? '-',
                    (empty($row['posicion_recurrencia']) ? '-' : $row['posicion_recurrencia']),
                    $num_rechequeo_de,
                    (!empty($row['fech_vantive']) ? date('d/m/Y', strtotime($row['fech_vantive'])) : 'SIN FECHA'),
                    (!empty($row['fech_inicio']) ? date('d/m/Y', strtotime($row['fech_inicio'])) : 'SIN FECHA'),
                    (!empty($row['fech_fin']) ? date('d/m/Y', strtotime($row['fech_fin'])) : 'SIN FECHA'),
                    $row['sector_nombre'] ?? '-',
                    $row['producto'] ?? '-',
                    $row['dimensionamiento'] ?? '-',
                    $row['ips'] ?? '-',
                    $row['urls'] ?? '-',
                    $row['dispositivos'] ?? '-',
                    $row['agentes'] ?? '-',
                    $row['equipos'] ?? '-',
                    $row['otros'] ?? '-',
                    $row['estado'] ?? '-'
                ], NULL, 'A' . $rowNum);

                // Si tiene rechequeo_de, pinta la celda F (RECHEQUEO DE PROYECTO)
                if (!empty($row['rechequeo_de'])) {
                    $referencia = $row['rechequeo_de']; // ID al que hace referencia

                    // Si ese ID no tiene color, se genera uno
                    if (!isset($color_map[$referencia])) {
                        $color_map[$referencia] = substr(str_shuffle('ABCDEF0123456789'), 0, 6);
                    }

                    $color_hex = $color_map[$referencia];

                    // Pintar celda F (rechequeo de proyecto)
                    $sheet->getStyle('G' . $rowNum)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($color_hex);

                    // Ajustar color del texto según contraste
                    $r = hexdec(substr($color_hex, 0, 2));
                    $g = hexdec(substr($color_hex, 2, 2));
                    $b = hexdec(substr($color_hex, 4, 2));
                    $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
                    $textColor = ($brightness < 140) ? 'FFFFFF' : '000000';

                    $sheet->getStyle('G' . $rowNum)->getFont()->getColor()->setRGB($textColor);
                }

                $proyecto_id = $row['id'];
                if (isset($ids_con_rechequeos[$proyecto_id])) {
                    if (!isset($color_map[$proyecto_id])) {
                        $color_map[$proyecto_id] = substr(str_shuffle('ABCDEF0123456789'), 0, 6);
                    }

                    $color_id_hex = $color_map[$proyecto_id];

                    $sheet->getStyle('A' . $rowNum)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($color_id_hex);

                    // Ajustar color del texto
                    $r = hexdec(substr($color_id_hex, 0, 2));
                    $g = hexdec(substr($color_id_hex, 2, 2));
                    $b = hexdec(substr($color_id_hex, 4, 2));
                    $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
                    $textColor = ($brightness < 140) ? 'FFFFFF' : '000000';

                    $sheet->getStyle('A' . $rowNum)->getFont()->getColor()->setRGB($textColor);
                }

                $rowNum++;
            }
        }

        // ===== Estilos del encabezado =====
        $headerRange = 'A1:T1';
        $sheet->setAutoFilter($headerRange);
        $headerStyle = $sheet->getStyle($headerRange);
        $headerStyle->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('43578F');
        $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $headerStyle->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Centrar columnas
        $centerColumns = ['A', 'D', 'E', 'F', 'G', 'H', 'I', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
        foreach ($centerColumns as $col) {
            $sheet->getStyle($col)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Autoajuste de columnas
        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->freezePane('A2');

        // Salida
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$nombre}.xlsx\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public static function total_excel($data, $nombre)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Extraer todas las categorías únicas
        $todas_categorias = [];
        foreach ($data as $row) {
            if (!empty($row['categoria']) && !in_array($row['categoria'], $todas_categorias)) {
                $todas_categorias[] = $row['categoria'];
            }
        }
        sort($todas_categorias);

        // Encabezados
        $headers = array_merge(['CLIENTE'], $todas_categorias, ['TOTAL PROYECTOS']);
        $sheet->fromArray($headers, NULL, 'A1');

        //Construir estructura pivotada
        $pivot = [];
        foreach ($data as $row) {
            $cliente = $row['client_rs'];
            $categoria = $row['categoria'] ?: '-';
            $cantidad = (int) $row['cantidad_proyectos'];

            if (!isset($pivot[$cliente])) {
                $pivot[$cliente] = array_fill_keys($todas_categorias, 0);
            }
            $pivot[$cliente][$categoria] += $cantidad;
        }

        // Escribir filas
        $rowNum = 2;
        $totales_categorias = array_fill_keys($todas_categorias, 0);
        $total_general = 0;

        foreach ($pivot as $cliente => $cats) {
            $fila = [$cliente];
            $subtotal = 0;

            foreach ($todas_categorias as $cat) {
                $valor = $cats[$cat] > 0 ? $cats[$cat] : '-';
                $fila[] = $valor;
                if ($cats[$cat] > 0) {
                    $totales_categorias[$cat] += $cats[$cat];
                    $subtotal += $cats[$cat];
                }
            }

            $fila[] = $subtotal;
            $total_general += $subtotal;
            $sheet->fromArray($fila, NULL, 'A' . $rowNum);
            $rowNum++;
        }

        $rowNum++;

        $fila_total = ['TOTAL'];
        foreach ($todas_categorias as $cat) {
            $fila_total[] = $totales_categorias[$cat];
        }
        $fila_total[] = $total_general;
        $sheet->fromArray($fila_total, NULL, 'A' . $rowNum);

        // Pintar la fila TOTAL en gris
        $ultimaCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A{$rowNum}:{$ultimaCol}{$rowNum}")
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('D9D9D9');

        $sheet->getStyle("A{$rowNum}:{$ultimaCol}{$rowNum}")
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setRGB('000000');

        $headerStyle = $sheet->getStyle("A1:{$ultimaCol}1");
        $headerStyle->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('43578F');
        $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        foreach (range(1, count($headers)) as $i) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getStyle($col)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:{$ultimaCol}" . ($rowNum - 1));

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$nombre}.xlsx\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public static function getDatosReporteSinFiltroXlsx($data, $nombre)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = [
            'ID',
            'CLIENTE',
            'CUIT',
            'TITULO',
            'REF',
            'PRODUCTO',
            'SECTOR',
            'DIMENSIONAMIENTO',
            'HS CONSUMIDAS TOTAL',
            'HS NEGATIVAS',
            'HS RESTANTES',
            'HS POR USUARIO',
            'HS PM',
            'FECHA INICIO',
            'FECHA FIN',
            'ESTADO'
        ];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;

        foreach ($data as $fila) {

            $dimensionamiento = self::normalizarHoras($fila['dimensionamiento']);
            $consumidas       = self::normalizarHoras($fila['horas_consumidas_total'] ?? '');

            $minResto =
                self::horasToMin($dimensionamiento) -
                self::horasToMin($consumidas);

            if ($minResto < 0) {
                // Excedido
                $hsRestantes = '00:00';
                $hsNegativas = self::minToHoras(abs($minResto));
            } else {
                // Dentro del dimensionamiento
                $hsRestantes = self::minToHoras($minResto);
                $hsNegativas = '00:00';
            }

            $sheet->fromArray([
                $row - 1,
                $fila['client_rs'],
                $fila['cuit'],
                $fila['titulo'],
                $fila['refProy'],
                $fila['producto'],
                $fila['sector'],
                $dimensionamiento,
                $consumidas,
                $hsNegativas,
                $hsRestantes,
                $fila['horas_consumidas_por_usuario'], // ← NO normalizar
                $fila['usuario_pm_calidad'],
                $fila['fech_inicio'],
                $fila['fech_fin'],
                $fila['estado']
            ], null, 'A' . $row);

            $row++;
        }

        // AutoFilter
        $sheet->setAutoFilter("A1:P" . ($row - 1));

        // Estilo encabezado
        $sheet->getStyle("A1:P1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '43578F']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);

        // Filas alternadas
        for ($i = 2; $i < $row; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$i}:G{$i}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F2F2F2');

                $sheet->getStyle("J{$i}:K{$i}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F2F2F2');
            }
        }

        // Colores HS
        for ($i = 2; $i < $row; $i++) {
            $neg = $sheet->getCell("J{$i}")->getValue();
            $res = $sheet->getCell("K{$i}")->getValue();

            // HS NEGATIVAS
            $sheet->getStyle("J{$i}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB(
                    $neg !== '00:00' ? 'FFA500' : '90EE90'
                );

            // HS RESTANTES
            $sheet->getStyle("K{$i}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB(
                    $res !== '00:00' ? 'ADD8E6' : '90EE90'
                );
        }

        // Centrado
        foreach (range('A', 'P') as $col) {
            $sheet->getStyle($col)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Bordes
        $sheet->getStyle("A1:P" . ($row - 1))->applyFromArray([
            'borders' => [
                'outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                'inside'  => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ]
        ]);

        // AutoSize
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Exportar
        ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$nombre}.xlsx\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public static function getDatosReporteSinFiltroDocx($data, $nombre, $fechaDesde = null, $fechaHasta = null)
    {
        $phpWord = new PhpWord();

        // Estilos
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 20]);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14]);

        $section = $phpWord->addSection();

        // ===== PAGINACIÓN =====
        $footer = $section->addFooter();

        $footer->addPreserveText(
            'Página {PAGE} de {NUMPAGES}',
            ['size' => 10],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );


        // Título
        $section->addText(
            'TIMESUMMARY',
            ['bold' => true, 'size' => 28],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );

        // Fecha
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $fechaActual = date("d/m/Y");

        $section->addText(
            "Generado el: " . $fechaActual,
            ['italic' => true, 'size' => 10],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );

        // Filtros
        if (!empty($fechaDesde) || !empty($fechaHasta)) {
            $txtFiltro = "Filtro por fechas: ";
            $txtFiltro .= (!empty($fechaDesde) ? "Desde $fechaDesde " : "");
            $txtFiltro .= (!empty($fechaHasta) ? "Hasta $fechaHasta" : "");
            $section->addText($txtFiltro, ['bold' => true]);
        }

        $section->addPageBreak();

        // Título
        $section->addText(
            'TABLA DE PROYECTOS GENERAL',
            ['bold' => true, 'size' => 20],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );

        $section->addTextBreak(1);

        $section->addText(
            "Aqui se presenta un resumen de cada proyecto, incluyendo el nombre del cliente, el producto contratado, el sector asignado, la cantidad de horas dimensionadas, las fechas de inicio y finalización, y el estado actual del proyecto."
        );

        // TABLA
        $table = $section->addTable([
            'borderSize' => 2,
            'borderColor' => '000000',
            'cellMargin' => 100
        ]);

        // Encabezados y ancho por columna
        $headers = [
            'CLIENTE'  => 3000,
            'PRODUCTO' => 2800,
            'SECTOR'   => 3000,
            'USUARIOS' => 3000,
            'HS' => 2500,
            'INICIO'   => 2500,
            'FIN'      => 2500,
            'ESTADO'   => 3000   // MAS GRANDE
        ];

        // Fila encabezado
        $bgStyle = ['bgColor' => '43578F'];

        $table->addRow();
        foreach ($headers as $titulo => $ancho) {
            $table->addCell($ancho, $bgStyle)->addText(
                $titulo,
                ['color' => 'FFFFFF', 'bold' => true, 'size' => 8]
            );
        }

        // Filas con datos
        foreach ($data as $fila) {
            $table->addRow();
            $table->addCell($headers['CLIENTE'])->addText($fila['client_rs'], ['size' => 7]);
            $table->addCell($headers['PRODUCTO'])->addText($fila['producto'], ['size' => 7]);
            $table->addCell($headers['SECTOR'])->addText($fila['sector'], ['size' => 7]);
            $table->addCell($headers['USUARIOS'])->addText($fila['usuarios_asignados'], ['size' => 7]);
            $table->addCell($headers['HS'])->addText($fila['dimensionamiento'], ['size' => 7]);
            $table->addCell($headers['INICIO'])->addText($fila['fech_inicio'], ['size' => 7]);
            $table->addCell($headers['FIN'])->addText($fila['fech_fin'], ['size' => 7]);
            $table->addCell($headers['ESTADO'])->addText($fila['estado'], ['size' => 7]);
        }

        $section->addPageBreak();

        $section->addText(
            "DETALLE POR PROYECTO",
            ['bold' => true, 'size' => 20],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );

        $section->addTextBreak(2);
        // Iterar por cada proyecto
        foreach ($data as $fila) {

            // Nombre cliente + producto
            $section->addText(
                strtoupper($fila['client_rs'] . " REF " . $fila['refProy']),
                ['bold' => true, 'size' => 12]
            );

            $section->addText(
                "Inicio: " . ($fila['fech_inicio'] ?: "N/A") .
                    "   |   Fin: " . ($fila['fech_fin'] ?: "N/A")
            );

            $section->addText(
                "Producto: " . ($fila['producto'])
            );

            // Dimensionamiento y fechas
            $section->addText(
                "Dimensionamiento: " . ($fila['dimensionamiento'] . " hs" ?: "N/A")
            );

            $section->addText(
                "Horas consumidas total: " . (empty($fila['horas_consumidas_total']) ? "00:00 hs" : $fila['horas_consumidas_total'] . " hs")
            );

            $section->addTextBreak(0.5);

            // Subtitulo colaboradores
            $section->addText("Horas consumidas por colaborador:", ['bold' => true]);

            // horas_consumidas_por_usuario ya viene así: "Mauricio 05:30, Rodrigo 02:45..."
            if (!empty($fila['horas_consumidas_por_usuario'])) {

                // Convertimos la cadena en items separados
                $colaboradores = explode(", ", $fila['horas_consumidas_por_usuario'] . " hs");

                // Lista de colaboradores & horas
                $listStyle = ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED];

                foreach ($colaboradores as $col) {
                    $section->addListItem($col, 0, null, $listStyle);
                }
            } else {
                $section->addText("Sin registros de carga.", ['italic' => true]);
            }

            // Subtitulo PM
            $section->addText("Horas consumidas PM:", ['bold' => true]);

            // horas_consumidas_por_usuario ya viene así: "Mauricio 05:30, Rodrigo 02:45..."
            if (!empty($fila['usuario_pm_calidad'])) {

                // Convertimos la cadena en items separados
                $colaboradores = explode(", ", $fila['usuario_pm_calidad'] . " hs");

                // Lista de colaboradores & horas
                $listStyle = ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED];

                foreach ($colaboradores as $col) {
                    $section->addListItem($col, 0, null, $listStyle);
                }
            } else {
                $section->addText("Sin registros de carga.", ['italic' => true]);
            }

            $section->addTextBreak(1);
        }
        $nombreArchivo = $nombre . "_" . $fechaActual;
        // Exportar DOCX
        $filename = "{$nombreArchivo}.docx";

        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Pragma: public');

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save("php://output");
        exit;
    }
}
