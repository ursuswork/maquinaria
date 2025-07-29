<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$busqueda    = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$tipo_filtro = strtolower(trim($_GET['tipo'] ?? 'todas'));
$subtipo_filtro = strtolower(trim($_GET['subtipo'] ?? 'todos'));

$sql = "
  SELECT m.*, r.condicion_estimada, r.observaciones, r.fecha AS fecha_recibo
  FROM maquinaria m
  LEFT JOIN recibo_unidad r ON m.id = r.id_maquinaria
";
if ($busqueda !== '') {
  $sql .= " WHERE (m.nombre LIKE '%$busqueda%' OR m.modelo LIKE '%$busqueda%' OR m.numero_serie LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'produccion nueva' || $tipo_filtro === 'nueva') {
  $sql .= (strpos($sql, 'WHERE') !== false ? ' AND ' : ' WHERE ') 
        . "LOWER(TRIM(m.tipo_maquinaria)) = 'nueva'";
}
if (($tipo_filtro === 'produccion nueva' || $tipo_filtro === 'nueva') && $subtipo_filtro !== 'todos') {
  $sql .= " AND LOWER(TRIM(m.subtipo)) = '" . $conn->real_escape_string($subtipo_filtro) . "'";
}
if ($tipo_filtro === 'usada') {
  $sql .= (strpos($sql, 'WHERE') !== false ? ' AND ' : ' WHERE ') 
        . "LOWER(TRIM(m.tipo_maquinaria)) = 'usada'";
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
    body {
      background-color: #001f3f;
      color: #ffffff;
    }
    .table thead th {
      background-color: #004080;
      color: #ffffff;
    }
    .table tbody tr:nth-child(even) {
      background-color: #003366;
    }
    .table tbody tr:nth-child(odd) {
      background-color: #002b5c;
    }
    .badge-nueva {
      background-color: #ffc107;
      color: #001f3f;
      padding: 6px 12px;
      border-radius: 6px;
    }
    .progress {
      height: 22px;
      border-radius: 20px;
      background-color: #002b5c;
      overflow: hidden;
    }
    .progress-bar {
      font-weight: bold;
      background-color: #28a745 !important;
      color: white;
    }
    .text-light.small.mt-1 {
      font-size: 0.85rem;
      color: #ffc107 !important;
    }
    .btn-flotante {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #ffc107;
      color: #001f3f;
      padding: 12px 18px;
      border-radius: 50px;
      font-weight: bold;
      text-decoration: none;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
    .imagen-thumbnail {
      width: 80px;
      height: auto;
      border-radius: 4px;
    }
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
  <!-- Pestañas de filtro principal -->
  <ul class="nav nav-tabs mb-2">
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'todas' ? 'active' : '' ?>" href="?tipo=todas">Todas</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= ($tipo_filtro === 'produccion nueva' || $tipo_filtro === 'nueva') ? 'active' : '' ?>" href="?tipo=produccion nueva">Producción Nueva</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'usada' ? 'active' : '' ?>" href="?tipo=usada">Usada</a>
    </li>
  </ul>

  <?php if ($tipo_filtro === 'produccion nueva' || $tipo_filtro === 'nueva'): ?>
  <!-- Subpestañas de subtipo -->
  <ul class="nav nav-pills mb-3 ms-3">
    <li class="nav-item">
      <a class="nav-link <?= $subtipo_filtro === 'todos' ? 'active' : '' ?>" href="?tipo=produccion nueva&subtipo=todos">Todos</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $subtipo_filtro === 'bachadora' ? 'active' : '' ?>" href="?tipo=produccion nueva&subtipo=bachadora">Bachadora</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $subtipo_filtro === 'esparcidor de sello' ? 'active' : '' ?>" href="?tipo=produccion nueva&subtipo=esparcidor de sello">Esparcidor de Sello</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $subtipo_filtro === 'petrolizadora' ? 'active' : '' ?>" href="?tipo=produccion nueva&subtipo=petrolizadora">Petrolizadora</a>
    </li>
  </ul>
  <?php endif; ?>

  <!-- Búsqueda -->
  <form class="mb-3" method="GET">
    <div class="input-group">
      <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_filtro) ?>">
      <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, modelo o número de serie" value="<?= htmlspecialchars($busqueda) ?>">
      <button class="btn btn-warning" type="submit">Buscar</button>
    </div>
  </form>
  <!-- Tabla -->
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
        <th>Observaciones</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
<?php while($fila = $resultado->fetch_assoc()):
  $id = intval($fila['id']);
  $tipo_maq = strtolower(trim($fila['tipo_maquinaria']));
  $subtipo = strtolower(trim($fila['subtipo']));
  $avance = 0;

  if ($tipo_maq === 'nueva') {
    // Aquí se calcula el avance para nueva según subtipo (bachadora, esparcidor, petrolizadora)
    include 'calculo_avance_por_subtipo.php'; // O el bloque que ya incluimos antes
  }
?>
  <tr>
    <td>
      <?php if (!empty($fila['imagen'])): ?>
        <img src="imagenes/<?= htmlspecialchars($fila['imagen']) ?>" class="imagen-thumbnail">
      <?php else: ?>
        Sin imagen
      <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($fila['nombre']) ?></td>
    <td><?= htmlspecialchars($fila['modelo']) ?></td>
    <td><?= htmlspecialchars($fila['ubicacion']) ?></td>
    <td><?= $tipo_maq === 'nueva' ? '<span class="badge-nueva">Nueva</span>' : 'Usada' ?></td>
    <td><?= htmlspecialchars($fila['subtipo']) ?></td>
    <td>
      <?php if ($tipo_maq === 'usada'): ?>
  <div class="text-center">
    <div class="progress mb-1">
      <div class="progress-bar" style="width:<?= intval($fila['condicion_estimada']) ?>%"></div>
    </div>
    <span class="fs-5 text-warning fw-bold"><?= intval($fila['condicion_estimada']) ?>%</span>
    <?php if (!empty($fila['fecha_recibo'])): ?>
      <div class="text-light small mt-1">🗓 <strong><?= date('d/m/Y', strtotime($fila['fecha_recibo'])) ?></strong></div>
    <?php endif; ?>
  </div>
<?php else: ?>
        <?php if (!empty($fila['fecha_recibo'])): ?>
          <div class="text-light small mt-1">🗓 <strong><?= date('d/m/Y', strtotime($fila['fecha_recibo'])) ?></strong></div>
        <?php endif; ?>
      <?php else: ?>
        <div class="progress">
          <div class="progress-bar" style="width:<?= $avance ?>%">
            <?= $avance ?>%
          </div>
        </div>
      <?php endif; ?>
    </td>
    <td><?= nl2br(htmlspecialchars($fila['observaciones'] ?? '')) ?></td>
    <td>
      <a href="editar_maquinaria.php?id=<?= $id ?>" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-pencil-square"></i>
      </a>
      <a href="eliminar_maquinaria.php?id=<?= $id ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')">
        <i class="bi bi-trash"></i>
      </a>
      <?php if ($tipo_maq === 'usada'): ?>
        <a href="acciones/recibo_unidad.php?id=<?= $id ?>" class="btn btn-sm btn-outline-warning">
          <i class="bi bi-file-earmark-text"></i>
        </a>
      <?php elseif ($tipo_maq === 'nueva' && in_array($subtipo, ['bachadora', 'esparcidor de sello', 'petrolizadora'])): ?>
        <?php 
          $map = [
            'bachadora' => 'avance_bachadora.php',
            'esparcidor de sello' => 'avance_esparcidor.php',
            'petrolizadora' => 'avance_petrolizadora.php'
          ];
        ?>
        <a href="<?= $map[$subtipo] ?>?id=<?= $id ?>" class="btn btn-sm btn-outline-success">
          <i class="bi bi-bar-chart-line"></i>
        </a>
      <?php endif; ?>
    </td>
  </tr>
<?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Botón flotante de exportar -->
<a href="exportar_excel.php" class="btn btn-warning position-fixed bottom-0 end-0 m-4 shadow">
  <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
</a>

</body>
</html>
