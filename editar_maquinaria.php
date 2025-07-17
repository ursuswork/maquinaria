<?php
include 'conexion.php';
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}

$id = $_GET['id'];
$query = $conn->prepare("SELECT * FROM maquinaria WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$resultado = $query->get_result();
$row = $resultado->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nombre = $_POST['nombre'];
  $modelo = $_POST['modelo'];
  $ubicacion = $_POST['ubicacion'];
  $tipo_maquinaria = $_POST['tipo_maquinaria'];
  $condicion = $_POST['condicion_estimada'];

  if ($_FILES['imagen']['name']) {
    $imagen = uniqid() . "-" . basename($_FILES['imagen']['name']);
    $ruta = "imagenes/" . $imagen;
    move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
  } else {
    $imagen = $row['imagen'];
  }

  $stmt = $conn->prepare("UPDATE maquinaria SET nombre=?, modelo=?, ubicacion=?, tipo_maquinaria=?, condicion_estimada=?, imagen=? WHERE id=?");
  $stmt->bind_param("sssssisi", $nombre, $modelo, $ubicacion, $tipo_maquinaria, $condicion, $imagen, $id);
  $stmt->execute();

  header("Location: inventario.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Maquinaria</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4 text-primary">Editar Maquinaria</h2>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" value="<?= $row['nombre'] ?>" required></div>
    <div class="mb-3"><label class="form-label">Modelo</label><input type="text" name="modelo" class="form-control" value="<?= $row['modelo'] ?>" required></div>
    <div class="mb-3"><label class="form-label">Ubicación</label><input type="text" name="ubicacion" class="form-control" value="<?= $row['ubicacion'] ?>" required></div>
    <div class="mb-3">
      <label class="form-label">Tipo de Maquinaria</label>
      <select name="tipo_maquinaria" class="form-control" required>
        <option value="nueva" <?= $row['tipo_maquinaria'] == 'nueva' ? 'selected' : '' ?>>Nueva</option>
        <option value="usada" <?= $row['tipo_maquinaria'] == 'usada' ? 'selected' : '' ?>>Usada</option>
      </select>
    </div>
    <div class="mb-3"><label class="form-label">Condición (%)</label><input type="number" name="condicion_estimada" class="form-control" value="<?= $row['condicion_estimada'] ?>" min="0" max="100" required></div>
    <div class="mb-3"><label class="form-label">Imagen</label><input type="file" name="imagen" class="form-control" accept="image/*"></div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="inventario.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>
