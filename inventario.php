<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

// Filtros
$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$tipo_filtro = strtolower(trim($_GET['tipo'] ?? 'todas'));
$subtipo_filtro = strtolower(trim($_GET['subtipo'] ?? 'todos'));

$where = [];

if ($busqueda !== '') {
  $where[] = "(m.nombre LIKE '%$busqueda%' OR m.modelo LIKE '%$busqueda%' OR m.numero_serie LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'produccion nueva' || $tipo_filtro === 'nueva') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'nueva'";
}
if ($tipo_filtro === 'usada') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'usada'";
}
if ($tipo_filtro === 'camion') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'camion'";
}
if (($tipo_filtro === 'produccion nueva' || $tipo_filtro === 'nueva') && $subtipo_filtro !== 'todos') {
  $where[] = "LOWER(TRIM(m.subtipo)) = '" . $conn->real_escape_string($subtipo_filtro) . "'";
}

$sql = "
SELECT m.*,
       r.condicion_estimada, r.observaciones, r.fecha AS fecha_recibo,
       ab.avance AS avance_bachadora, ab.updated_at AS updated_bachadora,
       ae.avance AS avance_esparcidor, ae.updated_at AS updated_esparcidor,
       ap.avance AS avance_petrolizadora, ap.updated_at AS updated_petrolizadora
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
    .table thead th { background-color: #004080; color: #ffffff; }
    .table tbody tr:nth-child(even) { background-color: #003366; }
    .table tbody tr:nth-child(odd) { background-color: #002b5c; }
    .badge-nueva { background-color: #ffc107; color: #001f3f; padding: 6px 12px; border-radius: 6px; }
    .imagen-thumbnail { width: 80px; height: auto; border-radius: 4px; }
    .progress { height: 26px; border-radius: 20px; background-color: #002b5c; overflow: hidden; }
    .progress-bar { font-weight: bold; background-color: #28a745 !important; color: white; font-size:1.2rem; }
    .text-light.small.mt-1 { font-size: 0.85rem; color: #ffc107 !important; }
    h1.display-4 { font-size: 2.7rem; letter-spacing: 2px; color: #ffc107; margin-bottom: 2rem; text-shadow:0 2px 8px #0009;}
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between mb-4 align-items-center">
    <h1 class="display-4">Inventario de Maquinaria</h1>
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
      <a class="nav-link <?= ($tipo_filtro === 'produccion nueva' || $tipo_filtro === 'nueva') ? 'active' : '' ?>" href="?tipo=nueva">Producción Nueva</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'usada' ? 'active' : '' ?>" href="?tipo=usada">Usada</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'camion' ? 'active' : '' ?>" href="?tipo=camion">Camión</a>
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
        <th>Año</th>
        <th>Número de Serie</th>
        <th>Ubicación</th>
        <th>Tipo</th>
        <th>Subtipo</th>
        <th>Avance / Condición</th>
        <th>Acciones</th>
      </tr>
    </thead>
   <tbody>
<?php if ($resultado->num_rows > 0): ?>
<?php while ($fila = $resultado->fetch_assoc()):
  $id = intval($fila['id']);
  $tipo_maq = strtolower(trim($fila['tipo_maquinaria']));
  $subtipo = strtolower(trim($fila['subtipo']));
  ?>
  <tr>
    <td>
      <?php if (!empty($fila['imagen'])): ?>
        <img src="imagenes/<?= htmlspecialchars($fila['imagen']) ?>" class="imagen-thumbnail" alt="<?= htmlspecialchars($fila['nombre']) ?>">
      <?php else: ?>
        Sin imagen
      <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($fila['nombre']) ?></td>
    <td><?= htmlspecialchars($fila['modelo']) ?></td>
    <td><?= htmlspecialchars($fila['anio']) ?></td>
    <td><?= htmlspecialchars($fila['numero_serie']) ?></td>
    <td><?= htmlspecialchars($fila['ubicacion']) ?></td>
    <td>
      <?php
        if ($tipo_maq === 'nueva') {
          echo '<span class="badge-nueva">Nueva</span>';
        } elseif ($tipo_maq === 'camion') {
          echo '<span class="badge bg-info text-dark">Camión</span>';
        } else {
          echo 'Usada';
        }
      ?>
    </td>
    <td><?= htmlspecialchars($fila['subtipo']) ?></td>
    <td>
      <?php
      if ($tipo_maq === 'usada') {
        if (!is_null($fila['condicion_estimada'])) {
          $avance = intval($fila['condicion_estimada']);
          echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.$avance.'%;">'.$avance.'%</div></div>';
          if (!empty($fila['fecha_recibo'])) {
            echo '<div class="text-light small mt-1">🗓 <strong>'.date('d/m/Y', strtotime($fila['fecha_recibo'])).'</strong></div>';
          }
        } else {
          echo '<span class="text-warning">Sin recibo</span>';
        }
      } elseif ($tipo_maq === 'nueva') {
        if ($subtipo === 'bachadora') {
          if (!is_null($fila['avance_bachadora'])) {
            $avance = intval($fila['avance_bachadora']);
            echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.$avance.'%;">'.$avance.'%</div></div>';
            if (!empty($fila['updated_bachadora'])) {
              echo '<div class="small text-light mt-1">🗓 <strong>'.date('d/m/Y H:i', strtotime($fila['updated_bachadora'])).'</strong></div>';
            }
          }
          echo '<a href="avance_bachadora.php?id='.$fila['id'].'" class="btn btn-sm btn-outline-success mt-2"><i class="bi bi-bar-chart-line"></i> Avance</a>';
        } elseif ($subtipo === 'esparcidor de sello') {
          if (!is_null($fila['avance_esparcidor'])) {
            $avance = intval($fila['avance_esparcidor']);
            echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.$avance.'%;">'.$avance.'%</div></div>';
            if (!empty($fila['updated_esparcidor'])) {
              echo '<div class="small text-light mt-1">🗓 <strong>'.date('d/m/Y H:i', strtotime($fila['updated_esparcidor'])).'</strong></div>';
            }
          }
          echo '<a href="avance_esparcidor.php?id='.$fila['id'].'" class="btn btn-sm btn-outline-success mt-2"><i class="bi bi-bar-chart-line"></i> Avance</a>';
        } elseif ($subtipo === 'petrolizadora') {
          if (!is_null($fila['avance_petrolizadora'])) {
            $avance = intval($fila['avance_petrolizadora']);
            echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.$avance.'%;">'.$avance.'%</div></div>';
            if (!empty($fila['updated_petrolizadora'])) {
              echo '<div class="small text-light mt-1">🗓 <strong>'.date('d/m/Y H:i', strtotime($fila['updated_petrolizadora'])).'</strong></div>';
            }
          }
          echo '<a href="avance_petrolizadora.php?id='.$fila['id'].'" class="btn btn-sm btn-outline-success mt-2"><i class="bi bi-bar-chart-line"></i> Avance</a>';
        } else {
          echo '<span class="text-secondary">N/A</span>';
        }
      } elseif ($tipo_maq === 'camion') {
        echo '<span class="text-info">Sin avance</span>';
      } else {
        echo '<span class="text-secondary">N/A</span>';
      }
      ?>
    </td>
    <td>
      <a href="editar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
        <i class="bi bi-pencil-square"></i>
      </a>
      <a href="eliminar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')" title="Eliminar">
        <i class="bi bi-trash"></i>
      </a>
      <?php if ($tipo_maq === 'usada'): ?>
        <a href="acciones/recibo_unidad.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-warning" title="Editar recibo de unidad">
          <i class="bi bi-file-earmark-text"></i> Recibo
        </a>
      <?php endif; ?>
      <?php if ($tipo_maq === 'nueva' && in_array($subtipo, ['bachadora', 'esparcidor de sello', 'petrolizadora'])): ?>
        <!-- Ya están los botones de avance arriba -->
      <?php endif; ?>
    </td>
  </tr>
<?php endwhile; ?>
<?php else: ?>
  <tr>
    <td colspan="10" class="text-center text-warning">No se encontraron registros.</td>
  </tr>
<?php endif; ?>
</tbody>
  </table>
  <a href="exportar_excel.php" class="btn btn-warning position-fixed bottom-0 end-0 m-4 shadow">
    <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
  </a>
</div>
</body>
</html>
