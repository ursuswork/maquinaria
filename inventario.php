<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

// Filtros
$tipo_filtro = isset($_GET['tipo']) ? strtolower(trim($_GET['tipo'])) : 'todas';
$subtipo_filtro = isset($_GET['subtipo']) ? strtolower(trim($_GET['subtipo'])) : 'todos';

// Armar WHERE dinámico
$where = [];
if ($tipo_filtro !== 'todas') {
    $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = '$tipo_filtro'";
}
if ($subtipo_filtro !== 'todos' && $subtipo_filtro !== '') {
    $where[] = "LOWER(TRIM(m.subtipo)) = '$subtipo_filtro'";
}

$sql = "SELECT m.*, r.condicion_estimada, r.observaciones, r.fecha AS fecha_recibo
        FROM maquinaria m
        LEFT JOIN recibo_unidad r ON m.id = r.id_maquinaria";
if (count($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY m.tipo_maquinaria ASC, m.nombre ASC";

$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario de Maquinaria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #001f3f; color: #ffffff; }
    .table thead th { background-color: #004080; color: #ffffff; }
    .table tbody tr:nth-child(even) { background-color: #003366; }
    .table tbody tr:nth-child(odd) { background-color: #002b5c; }
    .badge-nueva { background-color: #ffc107; color: #001f3f; padding: 6px 12px; border-radius: 6px; }
    .btn-flotante { position: fixed; bottom: 20px; right: 20px; background-color: #ffc107; color: #001f3f; padding: 12px 18px; border-radius: 50px; font-weight: bold; text-decoration: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3); }
    .imagen-thumbnail { width: 80px; height: auto; border-radius: 4px; }
    .progress { height: 22px; border-radius: 20px; background-color: #002b5c; overflow: hidden; }
    .progress-bar { font-weight: bold; background-color: #28a745 !important; color: white; }
    .text-light.small.mt-1 { font-size: 0.85rem; color: #ffc107 !important; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3 align-items-center">
    <h3 class="text-warning">Inventario de Maquinaria</h3>
    <div>
      <a href="agregar_maquinaria.php" class="btn btn-success">+ Agregar Maquinaria</a>
      <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
    </div>
  </div>
  <!-- Filtros -->
  <ul class="nav nav-tabs mb-2">
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'todas' ? 'active' : '' ?>" href="?tipo=todas">Todas</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'nueva' ? 'active' : '' ?>" href="?tipo=nueva">Nuevas</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'usada' ? 'active' : '' ?>" href="?tipo=usada">Usadas</a>
    </li>
  </ul>

  <!-- Subfiltro de subtipo solo si está en 'nueva' -->
  <?php if ($tipo_filtro === 'nueva'): ?>
  <ul class="nav nav-pills mb-3 ms-3">
    <li class="nav-item">
      <a class="nav-link <?= $subtipo_filtro === 'todos' ? 'active' : '' ?>" href="?tipo=nueva&subtipo=todos">Todos</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $subtipo_filtro === 'bachadora' ? 'active' : '' ?>" href="?tipo=nueva&subtipo=bachadora">Bachadora</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $subtipo_filtro === 'esparcidor de sello' ? 'active' : '' ?>" href="?tipo=nueva&subtipo=esparcidor de sello">Esparcidor de Sello</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $subtipo_filtro === 'petrolizadora' ? 'active' : '' ?>" href="?tipo=nueva&subtipo=petrolizadora">Petrolizadora</a>
    </li>
  </ul>
  <?php endif; ?>

  <!-- Tabla de resultados -->
  <table class="table table-hover table-bordered text-white">
    <thead>
      <tr>
        <th>Imagen</th>
        <th>Nombre</th>
        <th>Modelo</th>
        <th>Ubicación</th>
        <th>Tipo</th>
        <th>Subtipo</th>
        <th>Avance / Condición</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php if ($resultado->num_rows > 0): ?>
      <?php while($fila = $resultado->fetch_assoc()): ?>
      <tr>
        <td>
          <?php if (!empty($fila['imagen'])): ?>
            <img src="imagenes/<?= htmlspecialchars($fila['imagen']) ?>" class="imagen-thumbnail" alt="Imagen de <?= htmlspecialchars($fila['nombre']) ?>">
          <?php else: ?>
            Sin imagen
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($fila['nombre']) ?></td>
        <td><?= htmlspecialchars($fila['modelo']) ?></td>
        <td><?= htmlspecialchars($fila['ubicacion']) ?></td>
        <td>
          <?= strtolower($fila['tipo_maquinaria']) === 'nueva' ? '<span class="badge-nueva">Nueva</span>' : 'Usada' ?>
        </td>
        <td><?= htmlspecialchars($fila['subtipo']) ?></td>
        <td>
          <?php if (strtolower($fila['tipo_maquinaria']) === 'usada'): ?>
            <?php if (!is_null($fila['condicion_estimada'])): ?>
              <div class="progress mb-1">
                <div class="progress-bar" style="width:<?= intval($fila['condicion_estimada']) ?>%;">
                  <?= intval($fila['condicion_estimada']) ?>%
                </div>
              </div>
              <div class="text-center" style="font-size:1.3rem; color: #ffc107;">
                <?= intval($fila['condicion_estimada']) ?>%
              </div>
              <?php if (!empty($fila['fecha_recibo'])): ?>
                <div class="text-light small mt-1">
                  🗓 <strong><?= date('d/m/Y', strtotime($fila['fecha_recibo'])) ?></strong>
                </div>
              <?php endif; ?>
            <?php else: ?>
              <span class="text-warning">Sin recibo</span>
            <?php endif; ?>
          <?php elseif (strtolower($fila['tipo_maquinaria']) === 'nueva'): ?>
            <span class="text-secondary">N/A</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="editar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
            <i class="bi bi-pencil-square"></i>
          </a>
          <a href="eliminar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')" title="Eliminar">
            <i class="bi bi-trash"></i>
          </a>
          <?php if (strtolower($fila['tipo_maquinaria']) === 'usada'): ?>
            <a href="acciones/recibo_unidad.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-warning" title="Editar recibo de unidad">
              <i class="bi bi-file-earmark-text"></i> Recibo
            </a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr>
        <td colspan="8" class="text-center text-warning">No se encontraron registros.</td>
      </tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
