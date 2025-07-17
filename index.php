<?php
session_start();

// Si el usuario ya inició sesión, lo envías al inventario
if (isset($_SESSION['usuario'])) {
  header("Location: inventario.php");
  exit;
}

// Si no ha iniciado sesión, lo envías al login
header("Location: login.php");
exit;
?>
