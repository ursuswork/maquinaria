<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if ($usuario === "admin" && hash('sha256', $contrasena) === hash('sha256', 'admin123')) {
        $_SESSION['login'] = true;
        header("Location: inventario.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #e9f1f7; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2 class="text-center mb-4">🔐 Iniciar Sesión</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Usuario</label>
            <input type="text" name="usuario" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" name="contrasena" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Entrar</button>
    </form>
</div>

</body>
</html>
