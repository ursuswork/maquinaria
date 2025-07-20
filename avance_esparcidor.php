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

// Etapas y pesos
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

// Crear tabla si no existe
$conn->query("
    CREATE TABLE IF NOT EXISTS avance_esparcidor (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_maquinaria INT NOT NULL,
        etapa VARCHAR(255) NOT NULL,
        completado BOOLEAN DEFAULT FALSE,
        UNIQUE KEY (id_maquinaria, etapa)
    )
");

// Guardar cambios si se envió POST
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

// Leer avances marcados
$completados = [];
$res = $conn->query("SELECT etapa FROM avance_esparcidor WHERE id_maquinaria = $id_maquinaria AND completado = 1");
while ($row = $res->fetch_assoc()) {
    $completados[] = $row['etapa'];
}

// Calcular porcentaje
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
    body { background-color: #121212; color: #f1f1f1; }
    .btn-toggle { min-width: 200px; margin-bottom: 8px; }
    .completed { background-color: #198754 !important; color: white !important; }
    .progress { height: 30px; }
    .progress-bar { font-weight: bold; }
  </style>
</head>
<body class="p-4">
  <div class="container">
    <h3 class="mb-4">Avance de Esparcidor de Sello – <?= htmlspecialchars($maquinaria['nombre']) ?></h3>

    <div class="mb-4">
      <div class="progress">
        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $total ?>%;" aria-valuenow="<?= $total ?>" aria-valuemin="0" aria-valuemax="100"><?= $total ?>%</div>
      </div>
    </div>

    <?php foreach ($etapas as $seccion => $items): ?>
      <h5 class="mt-4 text-primary"><?= $seccion ?></h5>
      <div class="row">
        <?php foreach ($items as $etapa => $peso): 
          $ya = in_array($etapa, $completados);
        ?>
          <div class="col-md-6">
            <form method="POST" class="d-inline-block w-100">
              <input type="hidden" name="etapa" value="<?= htmlspecialchars($etapa) ?>">
              <input type="hidden" name="accion" value="<?= $ya ? 'desmarcar' : 'marcar' ?>">
              <button type="submit" class="btn btn-toggle btn-sm <?= $ya ? 'btn-success completed' : 'btn-outline-light' ?>">
                <?= $etapa ?> (<?= $peso ?>%)
              </button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <a href="inventario.php" class="btn btn-secondary mt-4">← Volver al Inventario</a>
  </div>
</body>
</html>
