<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) die("ID inválido");

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
$recibo_existente = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1")->fetch_assoc();

$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGUEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECANICO" => ["TRANSMISION", "DIFERENCIALES", "CARDAN"],
  "SISTEMA HIDRAULICO" => ["BANCO DE VALVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRAULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCION", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRAULICOS", "ORBITROL", "TORQUES HUV (SATELITES)", "VALVULAS DE RETENCION", "REDUCTORES"],
  "SISTEMA ELECTRICO Y ELECTRONICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VALVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESION/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MODULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)"],
  "ESTETICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
  "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
];
$pesos = [
  "MOTOR" => 15,
  "SISTEMA MECANICO" => 15,
  "SISTEMA HIDRAULICO" => 30,
  "SISTEMA ELECTRICO Y ELECTRONICO" => 25,
  "ESTETICO" => 5,
  "CONSUMIBLES" => 10
];
$porcentajes = [];
foreach ($secciones as $s => $c) {
  $u = round($pesos[$s] / count($c), 2);
  foreach ($c as $i) $porcentajes[$i] = $u;
}
?>
<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='UTF-8'>
  <title>Recibo de Unidad</title>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
  <style>
    body { background-color: #001f3f; color: #fff; font-family: 'Segoe UI', sans-serif; }
    .container { background-color: #002b5c; padding: 2rem; border-radius: 1rem; max-width: 1200px; margin: auto; }
    .form-label { color: #ffc107; font-weight: bold; }
    .progress-bar { transition: width 0.4s ease; background-color: #28a745 !important; }
  </style>
</head>
<body>
<div class='container'>
  <h3 class='text-center'>Recibo de Unidad</h3>
  <form method='POST' action='guardar_recibo.php'>
    <input type='hidden' name='id_maquinaria' value='<?= $id_maquinaria ?>'>

    <div class='row mb-3'>
      <div class='col-md-4'><label class='form-label'>Equipo</label><input type='text' class='form-control' value='<?= htmlspecialchars($maquinaria['nombre']) ?>' readonly></div>
      <div class='col-md-4'><label class='form-label'>Marca</label><input type='text' class='form-control' value='<?= htmlspecialchars($maquinaria['marca']) ?>' readonly></div>
      <div class='col-md-4'><label class='form-label'>Modelo</label><input type='text' class='form-control' value='<?= htmlspecialchars($maquinaria['modelo']) ?>' readonly></div>
    </div>
