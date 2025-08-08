<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
require 'conexion.php';

// Datos de sesión
$usuario = $_SESSION['usuario'] ?? '';
$rol = $_SESSION['rol'] ?? 'consulta';

// Filtros sanitizados
$busqueda        = trim($_GET['busqueda'] ?? '');
$tipo_filtro     = strtolower(trim($_GET['tipo'] ?? 'todas'));
$subtipo_filtro  = strtolower(trim($_GET['subtipo'] ?? 'todos'));

// Construcción dinámica del WHERE
$where = [];
$params = [];
$types = '';

if ($busqueda !== '') {
    $where[] = "(m.nombre LIKE ? OR m.modelo LIKE ? OR m.numero_serie LIKE ?)";
    $busq_like = "%{$busqueda}%";
    $params[] = $busq_like; $params[] = $busq_like; $params[] = $busq_like;
    $types .= 'sss';
}
if ($tipo_filtro === 'nueva') {
    $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'nueva'";
    if ($subtipo_filtro !== 'todos') {
        $where[] = "LOWER(TRIM(m.subtipo)) = ?";
        $params[] = $subtipo_filtro;
        $types .= 's';
    }
}
if (in_array($tipo_filtro, ['usada', 'camion'])) {
    $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = ?";
    $params[] = $tipo_filtro;
    $types .= 's';
}

// SQL base
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

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY m.tipo_maquinaria ASC, m.nombre ASC";

// Preparar consulta
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();

// Función permisos
function puede($accion, $usuario, $rol, $tipo) {
    if ($usuario === 'jabri') {
        if ($accion === 'recibo') return in_array($tipo, ['usada', 'camion']);
        return true;
    }
    if ($rol === 'produccion' && in_array($tipo, ['nueva', 'camion'])) {
        if ($accion === 'recibo') return $tipo === 'camion';
        return true;
    }
    if ($rol === 'usada' && $tipo === 'usada') {
        return true;
    }
    return false;
}

