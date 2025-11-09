<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reportes
{
    public static function get_reporte_excel($data, $nombre)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //Encabezados
        $headers = [
            'ID',
            'CLIENTE',
            'TITULO',
            'RECURRENTE',
            'RECHEQUEO DE PROYECTO',
            'FECHA VANTIVE',
            'FECHA INICIO',
            'FECHA FINALIZACION',
            'SECTOR',
            'PRODUCTO',
            'HORAS',
            'ESTADO'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        // 🔹 Crear mapa ID real → posición visual
        $id_to_pos = [];
        foreach ($data as $index => $fila) {
            $id_to_pos[$fila['id']] = $index + 1;
        }

        $rowNum = 2;

        // 🎨 Mapa de colores
        $color_map = [];

        // 🔹 Mapa auxiliar para saber qué IDs tienen rechequeos asociados
        $ids_con_rechequeos = [];
        foreach ($data as $row) {
            if (!empty($row['rechequeo_de'])) {
                $ids_con_rechequeos[$row['rechequeo_de']] = true;
            }
        }

        // 🔹 Recorrer y exportar filas
        foreach ($data as $key => $row) {
            if (!in_array($row['estado'], ["FIN SIN IMPLEM", "ELIMINADO", "CANCELADO"])) {

                // 🧠 Resolver número de proyecto origen
                $num_rechequeo_de = '-';
                if (!empty($row['rechequeo_de'])) {
                    $num_rechequeo_de = isset($id_to_pos[$row['rechequeo_de']])
                        ? $id_to_pos[$row['rechequeo_de']]
                        : $row['rechequeo_de'];
                }

                // 📋 Insertar fila
                $sheet->fromArray([
                    $key + 1,
                    $row['cliente'],
                    $row['titulo'],
                    (empty($row['posicion_recurrencia']) ? '-' : $row['posicion_recurrencia']),
                    $num_rechequeo_de,
                    (!empty($row['fech_vantive']) ? date('d/m/Y', strtotime($row['fech_vantive'])) : 'SIN FECHA'),
                    (!empty($row['fech_inicio']) ? date('d/m/Y', strtotime($row['fech_inicio'])) : 'SIN FECHA'),
                    (!empty($row['fech_fin']) ? date('d/m/Y', strtotime($row['fech_fin'])) : 'SIN FECHA'),
                    $row['sector_nombre'],
                    $row['producto'],
                    $row['dimensionamiento'],
                    $row['estado']
                ], NULL, 'A' . $rowNum);

                // 🎨 Si tiene rechequeo_de, pinta la celda F (RECHEQUEO DE PROYECTO)
                if (!empty($row['rechequeo_de'])) {
                    $referencia = $row['rechequeo_de']; // ID al que hace referencia

                    // Si ese ID no tiene color, se genera uno
                    if (!isset($color_map[$referencia])) {
                        $color_map[$referencia] = substr(str_shuffle('ABCDEF0123456789'), 0, 6);
                    }

                    $color_hex = $color_map[$referencia];

                    // Pintar celda F (rechequeo de proyecto)
                    $sheet->getStyle('E' . $rowNum)
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

                    $sheet->getStyle('F' . $rowNum)->getFont()->getColor()->setRGB($textColor);
                }

                // 🎨 Pintar el ID solo si ese ID tiene rechequeos asociados
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
        $headerRange = 'A1:M1';
        $headerStyle = $sheet->getStyle($headerRange);
        $headerStyle->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('43578F');
        $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $headerStyle->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Centrar columnas
        $centerColumns = ['A', 'D', 'E', 'F', 'G', 'H', 'I', 'L'];
        foreach ($centerColumns as $col) {
            $sheet->getStyle($col)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Autoajuste de columnas
        foreach (range('A', 'M') as $col) {
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

    // Agregar salto de línea antes del total
    $rowNum++; // deja una fila vacía visualmente

    // 🧩 6️⃣ Fila de totales (gris)
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
        ->setRGB('D9D9D9'); // gris claro

    $sheet->getStyle("A{$rowNum}:{$ultimaCol}{$rowNum}")
        ->getFont()
        ->setBold(true)
        ->getColor()
        ->setRGB('000000');

    // Estilos del encabezado
    $headerStyle = $sheet->getStyle("A1:{$ultimaCol}1");
    $headerStyle->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setRGB('43578F');
    $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // Centrar y ajustar ancho
    foreach (range(1, count($headers)) as $i) {
        $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
        $sheet->getStyle($col)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Congelar encabezado
    $sheet->freezePane('A2');

    // Exportar
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"{$nombre}.xlsx\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}


}
