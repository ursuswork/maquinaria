<?php
session_start();
include 'conexion.php';

$usuario = $_POST['usuario'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ? AND password = ?");
$stmt->bind_param("ss", $usuario, $password);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    $_SESSION['usuario'] = $fila['usuario'];
    $_SESSION['rol'] = $fila['rol']; // ¡¡IMPORTANTE!! Guarda el rol real aquí
    header("Location: inventario.php");
    exit;
} else {
    header("Location: login.php?error=1");
    exit;
}
?>
