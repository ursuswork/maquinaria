<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$tipo_filtro = $_GET['tipo'] ?? 'todas';

$sql = "SELECT * FROM maquinaria";
if (!empty($busqueda)) {
  $sql .= " WHERE (nombre LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%' OR numero_serie LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'nueva') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "tipo_maquinaria = 'nueva'";
} elseif ($tipo_filtro === 'usada') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "tipo_maquinaria = 'usada'";
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
    body {
      background-color: #08088eff;
      color: #ffffff;
    }
    .card-maquinaria {
      background-color: #291378ff;
      border: 1px solid #333;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(39, 21, 174, 0.5);
    }
    .btn-primary, .btn-success, .btn-outline-primary, .btn-outline-danger, .btn-outline-secondary, .btn-outline-success {
      border-radius: 10px;
    }
    .progress {
      background-color: #333;
    }
    .progress-bar {
      font-weight: bold;
    }
    .etiqueta-nueva {
      background-color: #007bff;
      color: white;
      padding: 2px 8px;
      border-radius: 5px;
      font-size: 12px;
    }
    .nav-tabs .nav-link.active {
      background-color: #007bff;
      color: white;
    }
    .nav-tabs .nav-link {
      color: #ccc;
    }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h3 class="text-light">Inventario de Maquinaria</h3>
    <a href="agregar_maquinaria.php" class="btn btn-success">+ Agregar Maquinaria</a>
  </div>
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro == 'todas' ? 'active' : '' ?>" href="?tipo=todas">Todas</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro == 'nueva' ? 'active' : '' ?>" href="?tipo=nueva">Nueva</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro == 'usada' ? 'active' : '' ?>" href="?tipo=usada">Usada</a>
    </li>
  </ul>
  <form class="mb-4" method="GET">
    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_filtro) ?>">
    <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, modelo o número de serie" value="<?= htmlspecialchars($busqueda) ?>">
  </form>
  <div class="row">
    <?php while ($fila = $resultado->fetch_assoc()): ?>
      <div class="col-md-4 mb-4">
        <div class="card card-maquinaria p-3 text-light">
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
          <?php if ($fila['tipo_maquinaria'] == 'nueva' && $fila['subtipo'] == 'Esparcidor de sello'): ?>
            <a href="avance_esparcidor.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-success mt-2 w-100">🛠️ Ver Avance</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>
</body>
</html>
