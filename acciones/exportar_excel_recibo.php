<?php
require '../vendor/autoload.php';
include '../conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) {
  die("ID inválido.");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
$recibo = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria")->fetch_assoc();

if (!$maquinaria || !$recibo) {
  die("Datos no encontrados.");
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Recibo Unidad");

// Encabezados principales
$sheet->setCellValue('A1', 'Recibo de Unidad');
$sheet->mergeCells('A1:D1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

// Datos generales
$sheet->setCellValue('A3', 'Equipo');
$sheet->setCellValue('B3', $maquinaria['nombre']);
$sheet->setCellValue('A4', 'Marca');
$sheet->setCellValue('B4', $maquinaria['marca'] ?? '');
$sheet->setCellValue('A5', 'Modelo');
$sheet->setCellValue('B5', $maquinaria['modelo']);
$sheet->setCellValue('A6', 'Empresa Origen');
$sheet->setCellValue('B6', $recibo['empresa_origen']);
$sheet->setCellValue('A7', 'Empresa Destino');
$sheet->setCellValue('B7', $recibo['empresa_destino']);
$sheet->setCellValue('A8', 'Condición Estimada');
$sheet->setCellValue('B8', $recibo['condicion_estimada'] . '%');

// Componentes
$fila = 10;
$sheet->setCellValue("A{$fila}", "Componente");
$sheet->setCellValue("B{$fila}", "Estado");
$sheet->getStyle("A{$fila}:B{$fila}")->getFont()->setBold(true);
$fila++;

foreach ($recibo as $clave => $valor) {
    if (!in_array($clave, ['id', 'id_maquinaria', 'empresa_origen', 'empresa_destino', 'observaciones', 'condicion_estimada'])) {
        $sheet->setCellValue("A{$fila}", $clave);
        $sheet->setCellValue("B{$fila}", ucfirst($valor));
        $fila++;
    }
}

// Observaciones
$fila += 1;
$sheet->setCellValue("A{$fila}", "Observaciones");
$sheet->mergeCells("A{$fila}:D{$fila}");
$sheet->getStyle("A{$fila}")->getFont()->setBold(true);
$fila++;
$sheet->setCellValue("A{$fila}", $recibo['observaciones'] ?? '');
$sheet->mergeCells("A{$fila}:D{$fila}");

// Descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="recibo_unidad_' . $id_maquinaria . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
