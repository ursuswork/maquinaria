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
    "Colocar accesorios" => 5,
  ],
  "ESPARCIDOR" => [
    "Armar cajas negras y de controles" => 5,
    "Armar chasis" => 5,
    "Cortar, doblar y armar tolva" => 5,
    "Doblar, armar y colocar cabezal" => 5,
    "Doblar,armar,probar y colocar tanque de aceite" => 5,
    "Armar bomba" => 5,
    "Armar transportadores" => 3,
    "Pintar" => 2,
    "Colocar hidráulico y neumático" => 4,
    "Conectar eléctrico" => 3,
    "Colocar accesorios finales" => 3,
    "Prueba de equipo final" => 5
  ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa'])) {
  $etapa = $_POST['etapa'];
  $accion = $_POST['accion'];

  if ($accion === 'marcar') {
    $stmt = $conn->prepare("INSERT IGNORE INTO avance_esparcidor (id_maquinaria, etapa) VALUES (?, ?)");
    $stmt->bind_param("is", $id_maquinaria, $etapa);
    $stmt->execute();
  } elseif ($accion === 'desmarcar') {
    $stmt = $conn->prepare("DELETE FROM avance_esparcidor WHERE id_maquinaria = ? AND etapa = ?");
    $stmt->bind_param("is", $id_maquinaria, $etapa);
    $stmt->execute();
  }

  header("Location: avance_esparcidor.php?id=$id_maquinaria");
  exit;
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) die("Maquinaria no encontrada");

$completadas = [];
$res = $conn->query("SELECT etapa FROM avance_esparcidor WHERE id_maquinaria = $id_maquinaria");
while ($row = $res->fetch_assoc()) {
  $completadas[] = $row['etapa'];
}

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
  <title>Avance Esparcidor</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #001f3f;
      color: white;
      font-family: 'Segoe UI', sans-serif;
      font-size: 1rem;
    }
    .ficha {
      background-color: #012a5c;
      padding: 2rem;
      border-radius: 1.5rem;
      max-width: 900px;
      margin: 2rem auto;
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    }
    h3, h5 {
      color: #ffc107;
      text-align: center;
      font-size: 1.4rem;
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
      width: 90%;
      margin: 6px auto;
      display: block;
      border-radius: 1rem;
      font-size: 1rem;
      padding: 10px;
      text-align: center;
      color: white;
      background-color: #012a5c;
      border: 2px solid #004080;
      box-shadow: 0 2px 6px rgba(0,0,0,0.3);
      transition: all 0.2s ease-in-out;
    }
    .btn-toggle:hover {
      background-color: #003366;
      border-color: #0059b3;
    }
    .completed {
      background-color: #28a745 !important;
      color: white !important;
      font-weight: bold;
      border: 2px solid #1c7c35 !important;
    }
  </style>
</head>
<body>
  <div class="ficha">
    <h3>Avance Esparcidor</h3>
    <h5><?= htmlspecialchars($maquinaria['nombre']) ?> (Modelo: <?= htmlspecialchars($maquinaria['modelo']) ?>)</h5>

    <div class="progress">
      <div class="progress-bar" role="progressbar" style="width: <?= $porcentaje ?>%;" aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100"><?= $porcentaje ?>%</div>
    </div>

    <?php foreach ($etapas as $grupo => $pasos): ?>
      <h5 class="text-center text-info mt-4"> <?= $grupo ?> </h5>
      <?php foreach ($pasos as $etapa => $peso):
        $ya = in_array($etapa, $completadas);
      ?>
        <form method="POST" class="mb-1">
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
