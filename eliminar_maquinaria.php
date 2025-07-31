<?php
include 'conexion.php';
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}
$usuario = $_SESSION['usuario'] ?? '';
$rol = $_SESSION['rol'] ?? 'consulta';

// Checa permisos de eliminación:
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
  header("Location: inventario.php");
  exit;
}

$tipo = '';
if ($id > 0) {
  $res = $conn->query("SELECT tipo_maquinaria FROM maquinaria WHERE id = $id");
  if ($row = $res->fetch_assoc()) $tipo = strtolower($row['tipo_maquinaria']);
}

// Solo puede jabri, o el rol adecuado según el tipo
$puede_eliminar = false;
if ($usuario === 'jabri') {
  $puede_eliminar = true;
} elseif ($rol == 'produccion' && ($tipo == 'nueva' || $tipo == 'camion')) {
  $puede_eliminar = true;
} elseif ($rol == 'usada' && ($tipo == 'usada' || $tipo == 'camion')) {
  $puede_eliminar = true;
}
if (!$puede_eliminar) {
  header("Location: inventario.php");
  exit;
}

// Procede a borrar
$stmt = $conn->prepare("DELETE FROM maquinaria WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: inventario.php");
exit;
?>

