<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Maquinaria</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-4">
    <h4>Agregar Maquinaria</h4>
    <form action="procesar_agregar.php" method="POST" enctype="multipart/form-data">
      <input type="text" name="nombre" placeholder="Nombre" class="form-control mb-2" required>
      <select name="tipo" class="form-control mb-2" required>
        <option value="">Selecciona tipo</option>
        <option value="nueva">Nueva</option>
        <option value="usada">Usada</option>
      </select>
      <input type="text" name="modelo" placeholder="Modelo" class="form-control mb-2">
      <input type="text" name="numero_serie" placeholder="Número de serie" class="form-control mb-2">
      <input type="text" name="ubicacion" placeholder="Ubicación" class="form-control mb-2">
      <input type="file" name="imagen" accept="image/*" class="form-control mb-2" required>
      <button type="submit" class="btn btn-primary w-100">Guardar</button>
    </form>
  </div>
</body>
</html>