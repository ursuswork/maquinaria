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
  <style>
    body {
      background-color: #001f3f; /* Azul marino */
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .contenedor-agregar {
      background-color: white;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 600px;
    }
    .btn-primary {
      background-color: #007bff;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0056b3;
    }
    .btn-regresar {
      background-color: transparent;
      border: 2px solid #007bff;
      color: #007bff;
    }
    .btn-regresar:hover {
      background-color: #007bff;
      color: white;
    }
  </style>
</head>
<body>

  <div class="contenedor-agregar">
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
        <select name="tipo_maquinaria" class="form-select" required>
          <option value="nueva">Nueva</option>
          <option value="usada">Usada</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Imagen</label>
        <input type="file" name="imagen" class="form-control" accept="image/*">
      </div>
      <div class="d-grid mb-2">
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </form>
    <a href="inventario.php" class="btn btn-regresar w-100 text-center">← Volver al Inventario</a>
  </div>

</body>
</html>
