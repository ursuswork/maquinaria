<?php
include 'conexion.php';
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nombre = $_POST['nombre'];
  $tipo = $_POST['tipo'];
  $modelo = $_POST['modelo'];
  $ubicacion = $_POST['ubicacion'];
  $tipo_maquinaria = $_POST['tipo_maquinaria'];
  $condicion = $_POST['condicion_estimada'];

  $imagen = $_FILES['imagen']['name'];
  $temp = $_FILES['imagen']['tmp_name'];
  $nombre_archivo = uniqid() . "-" . basename($imagen);
  $ruta = "imagenes/" . $nombre_archivo;

  if (move_uploaded_file($temp, $ruta)) {
    $stmt = $conn->prepare("INSERT INTO maquinaria (nombre, tipo, modelo, ubicacion, tipo_maquinaria, condicion_estimada, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $nombre, $tipo, $modelo, $ubicacion, $tipo_maquinaria, $condicion, $nombre_archivo);
    $stmt->execute();
    header("Location: inventario.php");
    exit;
  } else {
    $error = "Error al subir la imagen.";
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Maquinaria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4 text-primary">Agregar Maquinaria</h2>
  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Tipo</label><input type="text" name="tipo" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Modelo</label><input type="text" name="modelo" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Ubicación</label><input type="text" name="ubicacion" class="form-control" required></div>
    <div class="mb-3">
      <label class="form-label">Tipo de Maquinaria</label>
      <select name="tipo_maquinaria" class="form-control" required>
        <option value="nueva">Nueva</option>
        <option value="usada">Usada</option>
      </select>
    </div>
    <div class="mb-3"><label class="form-label">Condición (%)</label><input type="number" name="condicion_estimada" class="form-control" min="0" max="100" required></div>
    <div class="mb-3"><label class="form-label">Imagen</label><input type="file" name="imagen" class="form-control" accept="image/*" required></div>
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="inventario.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>
