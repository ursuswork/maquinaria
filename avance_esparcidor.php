<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) die("❌ ID inválido");

$maq = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maq) die("❌ Maquinaria no encontrada.");

$etapas_tanque = [
  "Trazar, cortar, rolar y hacer ceja a tapas" => 5,
  "Trazar, cortar, rolar cuerpo" => 5,
  "Armar cuerpo" => 5,
  "Armar chasis" => 5,
  "Armar flux" => 5,
  "Colocar chasis y flux" => 5,
  "Colocar tapas y tubulares" => 5,
  "Colocar fibra de vidrio y lámina A.I" => 10,
  "Colocar accesorios" => 5
];
$etapas_esparcidor = [
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
  "Prueba de equipo final" => 100
];
$secciones = ['ARMAR TANQUE' => $etapas_tanque, 'ESPARCIDOR' => $etapas_esparcidor];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa'])) {
  $etapa = $conn->real_escape_string($_POST['etapa']);
  $check = $conn->query("SELECT * FROM avance_esparcidor WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'");
  if ($check->num_rows > 0) {
    $conn->query("DELETE FROM avance_esparcidor WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'");
  } else {
    $conn->query("INSERT INTO avance_esparcidor (id_maquinaria, etapa, completado) VALUES ($id_maquinaria, '$etapa', 1)");
  }
  header("Location: avance_esparcidor.php?id=$id_maquinaria");
  exit;
}

$completadas = [];
$res = $conn->query("SELECT etapa FROM avance_esparcidor WHERE id_maquinaria = $id_maquinaria");
while ($r = $res->fetch_assoc()) {
  $completadas[] = $r['etapa'];
}

$peso_total = 0;
$peso_hecho = 0;
foreach ($secciones as $grupo) {
  foreach ($grupo as $nombre => $peso) {
    $peso_total += $peso;
    if (in_array($nombre, $completadas)) {
      $peso_hecho += $peso;
    }
  }
}
$porcentaje = $peso_total > 0 ? round(($peso_hecho / $peso_total) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Avance Técnico</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #121212; color: #ffffff; }
    .contenedor { max-width: 900px; margin: auto; padding: 20px; }
    .btn-etapa { margin-bottom: 10px; }
    .btn-completada {
      background-color: #28a745 !important;
      color: white !important;
      border-color: #28a745 !important;
    }
    .progress { height: 30px; background-color: #333; }
    .progress-bar { font-weight: bold; font-size: 16px; }
  </style>
</head>
<body>
<div class="contenedor">
  <h3 class="text-center mb-4">🛠️ Avance: <?= htmlspecialchars($maq['nombre']) ?></h3>
  <p><strong>Modelo:</strong> <?= htmlspecialchars($maq['modelo']) ?></p>
  <p><strong>Ubicación:</strong> <?= htmlspecialchars($maq['ubicacion']) ?></p>
  <p><strong>Subtipo:</strong> <?= htmlspecialchars($maq['subtipo']) ?></p>
  <hr>
  <div class="mb-4">
    <h5>Progreso General:</h5>
    <div class="progress">
      <div class="progress-bar bg-info" style="width: <?= $porcentaje ?>%;"><?= $porcentaje ?>%</div>
    </div>
  </div>
  <form method="POST">
    <?php foreach ($secciones as $titulo => $etapas): ?>
      <h5 class="mt-4"><?= $titulo ?></h5>
      <div class="row">
        <?php foreach ($etapas as $etapa => $peso): ?>
          <div class="col-md-6">
            <button type="submit" name="etapa" value="<?= $etapa ?>"
              class="btn w-100 btn-outline-light btn-etapa <?= in_array($etapa, $completadas) ? 'btn-completada' : '' ?>">
              <?= in_array($etapa, $completadas) ? "✅ $etapa ($peso%)" : "$etapa ($peso%)" ?>
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </form>
  <div class="mt-4 text-center">
    <a href="inventario.php" class="btn btn-outline-secondary">← Volver al Inventario</a>
  </div>
</div>
</body>
</html>
