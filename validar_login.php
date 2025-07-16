<?php
session_start();
include 'conexion.php';

$usuario = $_POST['usuario'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ? AND password = SHA2(?, 256)");
$stmt->bind_param("ss", $usuario, $password);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
  $_SESSION['usuario'] = $usuario;
  header("Location: index.php");
} else {
  echo "<script>alert('Usuario o contraseña incorrectos');window.location='login.php';</script>";
}
?>
