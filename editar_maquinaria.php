<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}
include 'conexion.php';
$id = $_GET['id'];
$resultado = $conn->query("SELECT * FROM maquinaria WHERE id = $id");
$datos = $resultado->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Maquinaria</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
  <h4>Editar Maquinaria</h4>
  <form action="procesar_edicion.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $datos['id'] ?>">
    <input type="text" name="nombre" class="form-control mb-2" value="<?= $datos['nombre'] ?>" required>
    <select name="tipo" class="form-control mb-2" required>
      <option value="nueva" <?= $datos['tipo'] == 'nueva' ? 'selected' : '' ?>>Nueva</option>
      <option value="usada" <?= $datos['tipo'] == 'usada' ? 'selected' : '' ?>>Usada</option>
    </select>
    <input type="text" name="modelo" class="form-control mb-2" value="<?= $datos['modelo'] ?>">
    <input type="text" name="numero_serie" class="form-control mb-2" value="<?= $datos['numero_serie'] ?>">
    <input type="text" name="ubicacion" class="form-control mb-2" value="<?= $datos['ubicacion'] ?>">
    <input type="file" name="imagen" class="form-control mb-2">
    <button type="submit" class="btn btn-primary w-100">Actualizar</button>
  </form>
</div>
</body>
</html>