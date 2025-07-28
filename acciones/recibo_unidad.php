<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) {
  die("Error: ID de maquinaria inválido.");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) {
  die("Error: Maquinaria no encontrada.");
}

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
foreach ($secciones as $seccion => $componentes) {
  $porcentaje_por_componente = round($pesos[$seccion] / count($componentes), 2);
  foreach ($componentes as $componente) {
    $porcentajes[$componente] = $porcentaje_por_componente;
  }
}

$recibo_existente = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recibo de Unidad</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #001f3f; color: #ffffff; font-family: 'Segoe UI', sans-serif; }
    .container { background-color: #002b5c; padding: 2rem; border-radius: 1rem; max-width: 1200px; margin: auto; box-shadow: 0 0 20px #000000; }
    h3, h5 { color: #ffc107; border-bottom: 2px solid #ffc107; padding-bottom: .5rem; margin-bottom: 1rem; }
    .form-label { color: #ffc107; font-weight: bold; }
    .form-control { background-color: #003366; color: #ffffff; border: 1px solid #0059b3; margin-bottom: 1rem; }
    .btn-primary { background-color: #0056b3; border: none; font-weight: bold; }
    .btn-warning { background-color: #ffc107; border: none; font-weight: bold; color: #000000; }
    .progress-bar { transition: width 0.4s ease; background-color: #28a745 !important; }
  </style>
</head>
<body>
<div class="container py-4">
  <h3 class="text-center">Recibo de Unidad</h3>
  <form method="POST" action="guardar_recibo.php">
    <input type="hidden" name="id_maquinaria" value="<?= $id_maquinaria ?>">

    <div class="row mb-3">
      <div class="col-md-4">
        <label class="form-label">Equipo</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($maquinaria['nombre']) ?>" readonly>
      </div>
      <div class="col-md-4">
        <label class="form-label">Marca</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($maquinaria['marca']) ?>" readonly>
      </div>
      <div class="col-md-4">
        <label class="form-label">Modelo</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($maquinaria['modelo']) ?>" readonly>
      </div>
    </div>
<hr><h5>MOTOR</h5>
<div class='progress mb-3'><div class='progress-bar' id='barra_motor' style='width: 0%'>0%</div></div>
<div class='row'>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CILINDROS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CILINDROS]' id='CILINDROS_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='CILINDROS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CILINDROS]' id='CILINDROS_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='CILINDROS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CILINDROS]' id='CILINDROS_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='CILINDROS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>PISTONES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[PISTONES]' id='PISTONES_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='PISTONES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PISTONES]' id='PISTONES_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='PISTONES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PISTONES]' id='PISTONES_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='PISTONES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>ANILLOS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[ANILLOS]' id='ANILLOS_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='ANILLOS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ANILLOS]' id='ANILLOS_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='ANILLOS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ANILLOS]' id='ANILLOS_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='ANILLOS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>INYECTORES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[INYECTORES]' id='INYECTORES_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='INYECTORES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[INYECTORES]' id='INYECTORES_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='INYECTORES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[INYECTORES]' id='INYECTORES_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='INYECTORES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>BLOCK</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[BLOCK]' id='BLOCK_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='BLOCK_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BLOCK]' id='BLOCK_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='BLOCK_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BLOCK]' id='BLOCK_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='BLOCK_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CABEZA</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CABEZA]' id='CABEZA_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='CABEZA_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CABEZA]' id='CABEZA_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='CABEZA_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CABEZA]' id='CABEZA_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='CABEZA_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>VARILLAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[VARILLAS]' id='VARILLAS_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='VARILLAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[VARILLAS]' id='VARILLAS_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='VARILLAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[VARILLAS]' id='VARILLAS_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='VARILLAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>RESORTES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[RESORTES]' id='RESORTES_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='RESORTES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[RESORTES]' id='RESORTES_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='RESORTES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[RESORTES]' id='RESORTES_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='RESORTES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>PUNTERIAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[PUNTERIAS]' id='PUNTERIAS_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='PUNTERIAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PUNTERIAS]' id='PUNTERIAS_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='PUNTERIAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PUNTERIAS]' id='PUNTERIAS_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='PUNTERIAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CIGUEÑAL</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CIGUEÑAL]' id='CIGUEÑAL_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='CIGUEÑAL_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CIGUEÑAL]' id='CIGUEÑAL_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='CIGUEÑAL_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CIGUEÑAL]' id='CIGUEÑAL_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='CIGUEÑAL_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>ARBOL DE ELEVAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[ARBOL DE ELEVAS]' id='ARBOL_DE_ELEVAS_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='ARBOL_DE_ELEVAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ARBOL DE ELEVAS]' id='ARBOL_DE_ELEVAS_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='ARBOL_DE_ELEVAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ARBOL DE ELEVAS]' id='ARBOL_DE_ELEVAS_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='ARBOL_DE_ELEVAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>RETENES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[RETENES]' id='RETENES_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='RETENES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[RETENES]' id='RETENES_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='RETENES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[RETENES]' id='RETENES_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='RETENES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>LIGAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[LIGAS]' id='LIGAS_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='LIGAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[LIGAS]' id='LIGAS_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='LIGAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[LIGAS]' id='LIGAS_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='LIGAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>SENSORES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[SENSORES]' id='SENSORES_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='SENSORES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[SENSORES]' id='SENSORES_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='SENSORES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[SENSORES]' id='SENSORES_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='SENSORES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>POLEAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[POLEAS]' id='POLEAS_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='POLEAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[POLEAS]' id='POLEAS_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='POLEAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[POLEAS]' id='POLEAS_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='POLEAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CONCHA</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CONCHA]' id='CONCHA_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='CONCHA_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CONCHA]' id='CONCHA_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='CONCHA_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CONCHA]' id='CONCHA_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='CONCHA_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CREMAYERA</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CREMAYERA]' id='CREMAYERA_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='CREMAYERA_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CREMAYERA]' id='CREMAYERA_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='CREMAYERA_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CREMAYERA]' id='CREMAYERA_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='CREMAYERA_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CLUTCH</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CLUTCH]' id='CLUTCH_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='CLUTCH_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CLUTCH]' id='CLUTCH_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='CLUTCH_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CLUTCH]' id='CLUTCH_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='CLUTCH_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>COPLES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[COPLES]' id='COPLES_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='COPLES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[COPLES]' id='COPLES_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='COPLES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[COPLES]' id='COPLES_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='COPLES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>BOMBA DE INYECCION</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOMBA DE INYECCION]' id='BOMBA_DE_INYECCION_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='BOMBA_DE_INYECCION_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOMBA DE INYECCION]' id='BOMBA_DE_INYECCION_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='BOMBA_DE_INYECCION_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOMBA DE INYECCION]' id='BOMBA_DE_INYECCION_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='BOMBA_DE_INYECCION_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>JUNTAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[JUNTAS]' id='JUNTAS_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='JUNTAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[JUNTAS]' id='JUNTAS_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='JUNTAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[JUNTAS]' id='JUNTAS_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='JUNTAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>MARCHA</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[MARCHA]' id='MARCHA_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='MARCHA_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[MARCHA]' id='MARCHA_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='MARCHA_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[MARCHA]' id='MARCHA_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='MARCHA_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>TUBERIA</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[TUBERIA]' id='TUBERIA_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='TUBERIA_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TUBERIA]' id='TUBERIA_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='TUBERIA_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TUBERIA]' id='TUBERIA_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='TUBERIA_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>ALTERNADOR</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[ALTERNADOR]' id='ALTERNADOR_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='ALTERNADOR_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ALTERNADOR]' id='ALTERNADOR_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='ALTERNADOR_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ALTERNADOR]' id='ALTERNADOR_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='ALTERNADOR_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>FILTROS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[FILTROS]' id='FILTROS_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='FILTROS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[FILTROS]' id='FILTROS_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='FILTROS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[FILTROS]' id='FILTROS_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='FILTROS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>BASES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[BASES]' id='BASES_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='BASES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BASES]' id='BASES_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='BASES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BASES]' id='BASES_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='BASES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>SOPORTES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[SOPORTES]' id='SOPORTES_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='SOPORTES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[SOPORTES]' id='SOPORTES_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='SOPORTES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[SOPORTES]' id='SOPORTES_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='SOPORTES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>TURBO</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[TURBO]' id='TURBO_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='TURBO_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TURBO]' id='TURBO_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='TURBO_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TURBO]' id='TURBO_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='TURBO_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>ESCAPE</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[ESCAPE]' id='ESCAPE_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='ESCAPE_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ESCAPE]' id='ESCAPE_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='ESCAPE_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ESCAPE]' id='ESCAPE_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='ESCAPE_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CHICOTES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CHICOTES]' id='CHICOTES_bueno' value='bueno' data-seccion='MOTOR' data-peso='1'>
    <label class='btn btn-outline-success' for='CHICOTES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CHICOTES]' id='CHICOTES_regular' value='regular' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-warning' for='CHICOTES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CHICOTES]' id='CHICOTES_malo' value='malo' data-seccion='MOTOR' data-peso='0'>
    <label class='btn btn-outline-danger' for='CHICOTES_malo'>Malo</label>
  </div>
