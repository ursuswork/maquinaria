<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$id_maquinaria = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_maquinaria <= 0) {
  die("ID de maquinaria no válido");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) {
  die("❌ Maquinaria no encontrada.");
}

$etapas_arma = [
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
];

$etapas_bachadora = [
  "Armar barra" => 5,
  "Armar chasis de bomba y motor" => 5,
  "Armar accesorios" => 5,
  "Montar bomba y motor" => 5,
  "Montar accesorios" => 5,
  "Pintar" => 3,
  "Instalacion electrica" => 2,
  "Checar y tapar fugas" => 5,
  "Probar equipo" => 5,
];

$etapas = $etapas_arma + $etapas_bachadora;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa'])) {
  $etapa = $conn->real_escape_string($_POST['etapa']);
  $existe = $conn->query("SELECT 1 FROM avance_bachadora WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'");
  if ($existe->num_rows == 0) {
    $conn->query("INSERT INTO avance_bachadora (id_maquinaria, etapa) VALUES ($id_maquinaria, '$etapa')");
  } else {
    $conn->query("DELETE FROM avance_bachadora WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'");
  }
}

$realizadas = [];
$res = $conn->query("SELECT etapa FROM avance_bachadora WHERE id_maquinaria = $id_maquinaria");
while ($row = $res->fetch_assoc()) {
  $realizadas[] = $row['etapa'];
}

$peso_total = array_sum($etapas);
$peso_completado = 0;
foreach ($etapas as $nombre => $peso) {
  if (in_array($nombre, $realizadas)) {
    $peso_completado += $peso;
  }
}
$porcentaje = round(($peso_completado / $peso_total) * 100);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Avance Bachadora</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #001f3f;
      color: white;
    }
    .container {
      max-width: 1100px;
      margin: 2rem auto;
      padding: 2rem;
      background-color: #012a5c;
      border-radius: 1rem;
    }
    h3, h5 {
      color: #ffc107;
    }
    .progress {
      height: 35px;
      background-color: #2c3e50;
      border-radius: 1rem;
      overflow: hidden;
    }
    .progress-bar {
      background-color: #ffc107 !important;
      font-weight: bold;
      font-size: 1.2rem;
    }
    .btn-toggle {
      text-align: center;
      color: white;
      background-color: #012a5c;

      width: 100%;
      margin-bottom: 10px;
      border-radius: 0.5rem;
      background-color: white;
      color: black;
      font-weight: bold;
    }
    .completed {
      background-color: #28a745 !important;
      color: white !important;
    }
    .btn-toggle:hover {
      opacity: 0.9;
    }
  </style>
</head>
<body>
<div class="container">
  <h3 class="text-center">Avance de Bachadora</h3>
  <h5 class="text-center"><?= htmlspecialchars($maquinaria['nombre']) ?> (Modelo: <?= htmlspecialchars($maquinaria['modelo']) ?>)</h5>

  <div class="my-4">
    <div class="progress">
      <div class="progress-bar" role="progressbar" style="width: <?= $porcentaje ?>%;" aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100"><?= $porcentaje ?>%</div>
    </div>
  </div>

  <form method="post">
    <h5>ARMAR TANQUE</h5>
    <?php foreach ($etapas_arma as $etapa => $peso): ?>
      <button type="submit" name="etapa" value="<?= htmlspecialchars($etapa) ?>" class="btn btn-toggle <?= in_array($etapa, $realizadas) ? 'completed' : '' ?>">
        <?= htmlspecialchars($etapa) ?> (<?= $peso ?>%)
      </button>
    <?php endforeach; ?>

    <h5 class="mt-4">BACHADORA</h5>
    <?php foreach ($etapas_bachadora as $etapa => $peso): ?>
      <button type="submit" name="etapa" value="<?= htmlspecialchars($etapa) ?>" class="btn btn-toggle <?= in_array($etapa, $realizadas) ? 'completed' : '' ?>">
        <?= htmlspecialchars($etapa) ?> (<?= $peso ?>%)
      </button>
    <?php endforeach; ?>
  </form>

  <div class="text-center mt-4">
    <a href="inventario.php" class="btn btn-outline-light">← Volver al Inventario</a>
  </div>
</div>
</body>
</html>
