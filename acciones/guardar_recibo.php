<?php
session_start();
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) {
  die("ID inválido.");
}

$empresa_origen = $_POST['empresa_origen'] ?? '';
$empresa_destino = $_POST['empresa_destino'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';
$componentes = $_POST['componentes'] ?? [];

// Verificar si ya existe un recibo para esta maquinaria
$existe = $conn->query("SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria")->num_rows > 0;

// Construir campos dinámicos
$campos = "";
foreach ($componentes as $nombre => $valor) {
  $nombre_seguro = $conn->real_escape_string($nombre);
  $valor_seguro = $conn->real_escape_string($valor);
  $campos .= "`$nombre_seguro` = '$valor_seguro', ";
}
$campos .= "empresa_origen = '" . $conn->real_escape_string($empresa_origen) . "', ";
$campos .= "empresa_destino = '" . $conn->real_escape_string($empresa_destino) . "', ";
$campos .= "observaciones = '" . $conn->real_escape_string($observaciones) . "'";

if ($existe) {
  $sql = "UPDATE recibo_unidad SET $campos WHERE id_maquinaria = $id_maquinaria";
} else {
  $columnas = "`id_maquinaria`, " . implode(", ", array_map(function($k) use ($conn) {
    return "`" . $conn->real_escape_string($k) . "`";
  }, array_keys($componentes))) . ", empresa_origen, empresa_destino, observaciones";

  $valores = "$id_maquinaria, " . implode(", ", array_map(function($v) use ($conn) {
    return "'" . $conn->real_escape_string($v) . "'";
  }, array_values($componentes))) . ", '" . $conn->real_escape_string($empresa_origen) . "', '" . $conn->real_escape_string($empresa_destino) . "', '" . $conn->real_escape_string($observaciones) . "'";

  $sql = "INSERT INTO recibo_unidad ($columnas) VALUES ($valores)";
}

if ($conn->query($sql)) {
  // Calcular condición estimada final
  $secciones = [
    "MOTOR" => 15,
    "SISTEMA MECANICO" => 15,
    "SISTEMA HIDRAULICO" => 30,
    "SISTEMA ELECTRICO Y ELECTRONICO" => 25,
    "ESTETICO" => 5,
    "CONSUMIBLES" => 10
  ];

  $porcentaje_total = 0;

  foreach ($secciones as $nombre => $peso) {
    $consulta = $conn->query("SHOW COLUMNS FROM recibo_unidad LIKE '$nombre%'");
    $total = $consulta->num_rows;
    $buenos = 0;
    while ($col = $consulta->fetch_assoc()) {
      $columna = $col['Field'];
      $valor = $componentes[$columna] ?? '';
      if ($valor === 'bueno') $buenos++;
    }
    if ($total > 0) {
      $porcentaje_total += ($buenos / $total) * $peso;
    }
  }

  $porcentaje_total = round($porcentaje_total);
  $conn->query("UPDATE maquinaria SET condicion_estimada = $porcentaje_total WHERE id = $id_maquinaria");

  header("Location: ../inventario.php?exito=1");
  exit;
} else {
  echo "❌ Error al guardar: " . $conn->error;
}
?>