</div>
</div>
<hr><h5>SISTEMA MECANICO</h5>
<div class='progress mb-3'><div class='progress-bar' id='barra_sistema_mecanico' style='width: 0%'>0%</div></div>
<div class='row'>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>TRANSMISION</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[TRANSMISION]' id='TRANSMISION_bueno' value='bueno' data-seccion='SISTEMA MECANICO' data-peso='1'>
    <label class='btn btn-outline-success' for='TRANSMISION_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TRANSMISION]' id='TRANSMISION_regular' value='regular' data-seccion='SISTEMA MECANICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='TRANSMISION_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TRANSMISION]' id='TRANSMISION_malo' value='malo' data-seccion='SISTEMA MECANICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='TRANSMISION_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>DIFERENCIALES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[DIFERENCIALES]' id='DIFERENCIALES_bueno' value='bueno' data-seccion='SISTEMA MECANICO' data-peso='1'>
    <label class='btn btn-outline-success' for='DIFERENCIALES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[DIFERENCIALES]' id='DIFERENCIALES_regular' value='regular' data-seccion='SISTEMA MECANICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='DIFERENCIALES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[DIFERENCIALES]' id='DIFERENCIALES_malo' value='malo' data-seccion='SISTEMA MECANICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='DIFERENCIALES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CARDAN</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CARDAN]' id='CARDAN_bueno' value='bueno' data-seccion='SISTEMA MECANICO' data-peso='1'>
    <label class='btn btn-outline-success' for='CARDAN_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CARDAN]' id='CARDAN_regular' value='regular' data-seccion='SISTEMA MECANICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='CARDAN_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CARDAN]' id='CARDAN_malo' value='malo' data-seccion='SISTEMA MECANICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='CARDAN_malo'>Malo</label>
  </div>
