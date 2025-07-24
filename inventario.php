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
  $sql .= " WHERE (m.nombre LIKE '%$busqueda%' OR m.modelo LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'nueva') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "m.tipo_maquinaria = 'nueva'";
} elseif ($tipo_filtro === 'usada') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "m.tipo_maquinaria = 'usada'";
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
  <style>
    body { background-color: #001f3f; color: #ffffff; }
    .table thead th { background-color: #004080; color: #ffffff; }
    .table tbody tr:nth-child(even) { background-color: #003366; }
    .table tbody tr:nth-child(odd) { background-color: #002b5c; }
    .badge-nueva { background-color: #ffc107; color: #001f3f; padding: 5px 10px; border-radius: 5px; }
    .progress { height: 20px; }
    .progress-bar { font-weight: bold; }
    .nav-tabs .nav-link.active { background-color: #ffc107; color: #001f3f; }
    .nav-tabs .nav-link { color: #ffffff; }
    .btn-outline-primary { color: #0074cc; border-color: #0074cc; }
    .btn-outline-primary:hover { background-color: #0074cc; color: white; }
    .btn-outline-danger:hover { background-color: #dc3545; color: white; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
    <h3 class="text-white">Inventario de Maquinaria</h3>
    <div class="d-flex gap-2">
      <a href="agregar_maquinaria.php" class="btn btn-primary">+ Agregar Maquinaria</a>
      <a href="logout.php" class="btn btn-secondary">Cerrar sesión</a>
    </div>
  </div>
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link <?= $tipo_filtro == 'todas' ? 'active' : '' ?>" href="?tipo=todas">Todas</a></li>
    <li class="nav-item"><a class="nav-link <?= $tipo_filtro == 'nueva' ? 'active' : '' ?>" href="?tipo=nueva">Producción Nueva</a></li>
    <li class="nav-item"><a class="nav-link <?= $tipo_filtro == 'usada' ? 'active' : '' ?>" href="?tipo=usada">Usada</a></li>
  </ul>
  <form class="mb-3" method="GET">
    <div class="input-group">
      <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_filtro) ?>">
      <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, modelo o número de serie" value="<?= htmlspecialchars($busqueda) ?>">
    </div>
  </form>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Modelo</th>
        <th>Ubicación</th>
        <th>Tipo</th>
        <th>Subtipo</th>
        <th>Avance/Condición</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($fila = $resultado->fetch_assoc()): ?>
      <?php
        $tipo = strtolower(trim($fila['tipo_maquinaria'] ?? ''));
        $subtipo = strtolower(trim($fila['subtipo'] ?? ''));
        $porc_avance = 0;
        $etapas_realizadas = [];
        $etapas = [];
        if ($tipo === 'produccion nueva') {
          $avance_tabla = '';
          switch ($subtipo) {
            case 'esparcidor de sello': $avance_tabla = 'avance_esparcidor'; break;
            case 'petrolizadora': $avance_tabla = 'avance_petrolizadora'; break;
            case 'bachadora': $avance_tabla = 'avance_bachadora'; break;
          }
          if ($avance_tabla) {
            $avance_result = $conn->query("SELECT etapa FROM $avance_tabla WHERE id_maquinaria = {$fila['id']}");
            if ($avance_result) {
              while ($row = $avance_result->fetch_assoc()) $etapas_realizadas[] = $row['etapa'];
              $peso_total = count($etapas_realizadas) * 5;
              $peso_completado = count($etapas_realizadas) * 5;
              $porc_avance = $peso_total > 0 ? round(($peso_completado / $peso_total) * 100) : 0;
            }
          }
        }
      ?>
      <tr>
        <td><?= htmlspecialchars($fila['nombre']) ?></td>
        <td><?= htmlspecialchars($fila['modelo']) ?></td>
        <td><?= htmlspecialchars($fila['ubicacion']) ?></td>
        <td>
          <?= $tipo === 'nueva' ? '<span class="badge-nueva">Producción Nueva</span>' : 'Usada' ?>
        </td>
        <td><?= htmlspecialchars($fila['subtipo'] ?? '-') ?></td>
        <td>
          <?php if ($tipo === 'nueva' && $porc_avance > 0): ?>
            <div class="progress">
              <div class="progress-bar bg-success" style="width: <?= $porc_avance ?>%;">
                <?= $porc_avance ?>%
              </div>
            </div>
          <?php elseif ($tipo === 'usada' && isset($fila['condicion_estimada'])): ?>
            <div class="progress">
              <div class="progress-bar bg-warning text-dark" style="width: <?= $fila['condicion_estimada'] ?>%;">
                <?= $fila['condicion_estimada'] ?>%
              </div>
            </div>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
        <td>
          <a href="editar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
          <a href="eliminar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta maquinaria?')">Eliminar</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <button onclick="exportTableToExcel('tablaExportable', 'inventario_maquinaria')" 
    class="btn btn-warning shadow rounded-pill position-fixed" 
    style="bottom: 20px; right: 20px; z-index: 999;">
    📁 Exportar Excel
  </button>
  <table id="tablaExportable" style="display:none;">
    <thead>
      <tr>
        <th>ID</th><th>Nombre</th><th>Modelo</th><th>Ubicación</th><th>Tipo</th><th>Subtipo</th><th>Condición</th>
      </tr>
    </thead>
    <tbody>
    <?php $export_result = $conn->query($sql);
    while ($row = $export_result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['nombre']) ?></td>
        <td><?= htmlspecialchars($row['modelo']) ?></td>
        <td><?= htmlspecialchars($row['ubicacion']) ?></td>
        <td><?= $row['tipo_maquinaria'] === 'nueva' ? 'Producción Nueva' : 'Usada' ?></td>
        <td><?= htmlspecialchars($row['subtipo']) ?></td>
        <td><?= $row['tipo_maquinaria'] === 'usada' ? $row['condicion_estimada'] . '%' : '-' ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
<script>
function exportTableToExcel(tableID, filename = '') {
  const dataType = 'application/vnd.ms-excel';
  const table = document.getElementById(tableID);
  let tableHTML = '\uFEFF' + table.outerHTML;

  const fecha = new Date().toISOString().slice(0, 10);
  filename = filename ? `${filename}_${fecha}.xls` : `inventario_${fecha}.xls`;

  const downloadLink = document.createElement("a");
  document.body.appendChild(downloadLink);

  if (navigator.msSaveOrOpenBlob) {
    const blob = new Blob([tableHTML], { type: dataType });
    navigator.msSaveOrOpenBlob(blob, filename);
  } else {
    downloadLink.href = 'data:' + dataType + ',' + encodeURIComponent(tableHTML);
    downloadLink.download = filename;
    downloadLink.click();
  }

  document.body.removeChild(downloadLink);
}
</script>
</body>
</html>
