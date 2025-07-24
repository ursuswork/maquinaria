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

// Etapas y pesos
$etapas = [
  "ARMAR TANQUE" => [
    "Trazar,cortar,rolar y hacer ceja a tapas" => 5,
    "Trazar,cortar,rolar cuerpo" => 5,
    "Armar cuerpo" => 5,
    "Armar chasis" => 5,
    "Armar flux" => 5,
    "Colocar chasis y flux" => 5,
    "Colocar tapas y tubulares" => 5,
    "Colocar fibra de vidrio y lamina A.I" => 10,
    "Colocar accesorios tanque" => 5,
  ],
  "PETROLIZADORA" => [
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
  ]
];

// Marcar / desmarcar etapa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa'])) {
  $etapa = $_POST['etapa'];
  $accion = $_POST['accion'];

  if ($accion === 'marcar') {
    $stmt = $conn->prepare("INSERT IGNORE INTO avance_petrolizadora (id_maquinaria, etapa) VALUES (?, ?)");
    $stmt->bind_param("is", $id_maquinaria, $etapa);
    $stmt->execute();
  } elseif ($accion === 'desmarcar') {
    $stmt = $conn->prepare("DELETE FROM avance_petrolizadora WHERE id_maquinaria = ? AND etapa = ?");
    $stmt->bind_param("is", $id_maquinaria, $etapa);
    $stmt->execute();
  }

  header("Location: avance_petrolizadora.php?id=$id_maquinaria");
  exit;
}

// Consulta maquinaria
$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) die("Maquinaria no encontrada");

// Etapas completadas
$completadas = [];
$res = $conn->query("SELECT etapa FROM avance_petrolizadora WHERE id_maquinaria = $id_maquinaria");
while ($row = $res->fetch_assoc()) {
  $completadas[] = $row['etapa'];
}

// Cálculo de porcentaje
$peso_total = 0;
$peso_actual = 0;

foreach ($etapas as $grupo) {
  foreach ($grupo as $etapa => $peso) {
    $peso_total += $peso;
    if (in_array($etapa, $completadas)) {
      $peso_actual += $peso;
    }
  }
}
$porcentaje = $peso_total > 0 ? round(($peso_actual / $peso_total) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Avance Petrolizadora</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
        background-color: #001933;
        color: #fff;
        font-family: 'Segoe UI', sans-serif;
    }
    .contenedor {
        max-width: 960px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #012a5c;
        border-radius: 1rem;
        box-shadow: 0 0 20px rgba(0,0,0,0.6);
    }
    h3, h5 {
        text-align: center;
        color: #ffc107;
    }
    .progress {
        height: 30px;
        border-radius: 15px;
        background-color: #2c3e50;
        margin-bottom: 2rem;
    }
    .progress-bar {
        background-color: #ffc107;
        font-weight: bold;
        font-size: 1.1rem;
    }
    .btn-toggle {
        width: 100%;
        margin: 6px 0;
        border-radius: 1rem;
        font-size: 0.95rem;
        padding: 0.6rem 1rem;
        text-align: center;
        border: 1px solid #204060;
    }
    .completed {
        background-color: #28a745 !important;
        color: white !important;
        font-weight: bold;
    }
    .btn-outline-light {
        color: white;
        border: 1px solid #ccc;
    }
    .btn-outline-light:hover {
        background-color: #ffc107;
        color: black;
        font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="contenedor">
    <h3>Avance Petrolizadora</h3>
    <h5><?= htmlspecialchars($maquinaria['nombre']) ?> (Modelo: <?= htmlspecialchars($maquinaria['modelo']) ?>)</h5>

    <div class="progress">
      <div class="progress-bar" role="progressbar" style="width: <?= $porcentaje ?>%;" aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100"><?= $porcentaje ?>%</div>
    </div>

    <?php foreach ($etapas as $grupo => $pasos): ?>
      <h5 class="text-center text-info mt-4"><?= $grupo ?></h5>
      <?php foreach ($pasos as $etapa => $peso):
        $ya = in_array($etapa, $completadas);
      ?>
        <form method="POST" class="mb-2">
          <input type="hidden" name="etapa" value="<?= htmlspecialchars($etapa) ?>">
          <input type="hidden" name="accion" value="<?= $ya ? 'desmarcar' : 'marcar' ?>">
          <button type="submit" class="btn btn-toggle <?= $ya ? 'completed' : 'btn-outline-light' ?>">
            <?= $etapa ?> (<?= $peso ?>%)
          </button>
        </form>
      <?php endforeach; ?>
    <?php endforeach; ?>

    <div class="text-center mt-4">
      <a href="inventario.php" class="btn btn-outline-light">← Volver al Inventario</a>
    </div>
  </div>
</body>
</html>