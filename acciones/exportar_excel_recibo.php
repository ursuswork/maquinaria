<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) {
  die("❌ ID de maquinaria inválido.");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) {
  die("❌ Maquinaria no encontrada.");
}

$recibo = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria")->fetch_assoc();
if (!$recibo) {
  die("❌ Recibo no encontrado.");
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Recibo Unidad");

// Encabezados principales
$sheet->setCellValue('A1', 'Recibo de Unidad');
$sheet->setCellValue('A3', 'Equipo');
$sheet->setCellValue('B3', $maquinaria['nombre']);
$sheet->setCellValue('A4', 'Marca');
$sheet->setCellValue('B4', $maquinaria['marca']);
$sheet->setCellValue('A5', 'Modelo');
$sheet->setCellValue('B5', $maquinaria['modelo']);
$sheet->setCellValue('A6', 'Empresa Origen');
$sheet->setCellValue('B6', $recibo['empresa_origen']);
$sheet->setCellValue('A7', 'Empresa Destino');
$sheet->setCellValue('B7', $recibo['empresa_destino']);
$sheet->setCellValue('A8', 'Condición Estimada');
$sheet->setCellValue('B8', $recibo['condicion_estimada'] . '%');

// Componentes (desde fila 10)
$fila = 10;
$sheet->setCellValue("A$fila", "Componente");
$sheet->setCellValue("B$fila", "Estado");
$fila++;

foreach ($recibo as $componente => $estado) {
  if (!in_array($componente, ['id', 'id_maquinaria', 'empresa_origen', 'empresa_destino', 'condicion_estimada', 'observaciones'])) {
    $sheet->setCellValue("A$fila", ucfirst(str_replace('_', ' ', $componente)));
    $sheet->setCellValue("B$fila", ucfirst($estado));
    $fila++;
  }
}

// Observaciones
$sheet->setCellValue("A$fila", "Observaciones:");
$sheet->setCellValue("B$fila", $recibo['observaciones'] ?? '');

// Descargar archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="recibo_unidad_' . $id_maquinaria . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