// Función para mostrar avance
function mostrarAvance($fila, $tipo, $subtipo) {
    $map = [
        'usada' => ['campo' => 'condicion_estimada', 'fecha' => 'fecha_recibo'],
        'camion' => ['campo' => 'condicion_estimada', 'fecha' => 'fecha_recibo'],
        'nueva' => [
            'bachadora' => ['campo' => 'avance_bachadora', 'fecha' => 'fecha_bachadora'],
            'esparcidor de sello' => ['campo' => 'avance_esparcidor', 'fecha' => 'fecha_esparcidor'],
            'petrolizadora' => ['campo' => 'avance_petrolizadora', 'fecha' => 'fecha_petrolizadora'],
        ]
    ];

    if (!isset($map[$tipo])) {
        return '<span class="text-secondary">N/A</span>';
    }
    $conf = $map[$tipo];
    if ($tipo === 'nueva') {
        if (!isset($conf[$subtipo])) {
            return '<span class="text-secondary">N/A</span>';
        }
        $conf = $conf[$subtipo];
    }

    $avance = $fila[$conf['campo']] ?? null;
    $fecha = $fila[$conf['fecha']] ?? null;

    if (is_null($avance)) {
        return ($tipo === 'usada' || $tipo === 'camion')
            ? '<span class="text-warning">Sin recibo</span>'
            : '<span class="text-secondary">N/A</span>';
    }

    $out = '<div class="progress mb-1"><div class="progress-bar" style="width:' . intval($avance) . '%;">' . intval($avance) . '%</div></div>';
    if (!empty($fecha)) {
        $fecha_mx = (new DateTime($fecha, new DateTimeZone('UTC')))
            ->setTimezone(new DateTimeZone('America/Mexico_City'))
            ->format('d/m/Y H:i');
        $out .= '<div class="fecha-actualizacion">Actualizado: ' . $fecha_mx . '</div>';
    }
    return $out;
}
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
<?php include 'css/estilos_maquinaria.css'; ?>
</style>
</head>
<body>
<div class="container container-custom">
  <div class="top-bar">
    <div class="titulo-maquinaria">Maquinaria</div>
    <div class="top-bar-btns">
      <?php if (puede('editar', $usuario, $rol, $tipo_filtro)): ?>
        <a href="agregar_maquinaria.php" class="btn btn-agregar"><i class="bi bi-plus-circle"></i> Agregar Maquinaria</a>
        <a href="exportar_excel.php?tipo=<?= urlencode($tipo_filtro) ?>&busqueda=<?= urlencode($busqueda) ?>" class="btn btn-exportar">Exportar</a>
      <?php endif; ?>
      <a href="logout.php" class="btn btn-salir"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
    </div>
  </div>

  <!-- Filtros -->
  <ul class="nav nav-tabs mb-2">
    <?php
    $tipos = ['todas' => 'Todas', 'nueva' => 'Producción Nueva', 'usada' => 'Usada', 'camion' => 'Camiones'];
    foreach ($tipos as $key => $label):
    ?>
    <li class="nav-item">
      <a class="nav-link <?= $tipo_filtro === $key ? 'active' : '' ?>" href="?tipo=<?= $key ?>"><?= $label ?></a>
    </li>
    <?php endforeach; ?>
  </ul>

  <?php if ($tipo_filtro === 'nueva'): ?>
  <ul class="nav nav-pills mb-3 ms-3">
    <?php
    $subtipos = ['todos', 'bachadora', 'esparcidor de sello', 'petrolizadora', 'tanque de almacén', 'planta de mezcla en frío'];
    foreach ($subtipos as $sub):
    ?>
    <li class="nav-item">
      <a class="nav-link <?= $subtipo_filtro === $sub ? 'active' : '' ?>" href="?tipo=nueva&subtipo=<?= urlencode($sub) ?>"><?= ucfirst($sub) ?></a>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>

  <!-- Búsqueda -->
  <form class="mb-3" method="GET">
    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_filtro) ?>">
    <?php if ($tipo_filtro === 'nueva'): ?>
      <input type="hidden" name="subtipo" value="<?= htmlspecialchars($subtipo_filtro) ?>">
    <?php endif; ?>
    <div class="input-group">
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
          <th>Año</th>
          <th>Ubicación</th>
          <th class="text-center-middle">Tipo</th>
          <th>Subtipo / Capacidad</th>
          <th style="min-width:160px;">Avance / Condición</th>
          <th style="min-width:135px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($resultado->num_rows > 0): ?>
        <?php while($fila = $resultado->fetch_assoc()):
          $tipo = strtolower($fila['tipo_maquinaria']);
          $subtipo = strtolower($fila['subtipo']);
          $capacidad = $fila['capacidad'] ?? null;
        ?>
        <tr>
          <td>
            <?php if (!empty($fila['imagen'])): ?>
              <img src="imagenes/<?= htmlspecialchars($fila['imagen']) ?>" class="imagen-thumbnail" alt="Imagen de <?= htmlspecialchars($fila['nombre']) ?>">
            <?php else: ?>Sin imagen<?php endif; ?>
          </td>
          <td><?= htmlspecialchars($fila['nombre']) ?></td>
          <td><?= htmlspecialchars($fila['modelo']) ?></td>
          <td><?= htmlspecialchars($fila['numero_serie']) ?></td>
          <td><?= htmlspecialchars($fila['anio']) ?></td>
          <td><?= htmlspecialchars($fila['ubicacion']) ?></td>
          <td class="text-center-middle">
            <?php
              if ($tipo === 'nueva') echo '<span class="badge-nueva">Producción Nueva</span>';
              elseif ($tipo === 'usada') echo 'Usada';
              elseif ($tipo === 'camion') echo '<span class="badge-camion">Camión</span>';
              else echo ucfirst($tipo);
            ?>
          </td>
          <td>
            <?php
              if ($subtipo && $capacidad) {
                if (in_array($subtipo, ['petrolizadora', 'bachadora', 'tanque de almacén'])) {
                  echo ucfirst($subtipo) . " " . number_format($capacidad, 0, '.', ',') . " litros";
                } elseif ($subtipo === 'planta de mezcla en frío') {
                  echo ucfirst($subtipo) . " " . number_format($capacidad, 0, '.', ',') . " toneladas";
                } else {
                  echo ucfirst($subtipo) . " " . number_format($capacidad, 0, '.', ',');
                }
              } elseif ($subtipo) {
                echo ucfirst($subtipo);
              } elseif ($capacidad) {
                echo number_format($capacidad, 0, '.', ',');
              } else {
                echo '-';
              }
            ?>
          </td>
          <td><?= mostrarAvance($fila, $tipo, $subtipo) ?></td>
          <td>
            <?php if (puede('editar', $usuario, $rol, $tipo)): ?>
              <a href="editar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil-square"></i></a>
            <?php endif; ?>
            <?php if (puede('eliminar', $usuario, $rol, $tipo)): ?>
              <a href="eliminar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')" title="Eliminar"><i class="bi bi-trash"></i></a>
            <?php endif; ?>
            <?php if (puede('recibo', $usuario, $rol, $tipo)): ?>
              <a href="acciones/recibo_unidad.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-warning" title="Editar recibo de unidad"><i class="bi bi-file-earmark-text"></i> Recibo</a>
            <?php endif; ?>
            <?php if (puede('editar', $usuario, $rol, $tipo) && $tipo === 'nueva' && in_array($subtipo, ['bachadora', 'esparcidor de sello', 'petrolizadora'])): ?>
              <a href="avance_<?= str_replace(' ', '_', $subtipo) ?>.php?id=<?= $fila['id'] ?>" class="btn btn-avance" title="Avance <?= ucfirst($subtipo) ?>"><i class="bi bi-bar-chart-line"></i> Avance</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="10" class="text-center text-warning">No se encontraron registros.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Lightbox -->
<div class="lightbox" id="lightbox"><img src="" id="img-lightbox"></div>

<script>
document.querySelectorAll('.imagen-thumbnail').forEach(img => {
    img.addEventListener('click', () => {
        document.getElementById('img-lightbox').src = img.src;
        document.getElementById('lightbox').classList.add('active');
    });
});
document.getElementById('lightbox').addEventListener('click', cerrarLightbox);
document.addEventListener('keydown', e => e.key === 'Escape' && cerrarLightbox());

function cerrarLightbox(){
    document.getElementById('img-lightbox').src = '';
    document.getElementById('lightbox').classList.remove('active');
}
</script>
</body>
</html>
