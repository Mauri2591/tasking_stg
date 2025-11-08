<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reportes
{
    public static function get_reporte_excel($data, $nombre)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 🧩 Encabezados
        $headers = [
            'ID',
            'CLIENTE',
            'TITULO',
            'RECURRENTE',
            'RECHEQUEO',
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

        // 🔹 Crear mapa ID real → posición visual (para los rechequeos)
        $id_to_pos = [];
        foreach ($data as $index => $fila) {
            $id_to_pos[$fila['id']] = $index + 1;
        }

        $rowNum = 2;

        // 🔹 Recorrer y exportar filas
        foreach ($data as $key => $row) {
            if (!in_array($row['estado'], ["FIN SIN IMPLEM", "ELIMINADO", "CANCELADO"])) {

                $num_rechequeo_de = '-';
                if (!empty($row['rechequeo_de'])) {
                    $num_rechequeo_de = isset($id_to_pos[$row['rechequeo_de']])
                        ? $id_to_pos[$row['rechequeo_de']]
                        : $row['rechequeo_de'];
                }

                $sheet->fromArray([
                    $key + 1,
                    $row['cliente'],
                    $row['titulo'],
                    (empty($row['posicion_recurrencia']) ? '-' : $row['posicion_recurrencia']),
                    (empty($row['rechequeo']) ? '-' : $row['rechequeo']),
                    $num_rechequeo_de,
                    (!empty($row['fech_vantive']) ? date('d/m/Y', strtotime($row['fech_vantive'])) : 'SIN FECHA'),
                    (!empty($row['fech_inicio']) ? date('d/m/Y', strtotime($row['fech_inicio'])) : 'SIN FECHA'),
                    (!empty($row['fech_fin']) ? date('d/m/Y', strtotime($row['fech_fin'])) : 'SIN FECHA'),
                    $row['sector_nombre'],
                    $row['producto'],
                    $row['dimensionamiento'],
                    $row['estado']
                ], NULL, 'A' . $rowNum++);
            }
        }

        //===== Estilos de encabezado =====
        $headerRange = 'A1:M1';
        $headerStyle = $sheet->getStyle($headerRange);
        $headerStyle->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('43578f');
        $headerStyle->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        //Centrar todo el encabezado
        $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        //Centrar también el contenido de las columnas deseadas
        $centerColumns = ['D', 'E', 'F', 'G', 'H', 'I', 'L']; // Recurrencia, Rechequeo, Rechequeo de proyecto, Fechas, Horas
        foreach ($centerColumns as $col) {
            $sheet->getStyle($col)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        //Ajustar ancho automáticamente
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        //Congelar encabezado
        $sheet->freezePane('A2');

        // 🧾 Salida
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$nombre}.xlsx\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
