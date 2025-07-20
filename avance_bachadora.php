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

// Lista de etapas
$etapas = [
  // Armar Tanque (60%)
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

  // Bachadora (40%)
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

// Guardar etapa si se marca
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa'])) {
  $etapa = $conn->real_escape_string($_POST['etapa']);
  $existe = $conn->query("SELECT 1 FROM avance_bachadora WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'");
  if ($existe->num_rows == 0) {
    $conn->query("INSERT INTO avance_bachadora (id_maquinaria, etapa) VALUES ($id_maquinaria, '$etapa')");
  } else {
    $conn->query("DELETE FROM avance_bachadora WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'");
  }
}

// Obtener etapas completadas
$realizadas = [];
$res = $conn->query("SELECT etapa FROM avance_bachadora WHERE id_maquinaria = $id_maquinaria");
while ($row = $res->fetch_assoc()) {
  $realizadas[] = $row['etapa'];
}

// Calcular avance
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #121212; color: #fff; }
    .etapa-btn {
      width: 100%; margin-bottom: 10px;
    }
    .completada {
      background-color: #198754;
      color: white;
      font-weight: bold;
    }
  </style>
</head>
<body class="container py-4">
  <h3 class="mb-4">Avance: Bachadora</h3>
  <div class="progress mb-4" style="height: 30px;">
    <div class="progress-bar bg-info" style="width: <?= $porcentaje ?>%;">
      <?= $porcentaje ?>%
    </div>
  </div>
  <form method="post">
    <?php foreach ($etapas as $etapa => $peso): ?>
      <button type="submit" name="etapa" value="<?= htmlspecialchars($etapa) ?>" class="btn etapa-btn <?= in_array($etapa, $realizadas) ? 'completada' : 'btn-outline-light' ?>">
        <?= htmlspecialchars($etapa) ?> (<?= $peso ?>%)
      </button>
    <?php endforeach; ?>
  </form>
  <a href="inventario.php" class="btn btn-secondary mt-4">Volver al Inventario</a>
</body>
</html>