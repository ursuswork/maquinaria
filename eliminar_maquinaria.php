<?php
include 'conexion.php';
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}

// ---- CONTROL DE ROLES ----
$rol = $_SESSION['rol'] ?? 'consulta'; // produccion, usada, consulta

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  // Verifica el tipo de maquinaria antes de borrar
  $row = $conn->query("SELECT tipo_maquinaria FROM maquinaria WHERE id = $id")->fetch_assoc();
  if ($row) {
    $tipo = strtolower($row['tipo_maquinaria']);
    $puede_borrar = false;
    if ($rol == 'produccion' && ($tipo == 'nueva' || $tipo == 'camion')) $puede_borrar = true;
    if ($rol == 'usada' && ($tipo == 'usada' || $tipo == 'camion')) $puede_borrar = true;
    // consulta no puede eliminar nada

    if ($puede_borrar) {
      $stmt = $conn->prepare("DELETE FROM maquinaria WHERE id = ?");
      $stmt->bind_param("i", $id);
      $stmt->execute();
    }
  }
}

header("Location: inventario.php");
exit;
?>
