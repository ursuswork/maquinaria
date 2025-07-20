<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$tipo_filtro = $_GET['tipo'] ?? 'todas';

$sql = "SELECT * FROM maquinaria";
if (!empty($busqueda)) {
  $sql .= " WHERE (nombre LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%' OR numero_serie LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'nueva') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "tipo_maquinaria = 'nueva'";
} elseif ($tipo_filtro === 'usada') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "tipo_maquinaria = 'usada'";
}
$sql .= " ORDER BY tipo_maquinaria ASC, nombre ASC";
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
    body { background-color: #121212; color: #ffffff; }
    .card-maquinaria { background-color: #1e1e1e; border: 1px solid #333; border-radius: 15px; box-shadow: 0 0 10px rgba(0,0,0,0.5); }
    .progress { background-color: #333; }
    .progress-bar { font-weight: bold; }
    .btn, .nav-link { border-radius: 10px; }
    .etiqueta-nueva { background-color: #007bff; color: white; padding: 2px 8px; border-radius: 5px; font-size: 12px; }
    .nav-tabs .nav-link.active { background-color: #007bff; color: white; }
    .nav-tabs .nav-link { color: #ccc; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h3 class="text-light">Inventario de Maquinaria</h3>
    <a href="agregar_maquinaria.php" class="btn btn-success">+ Agregar Maquinaria</a>
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

    <?php
    $porc_avance = 0;
    if (strtolower(trim($fila['tipo_maquinaria'])) == 'nueva' && strtolower(trim($fila['subtipo'])) == 'esparcidor de sello') {
      $avance_result = $conn->query("SELECT etapa FROM avance_esparcidor WHERE id_maquinaria = {$fila['id']}");
      $etapas_realizadas = [];
      while ($row = $avance_result->fetch_assoc()) { $etapas_realizadas[] = $row['etapa']; }
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
      $completadas = $etapas_realizadas;
      $peso_total = array_sum($etapas);
      $peso_completado = 0;
      foreach ($etapas as $nombre => $peso) {
        if (in_array($nombre, $completadas)) $peso_completado += $peso;
      }
      $porc_avance = round(($peso_completado / $peso_total) * 100);
    }
    ?>

    <?php if ($porc_avance > 0): ?>
      <div class="progress mb-2" style="height: 25px;">
        <div class="progress-bar bg-info" style="width: <?= $porc_avance ?>%;"><?= $porc_avance ?>%</div>
      </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between">
      <a href="editar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary">✏️ Editar</a>
      <a href="eliminar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta maquinaria?')">🗑️ Eliminar</a>
    </div>

    <?php if (strtolower(trim($fila['tipo_maquinaria'])) == 'usada'): ?>
      <a href="acciones/recibo_unidad.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-secondary mt-2 w-100">📋 Recibo de Unidad</a>
    <?php endif; ?>
    <?php if (strtolower(trim($fila['tipo_maquinaria'])) == 'nueva' && strtolower(trim($fila['subtipo'])) == 'esparcidor de sello'): ?>
      <a href="avance_esparcidor.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-success mt-2 w-100">🛠️ Ver Avance</a>
    <?php endif; ?>
  </div>
</div>
<?php endwhile; ?>
</div>
</body>
</html>