</div>
</div>
<hr><h5>SISTEMA HIDRAULICO</h5>
<div class='progress mb-3'><div class='progress-bar' id='barra_sistema_hidraulico' style='width: 0%'>0%</div></div>
<div class='row'>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>BANCO DE VALVULAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[BANCO DE VALVULAS]' id='BANCO_DE_VALVULAS_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='BANCO_DE_VALVULAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BANCO DE VALVULAS]' id='BANCO_DE_VALVULAS_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='BANCO_DE_VALVULAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BANCO DE VALVULAS]' id='BANCO_DE_VALVULAS_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='BANCO_DE_VALVULAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>BOMBAS DE TRANSITO</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOMBAS DE TRANSITO]' id='BOMBAS_DE_TRANSITO_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='BOMBAS_DE_TRANSITO_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOMBAS DE TRANSITO]' id='BOMBAS_DE_TRANSITO_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='BOMBAS_DE_TRANSITO_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOMBAS DE TRANSITO]' id='BOMBAS_DE_TRANSITO_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='BOMBAS_DE_TRANSITO_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>BOMBAS DE PRECARGA</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOMBAS DE PRECARGA]' id='BOMBAS_DE_PRECARGA_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='BOMBAS_DE_PRECARGA_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOMBAS DE PRECARGA]' id='BOMBAS_DE_PRECARGA_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='BOMBAS_DE_PRECARGA_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOMBAS DE PRECARGA]' id='BOMBAS_DE_PRECARGA_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='BOMBAS_DE_PRECARGA_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>BOMBAS DE ACCESORIOS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOMBAS DE ACCESORIOS]' id='BOMBAS_DE_ACCESORIOS_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='BOMBAS_DE_ACCESORIOS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOMBAS DE ACCESORIOS]' id='BOMBAS_DE_ACCESORIOS_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='BOMBAS_DE_ACCESORIOS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOMBAS DE ACCESORIOS]' id='BOMBAS_DE_ACCESORIOS_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='BOMBAS_DE_ACCESORIOS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CLUTCH HIDRAULICO</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CLUTCH HIDRAULICO]' id='CLUTCH_HIDRAULICO_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='CLUTCH_HIDRAULICO_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CLUTCH HIDRAULICO]' id='CLUTCH_HIDRAULICO_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='CLUTCH_HIDRAULICO_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CLUTCH HIDRAULICO]' id='CLUTCH_HIDRAULICO_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='CLUTCH_HIDRAULICO_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>GATOS DE LEVANTE</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[GATOS DE LEVANTE]' id='GATOS_DE_LEVANTE_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='GATOS_DE_LEVANTE_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[GATOS DE LEVANTE]' id='GATOS_DE_LEVANTE_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='GATOS_DE_LEVANTE_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[GATOS DE LEVANTE]' id='GATOS_DE_LEVANTE_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='GATOS_DE_LEVANTE_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>GATOS DE DIRECCION</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[GATOS DE DIRECCION]' id='GATOS_DE_DIRECCION_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='GATOS_DE_DIRECCION_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[GATOS DE DIRECCION]' id='GATOS_DE_DIRECCION_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='GATOS_DE_DIRECCION_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[GATOS DE DIRECCION]' id='GATOS_DE_DIRECCION_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='GATOS_DE_DIRECCION_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>GATOS DE ACCESORIOS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[GATOS DE ACCESORIOS]' id='GATOS_DE_ACCESORIOS_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='GATOS_DE_ACCESORIOS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[GATOS DE ACCESORIOS]' id='GATOS_DE_ACCESORIOS_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='GATOS_DE_ACCESORIOS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[GATOS DE ACCESORIOS]' id='GATOS_DE_ACCESORIOS_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='GATOS_DE_ACCESORIOS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>MANGUERAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[MANGUERAS]' id='MANGUERAS_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='MANGUERAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[MANGUERAS]' id='MANGUERAS_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='MANGUERAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[MANGUERAS]' id='MANGUERAS_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='MANGUERAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>MOTORES HIDRAULICOS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[MOTORES HIDRAULICOS]' id='MOTORES_HIDRAULICOS_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='MOTORES_HIDRAULICOS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[MOTORES HIDRAULICOS]' id='MOTORES_HIDRAULICOS_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='MOTORES_HIDRAULICOS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[MOTORES HIDRAULICOS]' id='MOTORES_HIDRAULICOS_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='MOTORES_HIDRAULICOS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>ORBITROL</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[ORBITROL]' id='ORBITROL_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='ORBITROL_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ORBITROL]' id='ORBITROL_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='ORBITROL_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ORBITROL]' id='ORBITROL_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='ORBITROL_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>TORQUES HUV (SATELITES)</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[TORQUES HUV (SATELITES)]' id='TORQUES_HUV_SATELITES_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='TORQUES_HUV_SATELITES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TORQUES HUV (SATELITES)]' id='TORQUES_HUV_SATELITES_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='TORQUES_HUV_SATELITES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TORQUES HUV (SATELITES)]' id='TORQUES_HUV_SATELITES_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='TORQUES_HUV_SATELITES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>VALVULAS DE RETENCION</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[VALVULAS DE RETENCION]' id='VALVULAS_DE_RETENCION_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='VALVULAS_DE_RETENCION_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[VALVULAS DE RETENCION]' id='VALVULAS_DE_RETENCION_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='VALVULAS_DE_RETENCION_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[VALVULAS DE RETENCION]' id='VALVULAS_DE_RETENCION_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='VALVULAS_DE_RETENCION_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>REDUCTORES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[REDUCTORES]' id='REDUCTORES_bueno' value='bueno' data-seccion='SISTEMA HIDRAULICO' data-peso='1'>
    <label class='btn btn-outline-success' for='REDUCTORES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[REDUCTORES]' id='REDUCTORES_regular' value='regular' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='REDUCTORES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[REDUCTORES]' id='REDUCTORES_malo' value='malo' data-seccion='SISTEMA HIDRAULICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='REDUCTORES_malo'>Malo</label>
  </div>
