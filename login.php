
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
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h4 class="text-center mb-4 text-primary">Iniciar Sesión</h4>
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger" role="alert">Usuario o contraseña incorrectos.</div>
    <?php endif; ?>
    <form action="validar_login.php" method="POST">
      <input type="text" name="usuario" class="form-control mb-3" placeholder="Usuario" required>
      <input type="password" name="password" class="form-control mb-3" placeholder="Contraseña" required>
      <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>
  </div>
</body>
</html>
