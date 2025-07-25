<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$tipo_filtro = $_GET['tipo'] ?? 'todas';

$sql = "
  SELECT m.*, r.condicion_estimada, r.observaciones 
  FROM maquinaria m
  LEFT JOIN recibo_unidad r ON m.id = r.id_maquinaria
";
if (!empty($busqueda)) {
  $sql .= " WHERE (m.nombre LIKE '%$busqueda%' OR m.modelo LIKE '%$busqueda%' OR m.numero_serie LIKE '%$busqueda%')";
}
if (strtolower(trim($tipo_filtro)) === 'produccion nueva') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "LOWER(TRIM(m.tipo_maquinaria)) = 'nueva'";
} elseif (strtolower(trim($tipo_filtro)) === 'usada') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "LOWER(TRIM(m.tipo_maquinaria)) = 'usada'";
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
    .table thead th { background-color: #004080; color: #ffffff; border: none; }
    .table tbody tr { border-bottom: 1px solid #004f8c; }
    .table tbody tr:nth-child(even) { background-color: #003366; }
    .table tbody tr:nth-child(odd) { background-color: #002b5c; }
    .badge-nueva { background-color: #ffc107; color: #001f3f; padding: 6px 12px; border-radius: 6px; font-size: 0.9rem; font-weight: bold; }
    .progress { height: 22px; border-radius: 20px; background-color: #002b5c; overflow: hidden; }
    .progress-bar { font-weight: bold; background-color: #ffcc00 !important; color: black; border-radius: 20px; transition: width 0.4s ease; }
    .btn-flotante {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #ffc107;
      color: #001f3f;
      padding: 12px 20px;
      border: none;
      border-radius: 30px;
      font-weight: bold;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
      z-index: 1000;
    }
    .imagen-thumbnail {
      max-width: 80px;
      max-height: 80px;
      cursor: pointer;
    }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h3>Inventario de Maquinaria</h3>
    <div>
      <a href="agregar_maquinaria.php" class="btn btn-primary">+ Agregar Maquinaria</a>
      <a href="logout.php" class="btn btn-secondary">Cerrar sesión</a>
    </div>
  </div>
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link <?= $tipo_filtro == 'todas' ? 'active' : '' ?>" href="?tipo=todas">Todas</a></li>
    <li class="nav-item"><a class="nav-link <?= strtolower($tipo_filtro) == 'produccion nueva' ? 'active' : '' ?>" href="?tipo=produccion nueva">Producción Nueva</a></li>
    <li class="nav-item"><a class="nav-link <?= strtolower($tipo_filtro) == 'usada' ? 'active' : '' ?>" href="?tipo=usada">Usada</a></li>
  </ul>
  <form class="mb-3" method="GET">
    <div class="input-group">
      <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_filtro) ?>">
      <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, modelo o número de serie" value="<?= htmlspecialchars($busqueda) ?>">
    </div>
  </form>
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
<?php while ($fila = $resultado->fetch_assoc()):
  $subtipo = mb_strtolower(trim($fila['subtipo']));
  $avance = 0;
  if (strtolower(trim($fila['tipo_maquinaria'])) === 'nueva') {
    if ($subtipo === 'bachadora') {
      $q = $conn->query("SELECT SUM(CASE WHEN etapa IS NOT NULL THEN
        CASE etapa
          WHEN 'Trazar,cortar,rolar y hacer ceja a tapas' THEN 5
          WHEN 'Trazar,cortar,rolar cuerpo' THEN 5
          WHEN 'Armar cuerpo' THEN 5
          WHEN 'Armar chasis' THEN 5
          WHEN 'Armar flux' THEN 5
          WHEN 'Colocar chasis y flux' THEN 5
          WHEN 'Colocar tapas y tubulares' THEN 5
          WHEN 'Colocar fibra de vidrio y lamina A.I' THEN 10
          WHEN 'Colocar accesorios' THEN 5
          WHEN 'Armar ejes' THEN 5
          WHEN 'Armar jalon' THEN 5
          WHEN 'Armar barra' THEN 5
          WHEN 'Armar chasis de bomba y motor' THEN 5
          WHEN 'Armar accesorios' THEN 5
          WHEN 'Montar bomba y motor' THEN 5
          WHEN 'Montar accesorios' THEN 5
          WHEN 'Pintar' THEN 3
          WHEN 'Instalacion electrica' THEN 2
          WHEN 'Checar y tapar fugas' THEN 5
          WHEN 'Probar equipo' THEN 5
          ELSE 0 END ELSE 0 END) AS avance FROM avance_bachadora WHERE id_maquinaria = {$fila['id']}");
      $avance = ($q && $r = $q->fetch_assoc()) ? intval($r['avance']) : 0;
    } elseif ($subtipo === 'esparcidor') {
      $q = $conn->query("SELECT SUM(peso) as avance FROM avance_esparcidor WHERE id_maquinaria = {$fila['id']}");
      $avance = ($q && $r = $q->fetch_assoc()) ? intval($r['avance']) : 0;
    } elseif ($subtipo === 'petrolizadora') {
      $q = $conn->query("SELECT SUM(peso) as avance FROM avance_petrolizadora WHERE id_maquinaria = {$fila['id']}");
      $avance = ($q && $r = $q->fetch_assoc()) ? intval($r['avance']) : 0;
    }
  }
?>
<tr>
  <td><?php if (!empty($fila['imagen'])): ?><img src="imagenes/<?= htmlspecialchars($fila['imagen']) ?>" class="imagen-thumbnail"><?php else: ?>Sin imagen<?php endif; ?></td>
  <td><?= htmlspecialchars($fila['nombre']) ?></td>
  <td><?= htmlspecialchars($fila['modelo']) ?></td>
  <td><?= htmlspecialchars($fila['ubicacion']) ?></td>
  <td><?= strtolower(trim($fila['tipo_maquinaria'])) == 'nueva' ? '<span class="badge-nueva">Nueva</span>' : 'Usada' ?></td>
  <td><?= htmlspecialchars($fila['subtipo']) ?></td>
  <td>
    <?php if (strtolower(trim($fila['tipo_maquinaria'])) == 'usada'): ?>
      <div class='progress'><div class='progress-bar' style='width: <?= intval($fila['condicion_estimada']) ?>%'><?= intval($fila['condicion_estimada']) ?>%</div></div>
    <?php else: ?>
      <div class='progress'><div class='progress-bar' style='width: <?= $avance ?>%'><?= $avance ?>%</div></div>
    <?php endif; ?>
  </td>
  <td>
    <a href="editar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil-square"></i></a>
    <a href="eliminar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger me-1" onclick="return confirm('¿Eliminar maquinaria?')"><i class="bi bi-trash"></i></a>
    <?php
      $mapa_avance = ['bachadora'=>'avance_bachadora.php','esparcidor'=>'avance_esparcidor.php','petrolizadora'=>'avance_petrolizadora.php'];
      if (strtolower(trim($fila['tipo_maquinaria'])) == 'nueva' && isset($mapa_avance[$subtipo])):
    ?>
    <a href="<?= $mapa_avance[$subtipo] ?>?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-bar-chart-line"></i></a>
    <?php endif; ?>
  </td>
</tr>
<?php endwhile; ?>
    </tbody>
  </table>
</div>
<a href="exportar_excel.php" class="btn-flotante">📥 Exportar Excel</a>
</body>
</html>