</div>
</div>
<hr><h5>SISTEMA ELECTRICO Y ELECTRONICO</h5>
<div class='progress mb-3'><div class='progress-bar' id='barra_sistema_electrico_y_electronico' style='width: 0%'>0%</div></div>
<div class='row'>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>ALARMAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[ALARMAS]' id='ALARMAS_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='ALARMAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ALARMAS]' id='ALARMAS_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='ALARMAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ALARMAS]' id='ALARMAS_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='ALARMAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>ARNESES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[ARNESES]' id='ARNESES_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='ARNESES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ARNESES]' id='ARNESES_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='ARNESES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ARNESES]' id='ARNESES_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='ARNESES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>BOBINAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOBINAS]' id='BOBINAS_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='BOBINAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOBINAS]' id='BOBINAS_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='BOBINAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOBINAS]' id='BOBINAS_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='BOBINAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>BOTONES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOTONES]' id='BOTONES_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='BOTONES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOTONES]' id='BOTONES_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='BOTONES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BOTONES]' id='BOTONES_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='BOTONES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CABLES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CABLES]' id='CABLES_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='CABLES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CABLES]' id='CABLES_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='CABLES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CABLES]' id='CABLES_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='CABLES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CABLES DE SENSORES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CABLES DE SENSORES]' id='CABLES_DE_SENSORES_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='CABLES_DE_SENSORES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CABLES DE SENSORES]' id='CABLES_DE_SENSORES_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='CABLES_DE_SENSORES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CABLES DE SENSORES]' id='CABLES_DE_SENSORES_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='CABLES_DE_SENSORES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CONECTORES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CONECTORES]' id='CONECTORES_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='CONECTORES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CONECTORES]' id='CONECTORES_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='CONECTORES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CONECTORES]' id='CONECTORES_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='CONECTORES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>ELECTRO VALVULAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[ELECTRO VALVULAS]' id='ELECTRO_VALVULAS_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='ELECTRO_VALVULAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ELECTRO VALVULAS]' id='ELECTRO_VALVULAS_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='ELECTRO_VALVULAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ELECTRO VALVULAS]' id='ELECTRO_VALVULAS_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='ELECTRO_VALVULAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>FUSIBLES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[FUSIBLES]' id='FUSIBLES_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='FUSIBLES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[FUSIBLES]' id='FUSIBLES_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='FUSIBLES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[FUSIBLES]' id='FUSIBLES_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='FUSIBLES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>PORTA FUSIBLES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[PORTA FUSIBLES]' id='PORTA_FUSIBLES_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='PORTA_FUSIBLES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PORTA FUSIBLES]' id='PORTA_FUSIBLES_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='PORTA_FUSIBLES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PORTA FUSIBLES]' id='PORTA_FUSIBLES_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='PORTA_FUSIBLES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>INDICADORES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[INDICADORES]' id='INDICADORES_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='INDICADORES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[INDICADORES]' id='INDICADORES_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='INDICADORES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[INDICADORES]' id='INDICADORES_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='INDICADORES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>PRESION/AGUA/TEMPERATURA/VOLTIMETRO</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[PRESION/AGUA/TEMPERATURA/VOLTIMETRO]' id='PRESIONAGUATEMPERATURAVOLTIMETRO_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='PRESIONAGUATEMPERATURAVOLTIMETRO_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PRESION/AGUA/TEMPERATURA/VOLTIMETRO]' id='PRESIONAGUATEMPERATURAVOLTIMETRO_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='PRESIONAGUATEMPERATURAVOLTIMETRO_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PRESION/AGUA/TEMPERATURA/VOLTIMETRO]' id='PRESIONAGUATEMPERATURAVOLTIMETRO_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='PRESIONAGUATEMPERATURAVOLTIMETRO_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>LUCES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[LUCES]' id='LUCES_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='LUCES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[LUCES]' id='LUCES_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='LUCES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[LUCES]' id='LUCES_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='LUCES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>MODULOS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[MODULOS]' id='MODULOS_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='MODULOS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[MODULOS]' id='MODULOS_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='MODULOS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[MODULOS]' id='MODULOS_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='MODULOS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>TORRETA</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[TORRETA]' id='TORRETA_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='TORRETA_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TORRETA]' id='TORRETA_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='TORRETA_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TORRETA]' id='TORRETA_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='TORRETA_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>RELEVADORES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[RELEVADORES]' id='RELEVADORES_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='RELEVADORES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[RELEVADORES]' id='RELEVADORES_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='RELEVADORES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[RELEVADORES]' id='RELEVADORES_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='RELEVADORES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>SWITCH (LLAVE)</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[SWITCH (LLAVE)]' id='SWITCH_LLAVE_bueno' value='bueno' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='1'>
    <label class='btn btn-outline-success' for='SWITCH_LLAVE_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[SWITCH (LLAVE)]' id='SWITCH_LLAVE_regular' value='regular' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='SWITCH_LLAVE_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[SWITCH (LLAVE)]' id='SWITCH_LLAVE_malo' value='malo' data-seccion='SISTEMA ELECTRICO Y ELECTRONICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='SWITCH_LLAVE_malo'>Malo</label>
  </div>
