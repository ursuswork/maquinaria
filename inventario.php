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
  $sql .= " WHERE nombre LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%' OR numero_serie LIKE '%$busqueda%' OR subtipo LIKE '%$busqueda%'";
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
    .tabla-img { width: 80px; height: auto; border-radius: 4px; }
    .btn-sm { font-size: 0.75rem; }
  </style>
</head>
<body class="bg-light">
  <div class="container py-4">
    <h2 class="mb-4 text-center text-primary">Inventario de Maquinaria</h2>
    <form class="d-flex mb-3" method="GET">
      <input type="text" name="busqueda" class="form-control me-2" placeholder="Buscar maquinaria..." value="<?=htmlspecialchars($busqueda)?>">
      <button type="submit" class="btn btn-outline-primary">Buscar</button>
    </form>

    <table class="table table-bordered table-striped table-hover">
      <thead class="table-dark text-center">
        <tr>
          <th>Imagen</th>
          <th>Nombre</th>
          <th>Tipo</th>
          <th>Subtipo</th>
          <th>Modelo</th>
          <th>Ubicación</th>
          <th>Condición</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($fila = $resultado->fetch_assoc()): ?>
        <tr>
          <td class="text-center">
            <?php if (!empty($fila['imagen'])): ?>
              <img src="imagenes/<?=htmlspecialchars($fila['imagen'])?>" class="tabla-img">
            <?php else: ?>
              <span class="text-muted">Sin imagen</span>
            <?php endif; ?>
          </td>
          <td><?=htmlspecialchars($fila['nombre'])?></td>
          <td><?=htmlspecialchars($fila['tipo_maquinaria'])?></td>
          <td><?=htmlspecialchars($fila['subtipo'] ?? '-')?></td>
          <td><?=htmlspecialchars($fila['modelo'])?></td>
          <td><?=htmlspecialchars($fila['ubicacion'])?></td>
          <td>
            <div class="progress" style="height: 20px;">
              <div class="progress-bar 
                <?=($fila['condicion_estimada'] >= 85) ? 'bg-success' : (($fila['condicion_estimada'] >= 60) ? 'bg-warning' : 'bg-danger')?>"
                style="width: <?=$fila['condicion_estimada']?>%;">
                <?=$fila['condicion_estimada']?>%
              </div>
            </div>
          </td>
          <td class="text-center">
            <a href="editar_maquinaria.php?id=<?=$fila['id']?>" class="btn btn-sm btn-primary">✏️</a>
            <a href="eliminar_maquinaria.php?id=<?=$fila['id']?>" class="btn btn-sm btn-danger">🗑️</a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
