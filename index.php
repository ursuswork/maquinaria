<?php
session_start();
if (isset($_SESSION['usuario'])) {
  header("Location: inventario.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #001f3f;
      color: #0056b3;
    }
    .card {
      background-color: white;
      border: 1px solid #004080;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }
    .btn-primary {
      background-color: #0056b3;
      border: none;
      font-weight: bold;
    }
    .btn-primary:hover {
      background-color: #004080;
    }
    h4 {
      color: #ffc107;
    }
  </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h4 class="text-center mb-4">Iniciar Sesión</h4>
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger text-center">Usuario o contraseña incorrectos.</div>
    <?php endif; ?>
    <form action="validar_login.php" method="POST">
      <input type="text" name="usuario" class="form-control mb-3" placeholder="Usuario" required>
      <input type="password" name="password" class="form-control mb-3" placeholder="Contraseña" required>
      <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>
  </div>
</body>
</html>
