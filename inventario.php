<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

// Filtros
$busqueda    = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$tipo_filtro = strtolower(trim($_GET['tipo'] ?? 'todas'));
$subtipo_filtro = strtolower(trim($_GET['subtipo'] ?? 'todos'));

$where = [];
if ($busqueda !== '') {
  $where[] = "(m.nombre LIKE '%$busqueda%' OR m.modelo LIKE '%$busqueda%' OR m.numero_serie LIKE '%$busqueda%' OR m.marca LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'nueva') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'nueva'";
  if ($subtipo_filtro !== 'todos') {
    $where[] = "LOWER(TRIM(m.subtipo)) = '" . $conn->real_escape_string($subtipo_filtro) . "'";
  }
} elseif ($tipo_filtro === 'usada') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'usada'";
} elseif ($tipo_filtro === 'camion') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'camion'";
}

$sql = "
SELECT m.*,
       r.condicion_estimada, r.observaciones, r.fecha AS fecha_recibo,
       ab.avance AS avance_bachadora, ab.fecha_actualizacion AS fecha_bachadora,
       ae.avance AS avance_esparcidor, ae.fecha_actualizacion AS fecha_esparcidor,
       ap.avance AS avance_petrolizadora, ap.fecha_actualizacion AS fecha_petrolizadora
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
  <title>Maquinaria</title>
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
  .progress { height: 22px; border-radius: 20px; background-color: #002b5c; overflow: hidden; }
  .progress-bar { font-weight: bold; background-color: #28a745 !important; color: white; }
  .nav-tabs .nav-link,
  .nav-pills .nav-link {
    background: #175266 !important;    /* Azul petróleo */
    color: #fff !important;
    border: none !important;
    font-weight: bold;
    margin-right: 6px;
  }
  .nav-tabs .nav-link.active,
  .nav-pills .nav-link.active {
    background: #27a0b6 !important; /* Más claro para activo */
    color: #fff !important;
  }
  .fecha-actualizacion {
    color: #012a5c;        /* Azul marino */
    font-size: 0.93rem;
    font-weight: bold;
  }
</style>
</head>
<body>
<div class="container py-4">
  <div class="titulo-maquinaria">Maquinaria</div>
  <div class="d-flex justify-content-between mb-3 align-items-center">
    <div>
      <form action="exportar_inventario.php" method="POST" style="display:inline;">
        <button type="submit" class="btn btn-exportar">
          <i class="bi bi-download"></i> Exportar
        </button>
      </form>
    </div>
    <div>
      <a href="agregar_maquinaria.php" class="btn btn-success">+ Agregar Maquinaria</a>
      <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
    </div>
  </div>

  <!-- Pestañas -->
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
    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_filtro) ?>">
    <div class="input-group">
      <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, modelo, serie o marca" value="<?= htmlspecialchars($busqueda) ?>">
      <button class="btn btn-warning" type="submit">Buscar</button>
    </div>
  </form>

  <table class="table table-hover table-bordered text-white">
    <thead>
      <tr>
        <th>Imagen</th>
        <th>Nombre</th>
        <th>Marca</th>
        <th>Modelo</th>
        <th>Núm. Serie</th>
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
        <td><?= htmlspecialchars($fila['marca']) ?></td>
        <td><?= htmlspecialchars($fila['modelo']) ?></td>
        <td><?= htmlspecialchars($fila['numero_serie']) ?></td>
        <td><?= htmlspecialchars($fila['ubicacion']) ?></td>
        <td>
          <?php
            $tipo = strtolower($fila['tipo_maquinaria']);
            echo ($tipo === 'nueva') 
              ? '<span class="badge-nueva">Nueva</span>'
              : (($tipo === 'camion') ? '<span class="badge-camion">Camión</span>' : 'Usada');
          ?>
        </td>
        <td><?= htmlspecialchars($fila['subtipo']) ?></td>
        <td>
          <?php
            if ($tipo === 'usada') {
              if (!is_null($fila['condicion_estimada'])) {
                echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.intval($fila['condicion_estimada']).'%;">'.intval($fila['condicion_estimada']).'%</div></div>';
                if (!empty($fila['fecha_recibo'])) {
                  echo '<div class="text-fecha-azul">🗓 '.date('d/m/Y', strtotime($fila['fecha_recibo'])).'</div>';
                }
              } else {
                echo '<span class="text-warning">Sin recibo</span>';
              }
            } elseif ($tipo === 'nueva') {
              $subtipo = strtolower(trim($fila['subtipo']));
              if ($subtipo === 'bachadora' && !is_null($fila['avance_bachadora'])) {
                echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.intval($fila['avance_bachadora']).'%;">'.intval($fila['avance_bachadora']).'%</div></div>';
                if (!empty($fila['fecha_bachadora'])) {
                  echo '<div class="text-fecha-azul">🗓 '.date('d/m/Y', strtotime($fila['fecha_bachadora'])).'</div>';
                }
              } elseif ($subtipo === 'esparcidor de sello' && !is_null($fila['avance_esparcidor'])) {
                echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.intval($fila['avance_esparcidor']).'%;">'.intval($fila['avance_esparcidor']).'%</div></div>';
                if (!empty($fila['fecha_esparcidor'])) {
                  echo '<div class="text-fecha-azul">🗓 '.date('d/m/Y', strtotime($fila['fecha_esparcidor'])).'</div>';
                }
              } elseif ($subtipo === 'petrolizadora' && !is_null($fila['avance_petrolizadora'])) {
                echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.intval($fila['avance_petrolizadora']).'%;">'.intval($fila['avance_petrolizadora']).'%</div></div>';
                if (!empty($fila['fecha_petrolizadora'])) {
                  echo '<div class="text-fecha-azul">🗓 '.date('d/m/Y', strtotime($fila['fecha_petrolizadora'])).'</div>';
                }
              } else {
                echo '<span class="text-secondary">N/A</span>';
              }
            } elseif ($tipo === 'camion') {
              echo '<span class="text-secondary">N/A</span>';
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
          <?php
          if ($tipo === 'nueva') {
            $subtipo = strtolower(trim($fila['subtipo']));
            if ($subtipo === 'bachadora') {
              echo '<a href="avance_bachadora.php?id='.$fila['id'].'" class="btn btn-sm btn-avance btn-outline-success mt-1" title="Avance Bachadora"><i class="bi bi-bar-chart-line"></i> Avance</a>';
            } elseif ($subtipo === 'esparcidor de sello') {
              echo '<a href="avance_esparcidor.php?id='.$fila['id'].'" class="btn btn-sm btn-avance btn-outline-success mt-1" title="Avance Esparcidor"><i class="bi bi-bar-chart-line"></i> Avance</a>';
            } elseif ($subtipo === 'petrolizadora') {
              echo '<a href="avance_petrolizadora.php?id='.$fila['id'].'" class="btn btn-sm btn-avance btn-outline-success mt-1" title="Avance Petrolizadora"><i class="bi bi-bar-chart-line"></i> Avance</a>';
            }
          }
          if ($tipo === 'usada') {
            echo '<a href="acciones/recibo_unidad.php?id='.$fila['id'].'" class="btn btn-sm btn-outline-warning mt-1" title="Recibo de Unidad"><i class="bi bi-file-earmark-text"></i> Recibo</a>';
          }
          ?>
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
</div>
</body>
</html>
