<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) {
  die("ID de maquinaria inválido");
}

// Recolectar componentes y observaciones
$componentes = $_POST['componentes'] ?? [];
$observaciones = $conn->real_escape_string($_POST['observaciones'] ?? '');
$fecha_llenado = date('Y-m-d');

// Si ya existe un recibo, lo actualizamos. Si no, lo insertamos.
$existe = $conn->query("SELECT id_maquinaria FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria")->num_rows > 0;

// Construir columnas y valores dinámicamente
$columnas = "observaciones = '$observaciones', fecha_llenado = '$fecha_llenado'";
foreach ($componentes as $nombre => $valor) {
  $col = $conn->real_escape_string($nombre);
  $val = $conn->real_escape_string($valor);
  $columnas .= ", `$col` = '$val'";
}

if ($existe) {
  $sql = "UPDATE recibo_unidad SET $columnas WHERE id_maquinaria = $id_maquinaria";
} else {
  $nombres_cols = "id_maquinaria, observaciones, fecha_llenado";
  $valores = "$id_maquinaria, '$observaciones', '$fecha_llenado'";
  foreach ($componentes as $nombre => $valor) {
    $col = $conn->real_escape_string($nombre);
    $val = $conn->real_escape_string($valor);
    $nombres_cols .= ", `$col`";
    $valores .= ", '$val'";
  }
  $sql = "INSERT INTO recibo_unidad ($nombres_cols) VALUES ($valores)";
}

if (!$conn->query($sql)) {
  die("Error al guardar los datos: " . $conn->error);
}

// Cálculo de condición estimada
$pesos = [
  "MOTOR" => 15,
  "SISTEMA MECANICO" => 15,
  "SISTEMA HIDRAULICO" => 30,
  "SISTEMA ELECTRICO Y ELECTRONICO" => 25,
  "ESTETICO" => 5,
  "CONSUMIBLES" => 10
];

$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGUEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECANICO" => ["TRANSMISION", "DIFERENCIALES", "CARDAN"],
  "SISTEMA HIDRAULICO" => ["BANCO DE VALVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRAULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCION", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRAULICOS", "ORBITROL", "TORQUES HUV (SATELITES)", "VALVULAS DE RETENCION", "REDUCTORES"],
  "SISTEMA ELECTRICO Y ELECTRONICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VALVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESION/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MODULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)"],
  "ESTETICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
  "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
];

$total = 0;
foreach ($secciones as $nombre_seccion => $componentes_seccion) {
  $peso = $pesos[$nombre_seccion];
  $bueno = 0;
  foreach ($componentes_seccion as $comp) {
    if (($componentes[$comp] ?? '') === 'bueno') {
      $bueno++;
    }
  }
  $porcentaje_seccion = count($componentes_seccion) > 0 ? ($bueno / count($componentes_seccion)) * $peso : 0;
  $total += $porcentaje_seccion;
}

$condicion_total = round($total, 2);
$conn->query("UPDATE maquinaria SET condicion_estimada = $condicion_total WHERE id = $id_maquinaria");

header("Location: ../inventario.php");
exit;