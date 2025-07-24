
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
$etapas_tanque = [
    "Trazar, cortar, rolar y hacer ceja a tapas" => 5,
    "Trazar, cortar, rolar cuerpo" => 5,
    "Armar cuerpo" => 5,
    "Armar chasis" => 5,
    "Armar flux" => 5,
    "Colocar chasis y flux" => 5,
    "Colocar tapas y tubulares" => 5,
    "Colocar fibra de vidrio y lámina A.I" => 10,
    "Colocar accesorios" => 5,
];
$etapas_esparcidor = [
    "Armar cajas negras y de controles" => 5,
    "Armar chasis 2" => 5,
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
];
$etapas = $etapas_tanque + $etapas_esparcidor;
$conn->query("CREATE TABLE IF NOT EXISTS avance_esparcidor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_maquinaria INT NOT NULL,
    etapa VARCHAR(255) NOT NULL,
    completado BOOLEAN DEFAULT FALSE,
    UNIQUE KEY (id_maquinaria, etapa)
)");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa'])) {
    $etapa = $conn->real_escape_string($_POST['etapa']);
    $completado = $conn->query("SELECT 1 FROM avance_esparcidor WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'")->num_rows > 0;
    if (!$completado) {
        $conn->query("INSERT INTO avance_esparcidor (id_maquinaria, etapa, completado) VALUES ($id_maquinaria, '$etapa', 1)");
    } else {
        $conn->query("DELETE FROM avance_esparcidor WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'");
    }
}
$realizadas = [];
$res = $conn->query("SELECT etapa FROM avance_esparcidor WHERE id_maquinaria = $id_maquinaria AND completado = 1");
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
  <title>Avance Esparcidor</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #001f3f;
      color: white;
      font-family: 'Segoe UI', sans-serif;
    }
    .ficha {
      background-color: #012a5c;
      padding: 2rem;
      border-radius: 1.5rem;
      max-width: 1100px;
      margin: 2rem auto;
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
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
      width: 100%;
      margin: 0.25rem 0;
      border-radius: 0.75rem;
      font-size: 0.9rem;
      text-align: center;
    }
    .completed {
      background-color: #198754 !important;
      color: white !important;
      font-weight: bold;
    }
    .btn-outline-light {
      background-color: #012a5c;
      color: white;
      border: 1px solid #ccc;
    }
    .btn-outline-light:hover {
      background-color: #ffc107;
      color: #000;
      font-weight: bold;
    }
    .separador {
      background-color: #003366;
      color: white;
      padding: 0.6rem;
      border-radius: 1rem;
      margin-top: 1.5rem;
      text-align: center;
      font-weight: bold;
    }
    .renglon {
      border: 1px solid #444;
      padding: 0.25rem;
      border-radius: 0.5rem;
      margin-bottom: 0.5rem;
    }
  </style>
</head>
<body>
  <div class="ficha">
    <h3>Avance de Esparcidor de Sello</h3>
    <h5><?= htmlspecialchars($maquinaria['nombre']) ?> (Modelo: <?= htmlspecialchars($maquinaria['modelo']) ?>)</h5>
    <div class="mb-4">
      <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: <?= $porcentaje ?>%;" aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100"><?= $porcentaje ?>%</div>
      </div>
    </div>
    <form method="post">
      <div class="separador">ARMAR TANQUE</div>
      <?php foreach ($etapas_tanque as $etapa => $peso): ?>
        <div class="renglon text-center">
          <button type="submit" name="etapa" value="<?= htmlspecialchars($etapa) ?>" class="btn btn-toggle <?= in_array($etapa, $realizadas) ? 'completed' : 'btn-outline-light' ?>">
            <?= htmlspecialchars($etapa) ?> (<?= $peso ?>%)
          </button>
        </div>
      <?php endforeach; ?>
      <div class="separador">ESPARCIDOR</div>
      <?php foreach ($etapas_esparcidor as $etapa => $peso): ?>
        <div class="renglon text-center">
          <button type="submit" name="etapa" value="<?= htmlspecialchars($etapa) ?>" class="btn btn-toggle <?= in_array($etapa, $realizadas) ? 'completed' : 'btn-outline-light' ?>">
            <?= htmlspecialchars($etapa) ?> (<?= $peso ?>%)
          </button>
        </div>
      <?php endforeach; ?>
    </form>
    <div class="text-center mt-4">
      <a href="inventario.php" class="btn btn-outline-light">← Volver al Inventario</a>
    </div>
  </div>
</body>
</html>
