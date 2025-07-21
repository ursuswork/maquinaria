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

if (strtolower(trim($maquinaria['tipo_maquinaria'])) !== 'nueva' || strtolower(trim($maquinaria['subtipo'])) !== 'esparcidor de sello') {
    die("⚠️ Solo disponible para maquinaria nueva de subtipo 'esparcidor de sello'.");
}

$etapas = [
    "ARMAR TANQUE" => [
        "Trazar, cortar, rolar y hacer ceja a tapas" => 5,
        "Trazar, cortar, rolar cuerpo" => 5,
        "Armar cuerpo" => 5,
        "Armar chasis" => 5,
        "Armar flux" => 5,
        "Colocar chasis y flux" => 5,
        "Colocar tapas y tubulares" => 5,
        "Colocar fibra de vidrio y lámina A.I" => 10,
        "Colocar accesorios" => 5,
    ],
    "ESPARCIDOR" => [
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
        "Colocar accesorios finales" => 3,
        "Prueba de equipo final" => 5,
    ]
];

$conn->query("CREATE TABLE IF NOT EXISTS avance_esparcidor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_maquinaria INT NOT NULL,
    etapa VARCHAR(255) NOT NULL,
    completado BOOLEAN DEFAULT FALSE,
    UNIQUE KEY (id_maquinaria, etapa)
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $etapa = $_POST['etapa'] ?? '';
    $accion = $_POST['accion'] ?? '';
    if ($etapa && $accion) {
        if ($accion == 'marcar') {
            $conn->query("REPLACE INTO avance_esparcidor (id_maquinaria, etapa, completado) VALUES ($id_maquinaria, '$etapa', 1)");
        } elseif ($accion == 'desmarcar') {
            $conn->query("DELETE FROM avance_esparcidor WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'");
        }
    }
}

$completados = [];
$res = $conn->query("SELECT etapa FROM avance_esparcidor WHERE id_maquinaria = $id_maquinaria AND completado = 1");
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