</div>
</div>
<hr><h5>ESTETICO</h5>
<div class='progress mb-3'><div class='progress-bar' id='barra_estetico' style='width: 0%'>0%</div></div>
<div class='row'>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>PINTURA</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[PINTURA]' id='PINTURA_bueno' value='bueno' data-seccion='ESTETICO' data-peso='1'>
    <label class='btn btn-outline-success' for='PINTURA_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PINTURA]' id='PINTURA_regular' value='regular' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='PINTURA_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PINTURA]' id='PINTURA_malo' value='malo' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='PINTURA_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CALCOMANIAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CALCOMANIAS]' id='CALCOMANIAS_bueno' value='bueno' data-seccion='ESTETICO' data-peso='1'>
    <label class='btn btn-outline-success' for='CALCOMANIAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CALCOMANIAS]' id='CALCOMANIAS_regular' value='regular' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='CALCOMANIAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CALCOMANIAS]' id='CALCOMANIAS_malo' value='malo' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='CALCOMANIAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>ASIENTO</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[ASIENTO]' id='ASIENTO_bueno' value='bueno' data-seccion='ESTETICO' data-peso='1'>
    <label class='btn btn-outline-success' for='ASIENTO_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ASIENTO]' id='ASIENTO_regular' value='regular' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='ASIENTO_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ASIENTO]' id='ASIENTO_malo' value='malo' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='ASIENTO_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>TAPICERIA</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[TAPICERIA]' id='TAPICERIA_bueno' value='bueno' data-seccion='ESTETICO' data-peso='1'>
    <label class='btn btn-outline-success' for='TAPICERIA_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TAPICERIA]' id='TAPICERIA_regular' value='regular' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='TAPICERIA_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TAPICERIA]' id='TAPICERIA_malo' value='malo' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='TAPICERIA_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>TOLVAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[TOLVAS]' id='TOLVAS_bueno' value='bueno' data-seccion='ESTETICO' data-peso='1'>
    <label class='btn btn-outline-success' for='TOLVAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TOLVAS]' id='TOLVAS_regular' value='regular' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='TOLVAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[TOLVAS]' id='TOLVAS_malo' value='malo' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='TOLVAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CRISTALES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CRISTALES]' id='CRISTALES_bueno' value='bueno' data-seccion='ESTETICO' data-peso='1'>
    <label class='btn btn-outline-success' for='CRISTALES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CRISTALES]' id='CRISTALES_regular' value='regular' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='CRISTALES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CRISTALES]' id='CRISTALES_malo' value='malo' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='CRISTALES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>ACCESORIOS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[ACCESORIOS]' id='ACCESORIOS_bueno' value='bueno' data-seccion='ESTETICO' data-peso='1'>
    <label class='btn btn-outline-success' for='ACCESORIOS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ACCESORIOS]' id='ACCESORIOS_regular' value='regular' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='ACCESORIOS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[ACCESORIOS]' id='ACCESORIOS_malo' value='malo' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='ACCESORIOS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>SISTEMA DE RIEGO</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[SISTEMA DE RIEGO]' id='SISTEMA_DE_RIEGO_bueno' value='bueno' data-seccion='ESTETICO' data-peso='1'>
    <label class='btn btn-outline-success' for='SISTEMA_DE_RIEGO_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[SISTEMA DE RIEGO]' id='SISTEMA_DE_RIEGO_regular' value='regular' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-warning' for='SISTEMA_DE_RIEGO_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[SISTEMA DE RIEGO]' id='SISTEMA_DE_RIEGO_malo' value='malo' data-seccion='ESTETICO' data-peso='0'>
    <label class='btn btn-outline-danger' for='SISTEMA_DE_RIEGO_malo'>Malo</label>
  </div>
