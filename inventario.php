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
    .container { max-width: 1140px; margin: auto; padding: 20px; }
    .card-maquinaria {
      background-color: #002b5c;
      border: 1px solid #004080;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.5);
      padding: 20px;
      font-size: 1.05rem;
    }
    h5 { font-size: 1.25rem; }
    .progress { background-color: #003366; }
    .progress-bar { background-color: #007bff; color: #ffffff; font-weight: bold; }
    .btn, .nav-link { border-radius: 10px; font-size: 1rem; padding: 10px 16px; }
    .btn-outline-primary { color: #007bff; border-color: #007bff; }
    .btn-outline-primary:hover { background-color: #007bff; color: white; }
    .btn-outline-success { color: #ffc107; border-color: #ffc107; }
    .btn-outline-success:hover { background-color: #ffc107; color: #001f3f; }
    .btn-outline-danger { color: #dc3545; border-color: #dc3545; }
    .btn-outline-danger:hover { background-color: #dc3545; color: white; }
    .btn-outline-secondary { color: #ffc107; border-color: #ffc107; }
    .btn-outline-secondary:hover { background-color: #ffc107; color: #001f3f; }
    .btn-success { background-color: #007bff; border-color: #007bff; }
    .btn-success:hover { background-color: #0056b3; }
    .etiqueta-nueva { background-color: #007bff; color: white; padding: 2px 8px; border-radius: 5px; font-size: 12px; }
    .nav-tabs .nav-link.active { background-color: #ffc107; color: #001f3f; }
    .nav-tabs .nav-link { color: #ffffff; }
    form input[type="text"] { font-size: 1rem; padding: 10px; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
    <h3 class="text-light">Inventario de Maquinaria</h3>
    <div class="d-flex gap-2">
      <a href="agregar_maquinaria.php" class="btn btn-success">+ Agregar Maquinaria</a>
  </button>

      <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
    </div>
  </div>
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link <?= $tipo_filtro == 'todas' ? 'active' : '' ?>" href="?tipo=todas">Todas</a></li>
    <li class="nav-item"><a class="nav-link <?= $tipo_filtro == 'nueva' ? 'active' : '' ?>" href="?tipo=nueva">Nueva</a></li>
    <li class="nav-item"><a class="nav-link <?= $tipo_filtro == 'usada' ? 'active' : '' ?>" href="?tipo=usada">Usada</a></li>
  </ul>
  <form class="mb-4" method="GET">
    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_filtro) ?>">
    <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, modelo o número de serie" value="<?= htmlspecialchars($busqueda) ?>">
  </form>
  <div class="row">
<?php while ($fila = $resultado->fetch_assoc()): ?>
  <?php
    $porc_avance = 0;
    $etapas_realizadas = [];
    $subtipo = strtolower(trim($fila['subtipo'] ?? ''));
    $tipo = strtolower(trim($fila['tipo_maquinaria'] ?? ''));

    if ($tipo == 'nueva') {
      if ($subtipo == 'esparcidor de sello') {
        $avance_result = $conn->query("SELECT etapa FROM avance_esparcidor WHERE id_maquinaria = {$fila['id']}");
        $etapas = [
          "Trazar, cortar, rolar y hacer ceja a tapas" => 5,
          "Trazar, cortar, rolar cuerpo" => 5,
          "Armar cuerpo" => 5,
          "Armar chasis" => 5,
          "Armar flux" => 5,
          "Colocar chasis y flux" => 5,
          "Colocar tapas y tubulares" => 5,
          "Colocar fibra de vidrio y lámina A.I" => 10,
          "Colocar accesorios" => 5,
          "Armar cajas negras y de controles" => 5,
          "Armar chasis" => 5,
          "Cortar, doblar y armar tolva" => 5,
          "Doblar, armar y colocar cabezal" => 5,
          "Doblar, armar, probar y colocar tanque de aceite" => 5,
          "Armar bomba" => 5,
          "Armar transportadores" => 3,
          "Pintar" => 2,
          "Colocar hidráulico y neumático" => 4,
          "Conectar eléctrico" => 3,
          "Colocar accesorios finales" => 2,
          "Prueba de equipo final" => 5
        ];
      } elseif ($subtipo == 'petrolizadora') {
        $avance_result = $conn->query("SELECT etapa FROM avance_petrolizadora WHERE id_maquinaria = {$fila['id']}");
        $etapas = [
          "Trazar,cortar,rolar y hacer ceja a tapas" => 5,
          "Trazar,cortar,rolar cuerpo" => 5,
          "Armar cuerpo" => 5,
          "Armar chasis" => 5,
          "Armar flux" => 5,
          "Colocar chasis y flux" => 5,
          "Colocar tapas y tubulares" => 5,
          "Colocar fibra de vidrio y lamina A.I" => 10,
          "Colocar accesorios tanque" => 5,
          "Armar y colocar barra" => 5,
          "Armar y colocar chasis p/bomba y motor" => 5,
          "Armar,alinear motor y bomba" => 5,
          "Montar alinear motor" => 5,
          "Armar tuberia interna y externa" => 5,
          "Alinear y colocar tuberias" => 5,
          "Colocar accesorios petrolizadora" => 5,
          "Pintura" => 5,
          "Intalacion electrica" => 5,
          "Probar y checar fugas" => 5
        ];
      } elseif ($subtipo == 'bachadora') {
        $avance_result = $conn->query("SELECT etapa FROM avance_bachadora WHERE id_maquinaria = {$fila['id']}");
        $etapas = [
          "Trazar,cortar,rolar y hacer ceja a tapas" => 5,
          "Trazar,cortar,rolar cuerpo" => 5,
          "Armar cuerpo" => 5,
          "Armar chasis" => 5,
          "Armar flux" => 5,
          "Colocar chasis y flux" => 5,
          "Colocar tapas y tubulares" => 5,
          "Colocar fibra de vidrio y lamina A.I" => 10,
          "Colocar accesorios" => 5,
          "Armar ejes" => 5,
          "Armar jalon" => 5,
          "Armar barra" => 5,
          "Armar chasis de bomba y motor" => 5,
          "Armar accesorios" => 5,
          "Montar bomba y motor" => 5,
          "Montar accesorios" => 5,
          "Pintar" => 3,
          "Instalacion electrica" => 2,
          "Checar y tapar fugas" => 5,
          "Probar equipo" => 5
        ];
      }
      if (!empty($avance_result)) {
        while ($row = $avance_result->fetch_assoc()) {
          $etapas_realizadas[] = $row['etapa'];
        }
        $peso_total = array_sum($etapas);
        $peso_completado = 0;
        foreach ($etapas as $nombre => $peso) {
          if (in_array($nombre, $etapas_realizadas)) $peso_completado += $peso;
        }
        $porc_avance = round(($peso_completado / $peso_total) * 100);
      }
    }
  ?>
<div class="col-md-4 mb-4">
  <div class="card card-maquinaria p-3 text-light">
    <?php if (!empty($fila['imagen'])): ?>
      <img src="imagenes/<?= $fila['imagen'] ?>" class="img-fluid rounded mb-2" style="max-height:200px; object-fit:contain;">
    <?php endif; ?>
    <h5><?= htmlspecialchars($fila['nombre']) ?></h5>
    <p class="mb-1"><strong>Modelo:</strong> <?= htmlspecialchars($fila['modelo']) ?></p>
    <p class="mb-1"><strong>Ubicación:</strong> <?= htmlspecialchars($fila['ubicacion']) ?></p>
    <p class="mb-1"><strong>Tipo:</strong> <?= htmlspecialchars($fila['tipo_maquinaria']) ?>
      <?php if ($fila['tipo_maquinaria'] == 'nueva'): ?>
        <span class="etiqueta-nueva">Nueva</span>
      <?php endif; ?>
    </p>
    <?php if (!empty($fila['subtipo'])): ?>
      <p class="mb-1"><strong>Subtipo:</strong> <?= htmlspecialchars($fila['subtipo']) ?></p>
    <?php endif; ?>

    <?php if ($tipo == 'nueva' && $porc_avance > 0): ?>
      <div class="progress mb-2" style="height: 25px;">
        <div class="progress-bar bg-success text-white" style="width: <?= $porc_avance ?>%;">
          <?= $porc_avance ?>%
        </div>
      </div>
    <?php endif; ?>

    <?php if ($fila['tipo_maquinaria'] === 'usada' && isset($fila['condicion_estimada'])): ?>
  <div class="progress mb-2" style="height: 25px;">
    <div class="progress-bar bg-warning text-dark" style="width: <?= $fila['condicion_estimada'] ?>%;">
      <?= $fila['condicion_estimada'] ?>%
    </div>
  </div>
  <?php if (!empty($fila['observaciones'])): ?>
    <div class="mt-2 p-2 bg-light border rounded text-dark small">
      <strong>📝 Observaciones:</strong><br>
      <?= nl2br(htmlspecialchars($fila['observaciones'])) ?>
    </div>
  <?php endif; ?>
<?php endif; ?>
         <div class="d-flex justify-content-between">
      <a href="editar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary">✏️ Editar</a>
      <a href="eliminar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta maquinaria?')">🗑️ Eliminar</a>
    </div>

    <?php if ($tipo == 'usada'): ?>
      <a href="acciones/recibo_unidad.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-secondary mt-2 w-100">📋 Recibo de Unidad</a>
    <?php elseif ($tipo == 'nueva'): ?>
      <?php
        $archivo_avance = match ($subtipo) {
          'esparcidor de sello' => 'avance_esparcidor',
          'petrolizadora' => 'avance_petrolizadora',
          'bachadora' => 'avance_bachadora',
          default => ''
        };
      ?>
      <?php if ($archivo_avance): ?>
        <a href="<?= $archivo_avance ?>.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-success mt-2 w-100">🛠️ Ver Avance</a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<?php endwhile; ?>
  </div> <!-- row -->
</div>
<!-- Tabla invisible solo para exportar -->
<table id="tablaExportable" style="display:none;">
  <tr>
    <th>ID</th><th>Nombre</th><th>Marca</th><th>Modelo</th><th>Ubicación</th>
    <th>Tipo</th><th>Subtipo</th><th>Condición Estimada</th><th>Avance Producción</th><th>Observaciones</th>
  </tr>
  <?php
  $resultado_export = $conn->query($sql);
  while ($fila = $resultado_export->fetch_assoc()):
  ?>
  <tr>
    <td><?= $fila['id'] ?></td>
    <td><?= htmlspecialchars($fila['nombre']) ?></td>
    <td><?= htmlspecialchars($fila['marca'] ?? '-') ?></td>
    <td><?= htmlspecialchars($fila['modelo']) ?></td>
    <td><?= htmlspecialchars($fila['ubicacion']) ?></td>
    <td><?= htmlspecialchars($fila['tipo_maquinaria']) ?></td>
    <td><?= htmlspecialchars($fila['subtipo'] ?? '-') ?></td>
    <td><?= ($fila['tipo_maquinaria'] === 'usada' && isset($fila['condicion_estimada'])) ? $fila['condicion_estimada'] . "%" : '-' ?></td>
    <?php
  $porc_avance = '';
  if ($fila['tipo_maquinaria'] === 'nueva') {
    $subtipo = strtolower(trim($fila['subtipo'] ?? ''));
    $etapas_realizadas = [];
    $etapas = [];

    if ($subtipo === 'esparcidor de sello') {
      $avance_result = $conn->query("SELECT etapa FROM avance_esparcidor WHERE id_maquinaria = {$fila['id']}");
      $etapas = [
        "Trazar, cortar, rolar y hacer ceja a tapas" => 5,
        "Trazar, cortar, rolar cuerpo" => 5,
        "Armar cuerpo" => 5,
        "Armar chasis" => 5,
        "Armar flux" => 5,
        "Colocar chasis y flux" => 5,
        "Colocar tapas y tubulares" => 5,
        "Colocar fibra de vidrio y lámina A.I" => 10,
        "Colocar accesorios" => 5,
        "Armar cajas negras y de controles" => 5,
        "Armar chasis" => 5,
        "Cortar, doblar y armar tolva" => 5,
        "Doblar, armar y colocar cabezal" => 5,
        "Doblar, armar, probar y colocar tanque de aceite" => 5,
        "Armar bomba" => 5,
        "Armar transportadores" => 3,
        "Pintar" => 2,
        "Colocar hidráulico y neumático" => 4,
        "Conectar eléctrico" => 3,
        "Colocar accesorios finales" => 2,
        "Prueba de equipo final" => 5
      ];
    } elseif ($subtipo === 'petrolizadora') {
      $avance_result = $conn->query("SELECT etapa FROM avance_petrolizadora WHERE id_maquinaria = {$fila['id']}");
      $etapas = [
        "Trazar,cortar,rolar y hacer ceja a tapas" => 5,
        "Trazar,cortar,rolar cuerpo" => 5,
        "Armar cuerpo" => 5,
        "Armar chasis" => 5,
        "Armar flux" => 5,
        "Colocar chasis y flux" => 5,
        "Colocar tapas y tubulares" => 5,
        "Colocar fibra de vidrio y lamina A.I" => 10,
        "Colocar accesorios tanque" => 5,
        "Armar y colocar barra" => 5,
        "Armar y colocar chasis p/bomba y motor" => 5,
        "Armar,alinear motor y bomba" => 5,
        "Montar alinear motor" => 5,
        "Armar tuberia interna y externa" => 5,
        "Alinear y colocar tuberias" => 5,
        "Colocar accesorios petrolizadora" => 5,
        "Pintura" => 5,
        "Intalacion electrica" => 5,
        "Probar y checar fugas" => 5
      ];
    } elseif ($subtipo === 'bachadora') {
      $avance_result = $conn->query("SELECT etapa FROM avance_bachadora WHERE id_maquinaria = {$fila['id']}");
      $etapas = [
        "Trazar,cortar,rolar y hacer ceja a tapas" => 5,
        "Trazar,cortar,rolar cuerpo" => 5,
        "Armar cuerpo" => 5,
        "Armar chasis" => 5,
        "Armar flux" => 5,
        "Colocar chasis y flux" => 5,
        "Colocar tapas y tubulares" => 5,
        "Colocar fibra de vidrio y lamina A.I" => 10,
        "Colocar accesorios" => 5,
        "Armar ejes" => 5,
        "Armar jalon" => 5,
        "Armar barra" => 5,
        "Armar chasis de bomba y motor" => 5,
        "Armar accesorios" => 5,
        "Montar bomba y motor" => 5,
        "Montar accesorios" => 5,
        "Pintar" => 3,
        "Instalacion electrica" => 2,
        "Checar y tapar fugas" => 5,
        "Probar equipo" => 5
      ];
    }

    if (!empty($avance_result)) {
      while ($et = $avance_result->fetch_assoc()) {
        $etapas_realizadas[] = $et['etapa'];
      }
      $peso_total = array_sum($etapas);
      $peso_completado = 0;
      foreach ($etapas as $etapa => $peso) {
        if (in_array($etapa, $etapas_realizadas)) $peso_completado += $peso;
      }
      $porc_avance = round(($peso_completado / $peso_total) * 100);
    }
  }
?>
    <td><?= ($fila['tipo_maquinaria'] === 'nueva' && $porc_avance !== '') ? $porc_avance . "%" : '-' ?></td>
    <td><?= ($fila['tipo_maquinaria'] === 'usada' && !empty($fila['observaciones'])) ? htmlspecialchars($fila['observaciones']) : '' ?></td>
</tr>
<?php endwhile; ?>
</table>
</body>
<button onclick="exportTableToExcel('tablaExportable', 'inventario_maquinaria')" 
  class="btn btn-warning shadow rounded-pill position-fixed" 
  style="bottom: 20px; right: 20px; z-index: 999;">
  📁 Exportar Excel
</button>
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
</html>
