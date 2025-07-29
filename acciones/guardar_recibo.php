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

// Verificar si ya existe un recibo
$existe = $conn->query("SELECT id_maquinaria FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria")->num_rows > 0;

// Construcción de columnas para INSERT o UPDATE
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
// =====================
// Cálculo de Condición
// =====================
$pesos = [
  "MOTOR" => 15,
  "SISTEMA MECANICO" => 15,
  "SISTEMA HIDRAULICO" => 30,
  "SISTEMA ELECTRICO Y ELECTRONICO" => 25,
  "ESTETICO" => 5,
  "CONSUMIBLES" => 10
];

$secciones = [
  "MOTOR" => ["cilindros", "pistones", "anillos", "inyectores", "block", "cabeza", "varillas", "resortes", "punterias", "cigueñal", "arbol_de_elevas", "retenes", "ligas", "sensores", "poleas", "concha", "cremallera", "clutch", "coples", "bomba_de_inyeccion", "juntas", "marcha", "tuberia", "alternador", "filtros", "bases", "soportes", "turbo", "escape", "chicotes"],
  "SISTEMA MECANICO" => ["transmision", "diferenciales", "cardan"],
  "SISTEMA HIDRAULICO" => ["banco_de_valvulas", "bombas_de_transito", "bombas_de_precarga", "bombas_de_accesorios", "clutch_hidraulico", "gatos_de_levante", "gatos_de_direccion", "gatos_de_accesorios", "mangueras", "motores_hidraulicos", "orbitrol", "torques_huv_satelites", "valvulas_de_retencion", "reductores"],
  "SISTEMA ELECTRICO Y ELECTRONICO" => ["alarmas", "arneses", "bobinas", "botones", "cables", "cables_de_sensores", "conectores", "electro_valvulas", "fusibles", "porta_fusibles", "indicadores", "presion_agua_temperatura_voltimetro", "luces", "modulos", "torreta", "relevadores", "switch_llave"],
  "ESTETICO" => ["pintura", "calcomanias", "asiento", "tapiceria", "tolvas", "cristales", "accesorios", "sistema_de_riego"],
  "CONSUMIBLES" => ["puntas", "porta_puntas", "garras", "cuchillas", "cepillos", "separadores", "llantas", "rines", "bandas_orugas"]
];

$total = 0;
foreach ($secciones as $nombre_seccion => $componentes_seccion) {
  $clave_peso = strtoupper($nombre_seccion);
  $peso = $pesos[$clave_peso] ?? 0;
  $bueno = 0;
  foreach ($componentes_seccion as $comp) {
    if (($componentes[$comp] ?? '') === 'bueno') {
      $bueno++;
    }
  }
  $porcentaje = count($componentes_seccion) > 0 ? ($bueno / count($componentes_seccion)) * $peso : 0;
  $total += $porcentaje;
}

$condicion_total = round($total, 2);
$conn->query("UPDATE maquinaria SET condicion_estimada = $condicion_total WHERE id = $id_maquinaria");


// Redirigir al inventario
header("Location: ../inventario.php?exito=1");
exit;
?>
