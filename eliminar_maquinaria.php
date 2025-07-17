<?php
include 'conexion.php';
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $stmt = $conn->prepare("DELETE FROM maquinaria WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
}

header("Location: inventario.php");
exit;
?>