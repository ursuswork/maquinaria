<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Maquinaria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h3 class="text-primary mb-4">Agregar Maquinaria</h3>
  <form action="guardar_maquinaria.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label fw-bold">Nombre</label>
      <input type="text" name="nombre" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label fw-bold">Modelo</label>
      <input type="text" name="modelo" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label fw-bold">Ubicación</label>
      <input type="text" name="ubicacion" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label fw-bold">Tipo de Maquinaria</label>
      <select name="tipo_maquinaria" id="tipo_maquinaria" class="form-select" required onchange="mostrarSublista()">
        <option value="">Selecciona tipo</option>
        <option value="nueva">Nueva</option>
        <option value="usada">Usada</option>
      </select>
    </div>

    <div class="mb-3" id="sublista_nueva" style="display: none;">
      <label class="form-label fw-bold">Tipo de maquinaria nueva</label>
      <select name="subtipo_nueva" class="form-select">
        <option value="">Selecciona</option>
        <option value="petrolizadora">Petrolizadora</option>
        <option value="esparcidor de sello">Esparcidor de sello</option>
        <option value="tanque de almacen">Tanque de almacén</option>
        <option value="bachadora">Bachadora</option>
        <option value="planta de mezcla en frio">Planta de mezcla en frío</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label fw-bold">Imagen</label>
      <input type="file" name="imagen" class="form-control">
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
  </form>
</div>

<script>
function mostrarSublista() {
  const tipo = document.getElementById('tipo_maquinaria').value;
  document.getElementById('sublista_nueva').style.display = (tipo === 'nueva') ? 'block' : 'none';
}
</script>
</body>
</html>