</div>
</div>
<hr><h5>CONSUMIBLES</h5>
<div class='progress mb-3'><div class='progress-bar' id='barra_consumibles' style='width: 0%'>0%</div></div>
<div class='row'>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>PUNTAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[PUNTAS]' id='PUNTAS_bueno' value='bueno' data-seccion='CONSUMIBLES' data-peso='1'>
    <label class='btn btn-outline-success' for='PUNTAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PUNTAS]' id='PUNTAS_regular' value='regular' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-warning' for='PUNTAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PUNTAS]' id='PUNTAS_malo' value='malo' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-danger' for='PUNTAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>PORTA PUNTAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[PORTA PUNTAS]' id='PORTA_PUNTAS_bueno' value='bueno' data-seccion='CONSUMIBLES' data-peso='1'>
    <label class='btn btn-outline-success' for='PORTA_PUNTAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PORTA PUNTAS]' id='PORTA_PUNTAS_regular' value='regular' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-warning' for='PORTA_PUNTAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[PORTA PUNTAS]' id='PORTA_PUNTAS_malo' value='malo' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-danger' for='PORTA_PUNTAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>GARRAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[GARRAS]' id='GARRAS_bueno' value='bueno' data-seccion='CONSUMIBLES' data-peso='1'>
    <label class='btn btn-outline-success' for='GARRAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[GARRAS]' id='GARRAS_regular' value='regular' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-warning' for='GARRAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[GARRAS]' id='GARRAS_malo' value='malo' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-danger' for='GARRAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CUCHILLAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CUCHILLAS]' id='CUCHILLAS_bueno' value='bueno' data-seccion='CONSUMIBLES' data-peso='1'>
    <label class='btn btn-outline-success' for='CUCHILLAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CUCHILLAS]' id='CUCHILLAS_regular' value='regular' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-warning' for='CUCHILLAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CUCHILLAS]' id='CUCHILLAS_malo' value='malo' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-danger' for='CUCHILLAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>CEPILLOS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[CEPILLOS]' id='CEPILLOS_bueno' value='bueno' data-seccion='CONSUMIBLES' data-peso='1'>
    <label class='btn btn-outline-success' for='CEPILLOS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CEPILLOS]' id='CEPILLOS_regular' value='regular' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-warning' for='CEPILLOS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[CEPILLOS]' id='CEPILLOS_malo' value='malo' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-danger' for='CEPILLOS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>SEPARADORES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[SEPARADORES]' id='SEPARADORES_bueno' value='bueno' data-seccion='CONSUMIBLES' data-peso='1'>
    <label class='btn btn-outline-success' for='SEPARADORES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[SEPARADORES]' id='SEPARADORES_regular' value='regular' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-warning' for='SEPARADORES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[SEPARADORES]' id='SEPARADORES_malo' value='malo' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-danger' for='SEPARADORES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>LLANTAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[LLANTAS]' id='LLANTAS_bueno' value='bueno' data-seccion='CONSUMIBLES' data-peso='1'>
    <label class='btn btn-outline-success' for='LLANTAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[LLANTAS]' id='LLANTAS_regular' value='regular' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-warning' for='LLANTAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[LLANTAS]' id='LLANTAS_malo' value='malo' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-danger' for='LLANTAS_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>RINES</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[RINES]' id='RINES_bueno' value='bueno' data-seccion='CONSUMIBLES' data-peso='1'>
    <label class='btn btn-outline-success' for='RINES_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[RINES]' id='RINES_regular' value='regular' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-warning' for='RINES_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[RINES]' id='RINES_malo' value='malo' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-danger' for='RINES_malo'>Malo</label>
  </div>
