<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include 'conexion.php';

$id_maquinaria = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_maquinaria <= 0) {
    die("❌ ID de maquinaria inválido.");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) {
    die("❌ Maquinaria no encontrada.");
}

$tipo_maquinaria = strtolower(trim($maquinaria['tipo_maquinaria'] ?? ''));
$subtipo = strtolower(trim($maquinaria['subtipo'] ?? ''));
if ($tipo_maquinaria !== 'nueva' || $subtipo !== 'petrolizadora') {
    die("⚠️ Solo disponible para maquinaria nueva de subtipo 'petrolizadora'.");
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
    "PETROLIZADORAS" => [
        "Armar y colocar barra" => 5,
        "Armar y colocar chasis p/bomba y motor" => 5,
        "Armar,alinear motor y bomba" => 5,
        "Montar alinear motor" => 5,
        "Armar tuberia interna y externa" => 5,
        "Alinear y colocar tuberias" => 5,
        "Colocar accesorios" => 5,
        "Pintura" => 5,
        "Intalacion electrica" => 5,
        "Probar y checar fugas" => 5,
    ]
];

$conn->query("
    CREATE TABLE IF NOT EXISTS avance_petrolizadora (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_maquinaria INT NOT NULL,
        etapa VARCHAR(255) NOT NULL,
        completado BOOLEAN DEFAULT FALSE,
        UNIQUE KEY (id_maquinaria, etapa)
    )
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $etapa = $_POST['etapa'] ?? '';
    $accion = $_POST['accion'] ?? '';
    if ($etapa && $accion) {
        if ($accion == 'marcar') {
            $conn->query("REPLACE INTO avance_petrolizadora (id_maquinaria, etapa, completado) VALUES ($id_maquinaria, '$etapa', 1)");
        } elseif ($accion == 'desmarcar') {
            $conn->query("DELETE FROM avance_petrolizadora WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'");
        }
    }
}

$completados = [];
$res = $conn->query("SELECT etapa FROM avance_petrolizadora WHERE id_maquinaria = $id_maquinaria AND completado = 1");
while ($row = $res->fetch_assoc()) {
    $completados[] = $row['etapa'];
}

$total = 0;
foreach ($etapas as $grupo) {
    foreach ($grupo as $nombre => $peso) {
        if (in_array($nombre, $completados)) {
            $total += $peso;
        }
    }
}
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
        background-color: #001f3f;
        color: #f1f1f1;
        font-family: 'Segoe UI', sans-serif;
    }
    .ficha {
        background: linear-gradient(to right, #012a5c, #023f7c);
        padding: 2rem;
        border-radius: 1.5rem;
        max-width: 1100px;
        margin: 2rem auto;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
    }
    h3, h5 {
        color: #ffc107;
        text-align: center;
    }
    .progress {
        height: 40px;
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
        border-radius: 1.2rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    .btn-toggle:hover {
        transform: scale(1.05);
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
    <h3>Avance de Petrolizadora</h3>
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
