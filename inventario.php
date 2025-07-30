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

$where = [];
if ($busqueda !== '') {
  $where[] = "(m.nombre LIKE '%$busqueda%' OR m.modelo LIKE '%$busqueda%' OR m.numero_serie LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'nueva') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'nueva'";
  if ($subtipo_filtro !== 'todos') {
    $where[] = "LOWER(TRIM(m.subtipo)) = '".$conn->real_escape_string($subtipo_filtro)."'";
  }
}
if ($tipo_filtro === 'usada') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'usada'";
}
if ($tipo_filtro === 'camion') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'camion'";
}

$sql = "
SELECT m.*,
       r.condicion_estimada, r.observaciones, r.fecha AS fecha_recibo,
       ab.avance AS avance_bachadora,
       ae.avance AS avance_esparcidor,
       ap.avance AS avance_petrolizadora
FROM maquinaria m
LEFT JOIN recibo_unidad r ON m.id = r.id_maquinaria
LEFT JOIN avance_bachadora ab ON m.id = ab.id_maquinaria AND ab.etapa IS NULL
LEFT JOIN avance_esparcidor ae ON m.id = ae.id_maquinaria AND ae.etapa IS NULL
LEFT JOIN avance_petrolizadora ap ON m.id = ap.id_maquinaria AND ap.etapa IS NULL
";
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
    h3.text-warning {
      font-size: 2.5rem;
      font-weight: 900;
      letter-spacing: 2px;
      margin-bottom: 1.5rem;
      text-shadow: 0 2px 16px #000;
    }
    .nav-tabs .nav-link {
      color: #ffc107 !important;
      border: 1px solid #ffc107 !important;
      background: transparent !important;
      font-weight: bold;
      font-size: 1.2rem;
    }
    .nav-tabs .nav-link.active,
    .nav-tabs .nav-link.active:focus,
    .nav-tabs .nav-link.active:hover {
      background-color: #ffc107 !important;
      color: #001f3f !important;
      font-weight: bold;
    }
    .table thead th { background-color: #004080; color: #ffffff; }
    .table tbody tr:nth-child(even) { background-color: #003366; }
    .table tbody tr:nth-child(odd) { background-color: #002b5c; }
    .badge-nueva { background-color: #ffc107; color: #001f3f; padding: 6px 12px; border-radius: 6px; }
    .imagen-thumbnail { width: 80px; height: auto; border-radius: 4px; }
    .progress { height: 22px; border-radius: 20px; background-color: #002b5c; overflow: hidden; }
    .progress-bar { font-weight: bold; background-color: #28a745 !important; color: white; font-size: 1.2rem; }
    .text-light.small.mt-1 { font-size: 0.85rem; color: #ffc107 !important; }
    .btn.btn-avan { margin-top: 5px; }
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
  <!-- Pestañas -->
  <?php
    $isNueva = $tipo_filtro === 'nueva';
    $isCamion = $tipo_filtro === 'camion';
  ?>
  <ul class="nav nav-tabs mb-2">
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'todas' ? 'active' : '' ?>" href="?tipo=todas">Todas</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'nueva' ? 'active' : '' ?>" href="?tipo=nueva">Producción Nueva</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'usada' ? 'active' : '' ?>" href="?tipo=usada">Usada</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'camion' ? 'active' : '' ?>" href="?tipo=camion">Camión</a>
    </li>
  </ul>
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

  <!-- Buscador -->
  <form class="mb-3" method="GET">
    <div class="input-group">
      <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_filtro) ?>">
      <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, modelo o número de serie" value="<?= htmlspecialchars($busqueda) ?>">
      <button class="btn btn-warning" type="submit">Buscar</button>
    </div>
  </form>

  <table class="table table-hover table-bordered text-white">
    <thead>
      <tr>
        <th>Imagen</th>
        <th>Nombre</th>
        <th>Modelo</th>
        <?php if ($tipo_filtro === 'camion'): ?>
          <th>Año</th>
          <th>Número de Serie</th>
          <th>Marca</th>
        <?php endif; ?>
        <th>Ubicación</th>
        <th>Tipo</th>
        <?php if ($tipo_filtro !== 'camion'): ?>
          <th>Subtipo</th>
        <?php endif; ?>
        <?php if ($tipo_filtro !== 'camion'): ?>
          <th>Avance / Condición</th>
        <?php endif; ?>
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
        <?php if ($tipo_filtro === 'camion'): ?>
          <td><?= htmlspecialchars($fila['anio'] ?? '') ?></td>
          <td><?= htmlspecialchars($fila['numero_serie'] ?? '') ?></td>
          <td><?= htmlspecialchars($fila['marca'] ?? '') ?></td>
        <?php endif; ?>
        <td><?= htmlspecialchars($fila['ubicacion']) ?></td>
        <td>
          <?php
          $tipo = strtolower($fila['tipo_maquinaria']);
          echo ($tipo === 'nueva') ? '<span class="badge-nueva">Nueva</span>' : ucfirst($tipo);
          ?>
        </td>
        <?php if ($tipo_filtro !== 'camion'): ?>
          <td><?= htmlspecialchars($fila['subtipo']) ?></td>
        <?php endif; ?>
        <?php if ($tipo_filtro !== 'camion'): ?>
        <td>
          <?php
          $subtipo = strtolower($fila['subtipo']);
          if ($tipo === 'usada') {
            if (!is_null($fila['condicion_estimada'])) {
              echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.intval($fila['condicion_estimada']).'%;">'.intval($fila['condicion_estimada']).'%</div></div>';
              if (!empty($fila['fecha_recibo'])) {
                echo '<div class="text-light small mt-1">🗓 <strong>'.date('d/m/Y', strtotime($fila['fecha_recibo'])).'</strong></div>';
              }
            } else {
              echo '<span class="text-warning">Sin recibo</span>';
            }
          } elseif ($tipo === 'nueva') {
            if ($subtipo === 'bachadora') {
              if (!is_null($fila['avance_bachadora'])) {
                echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.intval($fila['avance_bachadora']).'%;">'.intval($fila['avance_bachadora']).'%</div></div>';
              }
            } elseif ($subtipo === 'esparcidor de sello') {
              if (!is_null($fila['avance_esparcidor'])) {
                echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.intval($fila['avance_esparcidor']).'%;">'.intval($fila['avance_esparcidor']).'%</div></div>';
              }
            } elseif ($subtipo === 'petrolizadora') {
              if (!is_null($fila['avance_petrolizadora'])) {
                echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.intval($fila['avance_petrolizadora']).'%;">'.intval($fila['avance_petrolizadora']).'%</div></div>';
              }
            }
          }
          ?>
        </td>
        <?php endif; ?>
        <td>
          <a href="editar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
            <i class="bi bi-pencil-square"></i>
          </a>
          <a href="eliminar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')" title="Eliminar">
            <i class="bi bi-trash"></i>
          </a>
          <?php if ($tipo === 'usada'): ?>
            <a href="acciones/recibo_unidad.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-warning" title="Editar recibo de unidad">
              <i class="bi bi-file-earmark-text"></i> Recibo
            </a>
          <?php elseif ($tipo === 'nueva' && in_array($subtipo, ['bachadora', 'esparcidor de sello', 'petrolizadora'])): ?>
            <?php
              $map = [
                'bachadora' => 'avance_bachadora.php',
                'esparcidor de sello' => 'avance_esparcidor.php',
                'petrolizadora' => 'avance_petrolizadora.php'
              ];
            ?>
            <a href="<?= $map[$subtipo] ?? '#' ?>?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-success btn-avan" title="Capturar/Ver avance">
              <i class="bi bi-bar-chart-line"></i> Avance
            </a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr>
        <td colspan="<?= $tipo_filtro === 'camion' ? '10' : '9' ?>" class="text-center text-warning">No se encontraron registros.</td>
      </tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
