<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['usuario'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($usuario && $password) {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ? AND password = ?");
        if (!$stmt) {
            die("❌ Error al preparar la consulta: " . $conn->error);
        }

        $stmt->bind_param("ss", $usuario, $password);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $_SESSION['usuario'] = $usuario;
            header("Location: inventario.php");
            exit;
        } else {
            header("Location: index.php?error=1");
            exit;
        }
    } else {
        header("Location: index.php?error=1");
        exit;
    }
} else {
    echo "❌ Acceso no permitido.";
}
?>