</div>
<div class='col-md-6 mb-3'>
  <label class='form-label fw-bold text-warning'>BANDAS / ORUGAS</label><br>
  <div class='btn-group' role='group'>
    <input type='radio' class='btn-check componente-radio' name='componentes[BANDAS / ORUGAS]' id='BANDAS__ORUGAS_bueno' value='bueno' data-seccion='CONSUMIBLES' data-peso='1'>
    <label class='btn btn-outline-success' for='BANDAS__ORUGAS_bueno'>Bueno</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BANDAS / ORUGAS]' id='BANDAS__ORUGAS_regular' value='regular' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-warning' for='BANDAS__ORUGAS_regular'>Regular</label>
    <input type='radio' class='btn-check componente-radio' name='componentes[BANDAS / ORUGAS]' id='BANDAS__ORUGAS_malo' value='malo' data-seccion='CONSUMIBLES' data-peso='0'>
    <label class='btn btn-outline-danger' for='BANDAS__ORUGAS_malo'>Malo</label>
  </div>
</div>
</div>

<div class="mb-3">
  <label class="form-label">Observaciones</label>
  <textarea name="observaciones" class="form-control" rows="3"></textarea>
</div>

<div class="text-center mt-4">
  <h5 class="text-warning">Condición Total Estimada: <span id="total_condicion">0%</span></h5>
