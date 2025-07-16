<?php
session_start();
include 'conexion.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = hash('sha256', $_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $_SESSION['login'] = true;
        header("Location: inventario.php");
        exit();
    } else {
        $error = "❌ Usuario o contraseña incorrectos.";
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
        body {
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .form-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-primary {
            border-radius: 10px;
            font-weight: bold;
        }
        .error-msg {
            color: red;
            text-align: center;
            margin-top: 10px;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="form-title">🔒 Iniciar Sesión</div>

    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Usuario:</label>
            <input type="text" class="form-control" id="username" name="username" required autofocus>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Entrar</button>
        </div>

        <?php if (!empty($error)) : ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>
    </form>
</div>

</body>
</html>
