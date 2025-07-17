<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
  die("❌ ID inválido.");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id")->fetch_assoc();
if (!$maquinaria) {
  die("❌ Maquinaria no encontrada.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Maquinaria</title>
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
    .contenedor-editar {
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
    img.preview {
      max-height: 150px;
      object-fit: contain;
      margin-bottom: 15px;
      display: block;
    }
  </style>
</head>
<body>

  <div class="contenedor-editar">
    <h4 class="text-center mb-4 text-primary">Editar Maquinaria</h4>
    <form action="procesar_editar.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $maquinaria['id'] ?>">

      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($maquinaria['nombre']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Modelo</label>
        <input type="text" name="modelo" class="form-control" value="<?= htmlspecialchars($maquinaria['modelo']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Ubicación</label>
        <input type="text" name="ubicacion" class="form-control" value="<?= htmlspecialchars($maquinaria['ubicacion']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Tipo</label>
        <select name="tipo_maquinaria" class="form-select" required>
          <option value="nueva" <?= $maquinaria['tipo_maquinaria'] == 'nueva' ? 'selected' : '' ?>>Nueva</option>
          <option value="usada" <?= $maquinaria['tipo_maquinaria'] == 'usada' ? 'selected' : '' ?>>Usada</option>
        </select>
      </div>

      <?php if (!empty($maquinaria['imagen'])): ?>
        <label class="form-label">Imagen actual:</label>
        <img src="imagenes/<?= $maquinaria['imagen'] ?>" class="preview">
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Nueva imagen (opcional)</label>
        <input type="file" name="imagen" class="form-control" accept="image/*">
      </div>

      <div class="d-grid mb-2">
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
      </div>
    </form>
    <a href="inventario.php" class="btn btn-regresar w-100 text-center">← Volver al Inventario</a>
  </div>

</body>
</html>