</div>

<div class="text-center mt-3">
  <button type="submit" class="btn btn-warning px-5 py-2">Guardar</button>
  <button type="button" onclick="window.print()" class="btn btn-primary px-4 py-2 ms-2">Imprimir</button>
</div>
</form>
</div>

<script>
  function actualizarAvance() {
    const pesos = {
      "MOTOR": 15,
      "SISTEMA MECANICO": 15,
      "SISTEMA HIDRAULICO": 30,
      "SISTEMA ELECTRICO Y ELECTRONICO": 25,
      "ESTETICO": 5,
      "CONSUMIBLES": 10
    };
    const avanceSeccion = {};
    document.querySelectorAll('.componente-radio:checked').forEach(radio => {
      if (radio.value === "bueno") {
        const seccion = radio.dataset.seccion;
        const peso = parseFloat(radio.dataset.peso || 0);
        if (!avanceSeccion[seccion]) avanceSeccion[seccion] = 0;
        avanceSeccion[seccion] += peso;
      }
    });
    for (const seccion in pesos) {
      const total = pesos[seccion];
      const actual = avanceSeccion[seccion] || 0;
      const barra = document.getElementById("barra_" + seccion.toLowerCase().replace(/ /g, "_"));
      const porcentaje = (actual / total * 100).toFixed(2);
      if (barra) {
        barra.style.width = porcentaje + "%";
        barra.innerText = actual.toFixed(2) + "%";
      }
    }
    const total = Object.values(avanceSeccion).reduce((a, b) => a + b, 0).toFixed(2);
    document.getElementById("total_condicion").innerText = total + "%";
  }

  document.querySelectorAll(".componente-radio").forEach(r => r.addEventListener("change", actualizarAvance));
  window.addEventListener("DOMContentLoaded", actualizarAvance);
</script>
</body>
</html>