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
  $where[] = "(m.nombre LIKE '%$busqueda%' OR m.modelo LIKE '%$busqueda%' OR m.numero_serie LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'nueva') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'nueva'";
  if ($subtipo_filtro !== 'todos') {
    $where[] = "LOWER(TRIM(m.subtipo)) = '" . $conn->real_escape_string($subtipo_filtro) . "'";
  }
}
if ($tipo_filtro === 'usada') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'usada'";
}
if ($tipo_filtro === 'camion') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'camion'";
}

// JOINS para avances y fechas
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
    .container-custom {
      background: #012a5c;
      border-radius: 32px;
      padding: 40px 32px;
      box-shadow: 0 0 20px rgba(0,0,0,0.18);
      margin-top: 40px;
    }
    .titulo-maquinaria {
      font-size: 2.5rem;
      font-weight: bold;
      text-align: center;
      letter-spacing: 2px;
      margin-bottom: 30px;
      color: #ffc107;
      text-shadow: 0 3px 12px #0e222e44;
    }
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 28px;
      gap: 12px;
      flex-wrap: wrap;
    }
    .top-bar-btns {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }
    .btn-agregar {
      background: #28a745;
      color: #fff;
      font-weight: bold;
      border-radius: 22px;
      padding: 7px 22px;
      border: none;
      font-size: 1.09rem;
      transition: background 0.17s;
    }
    .btn-agregar:hover { background: #218838; color: #fff; }
    .btn-exportar {
      background: #ffc107;
      color: #001f3f;
      font-weight: bold;
      border-radius: 22px;
      padding: 7px 26px;
      font-size: 1.1rem;
      transition: background 0.17s;
      border: none;
    }
    .btn-exportar:hover { background: #ffca2c; }
    .btn-salir {
      background: #e74c3c;
      color: #fff;
      font-weight: bold;
      border-radius: 22px;
      padding: 7px 19px;
      font-size: 1.06rem;
      transition: background 0.15s;
      border: none;
    }
    .btn-salir:hover { background: #c0392b; color: #fff; }
    .nav-tabs .nav-link,
    .nav-pills .nav-link {
      background: #175266 !important;
      color: #fff !important;
      border: none !important;
      font-weight: bold;
      margin-right: 6px;
      border-radius: 16px 16px 0 0 !important;
      transition: background 0.2s, color 0.2s;
    }
    .nav-tabs .nav-link.active,
    .nav-pills .nav-link.active {
      background: #ffc107 !important;
      color: #032c3b !important;
    }
    .nav-pills .nav-link {
      border-radius: 18px !important;
    }
    .table {
      border-radius: 18px;
      overflow: hidden;
      background: #012a5c;
      box-shadow: 0 3px 18px #00000018;
    }
    .table thead th {
      background-color: #004080;
      color: #ffffff;
      font-weight: 700;
      border: none;
      font-size: 1.05rem;
      letter-spacing: 1px;
    }
    .table tbody tr {
      transition: background 0.14s;
    }
    .table tbody tr:nth-child(even) { background-color: #003366; }
    .table tbody tr:nth-child(odd) { background-color: #002b5c; }
    .badge-nueva { background-color: #ffc107; color: #001f3f; padding: 6px 12px; border-radius: 8px; }
    .badge-camion { background: #01ff1294; color: #fff;padding: 6px 12px; border-radius: 8px; }
    .imagen-thumbnail { width: 82px; height: auto; border-radius: 8px; border: 2px solid #27a0b6; }
    .progress { height: 22px; border-radius: 20px; background-color: #002b5c; overflow: hidden; }
    .progress-bar {
      font-weight: bold;
      background-color: #28a745 !important;
      color: #fff;
      font-size: 1.1rem;
      transition: width 0.5s;
      box-shadow: 0 2px 8px #0002 inset;
    }
    .fecha-actualizacion {
      color: #012a5c;
      font-size: 0.96rem;
      font-weight: bold;
      margin-top: 2px;
      margin-bottom: 3px;
    }
    .btn-avance {
      background: #007b91;
      border: none;
      color: #fff;
      border-radius: 8px;
      font-size: 0.95rem;
      padding: 5px 13px;
      margin-top: 4px;
      transition: background 0.18s;
    }
    .btn-avance:hover { background: #015b65; }
    @media (max-width: 992px) {
      .container-custom { padding: 16px 2px; }
      .titulo-maquinaria { font-size: 2rem; }
      .top-bar { flex-direction: column; align-items: stretch; gap: 8px; }
      .top-bar-btns { justify-content: center; }
    }
  </style>
</head>
<body>
<div class="container container-custom">
  <div class="top-bar">
    <div class="titulo-maquinaria">Maquinaria</div>
    <div class="top-bar-btns">
      <a href="agregar_maquinaria.php" class="btn btn-agregar"><i class="bi bi-plus-circle"></i> Agregar Maquinaria</a>
      <a href="exportar_excel.php?tipo=<?= urlencode($tipo_filtro ?? '') ?>&busqueda=<?= urlencode($busqueda ?? '') ?>" class="btn btn-outline-warning me-2">Exportar</a>
      <a href="logout.php" class="btn btn-salir"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
    </div>
  </div>
  <!-- Filtros -->
  <ul class="nav nav-tabs mb-2">
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'todas' ? 'active' : '' ?>" href="?tipo=todas">Todas</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === 'nueva' ? 'active' : '' ?>" href="?tipo=nueva">Nueva</a>
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

  <!-- Búsqueda -->
  <form class="mb-3" method="GET">
    <div class="input-group">
      <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_filtro) ?>">
      <?php if ($tipo_filtro === 'nueva'): ?>
        <input type="hidden" name="subtipo" value="<?= htmlspecialchars($subtipo_filtro) ?>">
      <?php endif; ?>
      <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, modelo o número de serie" value="<?= htmlspecialchars($busqueda) ?>">
      <button class="btn btn-warning" type="submit">Buscar</button>
    </div>
  </form>
  <!-- Tabla -->
  <div class="table-responsive">
    <table class="table table-hover table-bordered text-white align-middle">
      <thead>
        <tr>
          <th>Imagen</th>
          <th>Nombre</th>
          <th>Modelo</th>
          <th>Número Serie</th>
          <th>Ubicación</th>
          <th>Tipo</th>
          <th>Subtipo</th>
          <th style="min-width:160px;">Avance / Condición</th>
          <th style="min-width:135px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($resultado->num_rows > 0): ?>
        <?php while($fila = $resultado->fetch_assoc()):
          $tipo = strtolower($fila['tipo_maquinaria']);
          $subtipo = strtolower($fila['subtipo']);
        ?>
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
          <td><?= htmlspecialchars($fila['numero_serie']) ?></td>
          <td><?= htmlspecialchars($fila['ubicacion']) ?></td>
          <td>
            <?php
              if ($tipo === 'nueva') echo '<span class="badge-nueva">Nueva</span>';
              elseif ($tipo === 'usada') echo 'Usada';
              elseif ($tipo === 'camion') echo '<span class="badge-camion">Camión</span>';
              else echo ucfirst($tipo);
            ?>
          </td>
          <td><?= htmlspecialchars($fila['subtipo']) ?></td>
          <td>
            <?php
            // AVANCES y FECHAS según tipo/subtipo
            if ($tipo === 'usada') {
                if (!is_null($fila['condicion_estimada'])) {
                    echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.intval($fila['condicion_estimada']).'%;">'.intval($fila['condicion_estimada']).'%</div></div>';
                    if (!empty($fila['fecha_recibo'])) {
                        echo '<div class="fecha-actualizacion">Actualizado: '.
                             date('d/m/Y', strtotime($fila['fecha_recibo'])).
                             '</div>';
                    }
                } else {
                    echo '<span class="text-warning">Sin recibo</span>';
                }
            } elseif ($tipo === 'nueva') {
                if ($subtipo === 'bachadora') {
                    if (!is_null($fila['avance_bachadora'])) {
                        echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.intval($fila['avance_bachadora']).'%;">'.intval($fila['avance_bachadora']).'%</div></div>';
                        if (!empty($fila['fecha_bachadora'])) {
                            echo '<div class="fecha-actualizacion">Actualizado: '.
                                 date('d/m/Y', strtotime($fila['fecha_bachadora'])).
                                 '</div>';
                        }
                    }
                } elseif ($subtipo === 'esparcidor de sello') {
                    if (!is_null($fila['avance_esparcidor'])) {
                        echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.intval($fila['avance_esparcidor']).'%;">'.intval($fila['avance_esparcidor']).'%</div></div>';
                        if (!empty($fila['fecha_esparcidor'])) {
                            echo '<div class="fecha-actualizacion">Actualizado: '.
                                 date('d/m/Y', strtotime($fila['fecha_esparcidor'])).
                                 '</div>';
                        }
                    }
                } elseif ($subtipo === 'petrolizadora') {
                    if (!is_null($fila['avance_petrolizadora'])) {
                        echo '<div class="progress mb-1"><div class="progress-bar" style="width:'.intval($fila['avance_petrolizadora']).'%;">'.intval($fila['avance_petrolizadora']).'%</div></div>';
                        if (!empty($fila['fecha_petrolizadora'])) {
                            echo '<div class="fecha-actualizacion">Actualizado: '.
                                 date('d/m/Y', strtotime($fila['fecha_petrolizadora'])).
                                 '</div>';
                        }
                    }
                } else {
                    echo '<span class="text-secondary">N/A</span>';
                }
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
            <?php if ($tipo === 'usada'): ?>
              <a href="acciones/recibo_unidad.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-warning" title="Editar recibo de unidad">
                <i class="bi bi-file-earmark-text"></i> Recibo
              </a>
            <?php elseif ($tipo === 'nueva' && $subtipo === 'bachadora'): ?>
              <a href="avance_bachadora.php?id=<?= $fila['id'] ?>" class="btn btn-avance" title="Avance de Bachadora">
                <i class="bi bi-bar-chart-line"></i> Avance
              </a>
            <?php elseif ($tipo === 'nueva' && $subtipo === 'esparcidor de sello'): ?>
              <a href="avance_esparcidor.php?id=<?= $fila['id'] ?>" class="btn btn-avance" title="Avance Esparcidor">
                <i class="bi bi-bar-chart-line"></i> Avance
              </a>
            <?php elseif ($tipo === 'nueva' && $subtipo === 'petrolizadora'): ?>
              <a href="avance_petrolizadora.php?id=<?= $fila['id'] ?>" class="btn btn-avance" title="Avance Petrolizadora">
                <i class="bi bi-bar-chart-line"></i> Avance
              </a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="9" class="text-center text-warning">No se encontraron registros.</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
