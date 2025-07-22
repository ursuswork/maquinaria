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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa'])) {
  $etapa = $_POST['etapa'];
  $accion = $_POST['accion'];

  if ($accion === 'agregar') {
    $stmt = $conn->prepare("INSERT IGNORE INTO avance_petrolizadora (id_maquinaria, etapa) VALUES (?, ?)");
    $stmt->bind_param("is", $id_maquinaria, $etapa);
    $stmt->execute();
  } elseif ($accion === 'eliminar') {
    $stmt = $conn->prepare("DELETE FROM avance_petrolizadora WHERE id_maquinaria = ? AND etapa = ?");
    $stmt->bind_param("is", $id_maquinaria, $etapa);
    $stmt->execute();
  }
  header("Location: avance_petrolizadora.php?id=" . $id_maquinaria);
  exit;
}

$realizadas = [];
$res = $conn->query("SELECT etapa FROM avance_petrolizadora WHERE id_maquinaria = $id_maquinaria");
while ($row = $res->fetch_assoc()) {
  $realizadas[] = $row['etapa'];
}

$peso_total = array_sum($etapas);
$peso_actual = 0;
foreach ($etapas as $etapa => $peso) {
  if (in_array($etapa, $realizadas)) {
    $peso_actual += $peso;
  }
}
$porcentaje = round(($peso_actual / $peso_total) * 100);
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
        background: linear-gradient(135deg, #001933, #004080);
        color: #f1f1f1;
        font-family: 'Segoe UI', sans-serif;
    }
    .ficha {
        background-color: #012a5c;
        padding: 2.5rem;
        border-radius: 1.5rem;
        max-width: 1100px;
        margin: 2rem auto;
        box-shadow: 0 8px 20px rgba(0,0,0,0.5);
    }
    h3, h5 {
        color: #ffc107;
        text-align: center;
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
        min-width: 280px;
        margin: 10px auto;
        display: block;
        border-radius: 1rem;
        font-size: 0.95rem;
    }
    .completed {
        background-color: #0056b3 !important;
        color: white !important;
        font-weight: bold;
    }
    .btn-outline-light:hover {
        background-color: #ffc107;
        color: #000;
        font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="ficha">
    <h3>Avance de Esparcidor de Sello</h3>
    <h5><?= htmlspecialchars($maquinaria['nombre']) ?> (Modelo: <?= htmlspecialchars($maquinaria['modelo']) ?>)</h5>

    <div class="mb-4">
      <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: <?= $total ?>%;" aria-valuenow="<?= $total ?>" aria-valuemin="0" aria-valuemax="100"><?= $total ?>%</div>
      </div>
    </div>

    <?php foreach ($etapas as $seccion => $items): ?>
      <h5 class="mt-4 text-info text-center"><?= $seccion ?></h5>
      <div class="row justify-content-center">
        <?php foreach ($items as $etapa => $peso): 
          $ya = in_array($etapa, $completados);
        ?>
          <div class="col-md-6 col-lg-4">
            <form method="POST" class="text-center">
              <input type="hidden" name="etapa" value="<?= htmlspecialchars($etapa) ?>">
              <input type="hidden" name="accion" value="<?= $ya ? 'desmarcar' : 'marcar' ?>">
              <button type="submit" class="btn btn-toggle btn-sm <?= $ya ? 'completed' : 'btn-outline-light' ?>">
                <?= $etapa ?> (<?= $peso ?>%)
              </button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <div class="text-center mt-4">
      <a href="inventario.php" class="btn btn-outline-light">← Volver al Inventario</a>
    </div>
  </div>
</body>
</html>