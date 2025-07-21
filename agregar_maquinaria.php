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
  
  <link rel="stylesheet" href="estilos_colores.css">
</head>
<body class="bg-dark text-white">

  <div class="contenedor-formulario">
    <h4 class="text-center mb-4 text-primary">Agregar Maquinaria</h4>
    <form action="procesar_agregar.php" method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Modelo</label>
        <input type="text" name="modelo" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Ubicación</label>
        <input type="text" name="ubicacion" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Tipo</label>
        <select name="tipo_maquinaria" class="form-select" id="tipo_maquinaria" onchange="mostrarSubtipo()" required>
          <option value="">Seleccionar</option>
          <option value="nueva">Nueva</option>
          <option value="usada">Usada</option>
        </select>
      </div>

      <div class="mb-3" id="subtipo_contenedor" style="display: none;">
        <label class="form-label">Subtipo</label>
        <select name="subtipo" class="form-select">
          <option value="Petrolizadora">Petrolizadora</option>
          <option value="Esparcidor de sello">Esparcidor de sello</option>
          <option value="Tanque de almacén">Tanque de almacén</option>
          <option value="Bachadora">Bachadora</option>
          <option value="Planta de mezcla en frío">Planta de mezcla en frío</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Imagen</label>
        <input type="file" name="imagen" class="form-control" accept="image/*">
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-success">Agregar Maquinaria</button>
      </div>
    </form>
  </div>

  <script>
    function mostrarSubtipo() {
      const tipo = document.getElementById('tipo_maquinaria').value;
      const subtipo = document.getElementById('subtipo_contenedor');
      subtipo.style.display = (tipo === 'nueva') ? 'block' : 'none';
    }
  </script>

</body>
</html>

