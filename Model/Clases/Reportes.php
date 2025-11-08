<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reportes
{
    public static function get_reporte_excel($data, $nombre)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = ['ID','CLIENTE', 'TITULO', 'RECURRENTE', 'RECHEQUEO', 'RECHEQUEO DE',  'FECHA VANTIVE', 'FECHA INICIO', 'FECHA FINALIZACION', 'SECTOR', 'PRODUCTO', 'HORAS', 'ESTADO'];
        $sheet->fromArray($headers, NULL, 'A1');

        $rowNum = 2;
        foreach ($data as $key => $row) {
            if (!in_array($row['estado'], ["FIN SIN IMPLEM", "ELIMINADO", "CANCELADO"])) {
                $sheet->fromArray([
                    $key+1,
                    $row['cliente'],
                    $row['titulo'],
                    (empty($row['posicion_recurrencia']) ? '-' : $row['posicion_recurrencia']),
                    (empty($row['rechequeo']) ? '-' : $row['rechequeo']),
                    (empty($row['rechequeo_de']) ? '-' : $row['rechequeo_de']),
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

        // Establecer tipo de salida
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$nombre}.xlsx\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
