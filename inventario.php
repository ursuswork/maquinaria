<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$sql = "SELECT * FROM maquinaria";
if (!empty($busqueda)) {
  $sql .= " WHERE nombre LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%' OR numero_serie LIKE '%$busqueda%'";
}
$sql .= " ORDER BY tipo_maquinaria ASC, nombre ASC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario de Maquinaria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .etiqueta-nueva { background-color: #2525ddff; color: white; padding: 2px 8px; border-radius: 5px; font-size: 12px; }
    .card-maquinaria {
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 15px;
      overflow: hidden;
    }
    .progress-bar { font-weight: bold; }
  </style>
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h3 class="text-primary">Inventario de Maquinaria</h3>
    <a href="agregar_maquinaria.php" class="btn btn-success">+ Agregar Maquinaria</a>
  </div>
  <form class="mb-4" method="GET">
    <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, modelo o número de serie" value="<?=htmlspecialchars($busqueda)?>">
  </form>
  <div class="row">
    <?php while ($fila = $resultado->fetch_assoc()): ?>
      <div class="col-md-4 mb-4">
        <div class="card card-maquinaria p-3">
          <?php if (!empty($fila['imagen'])): ?>
            <img src="imagenes/<?= $fila['imagen'] ?>" class="img-fluid rounded mb-2" style="max-height:200px; object-fit:contain;">
          <?php endif; ?>
          <h5><?= htmlspecialchars($fila['nombre']) ?></h5>
          <p class="mb-1"><strong>Modelo:</strong> <?= htmlspecialchars($fila['modelo']) ?></p>
          <p class="mb-1"><strong>Ubicación:</strong> <?= htmlspecialchars($fila['ubicacion']) ?></p>
          <p class="mb-1"><strong>Tipo:</strong> 
            <?= htmlspecialchars($fila['tipo_maquinaria']) ?>
            <?php if ($fila['tipo_maquinaria'] == 'nueva'): ?>
              <span class="etiqueta-nueva">Nueva</span>
            <?php endif; ?>
          </p>
          <?php if (!empty($fila['subtipo'])): ?>
            <p class="mb-1"><strong>Subtipo:</strong> <?= htmlspecialchars($fila['subtipo']) ?></p>
          <?php endif; ?>
          <?php if (!is_null($fila['condicion_estimada'])): ?>
            <div class="progress mb-2" style="height: 25px;">
              <div class="progress-bar <?= $fila['condicion_estimada'] >= 85 ? 'bg-success' : ($fila['condicion_estimada'] >= 60 ? 'bg-warning' : 'bg-danger') ?>" 
                   style="width: <?= $fila['condicion_estimada'] ?>%;">
                <?= $fila['condicion_estimada'] ?>%
              </div>
            </div>
          <?php endif; ?>
          <div class="d-flex justify-content-between">
            <a href="editar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary">✏️ Editar</a>
            <a href="eliminar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta maquinaria?')">🗑️ Eliminar</a>
          </div>
          <?php if ($fila['tipo_maquinaria'] == 'usada'): ?>
            <a href="acciones/recibo_unidad.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-secondary mt-2 w-100">📋 Recibo de Unidad</a>
          <?php endif; ?>
          <?php if ($fila['tipo_maquinaria'] == 'nueva' && $fila['subtipo'] == 'esparcidor de sello'): ?>
            <a href="avance_esparcidor.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-success mt-2 w-100">🛠️ Ver Avance</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>
</body>
</html>
