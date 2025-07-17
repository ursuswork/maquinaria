<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}
?>
<!-- Aquí continúa el contenido original de inventario.php -->
