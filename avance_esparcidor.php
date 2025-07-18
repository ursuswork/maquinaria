<?php
session_start();
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) die("❌ ID inválido");

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) die("❌ Maquinaria no encontrada.");

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
    "Colocar accesorios" => 5
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

// Insertar etapa si se selecciona
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa'])) {
  $etapa = $conn->real_escape_string($_POST['etapa']);
  $existe = $conn->query("SELECT id FROM avance_fabricacion WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'")->num_rows;
  if ($existe === 0) {
    $conn->query("INSERT INTO avance_fabricacion (id_maquinaria, etapa) VALUES ($id_maquinaria, '$etapa')");
  }
  header("Location: avance_esparcidor.php?id=$id_maquinaria");
  exit;
}

// Consultar etapas completadas
$completadas = [];
$res = $conn->query("SELECT etapa FROM avance_fabricacion WHERE id_maquinaria = $id_maquinaria");
while ($r = $res->fetch_assoc()) {
  $completadas[] = $r['etapa'];
}

// Calcular avance por sección
$progreso_total = 0;
$total_posible = 0;
foreach ($etapas as $seccion => $items) {
  foreach ($items as $nombre => $peso) {
    $total_posible += $peso;
    if (in_array($nombre, $completadas)) {
      $progreso_total += $peso;
    }
  }
}
$porcentaje = round(($progreso_total / $total_posible) * 100);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Avance Esparcidor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h4 class="mb-4 text-primary">Avance: <?=$maquinaria['nombre']?> (<?=$porcentaje?>%)</h4>

  <div class="progress mb-4" style="height: 30px;">
    <div class="progress-bar <?= $porcentaje >= 90 ? 'bg-success' : ($porcentaje >= 60 ? 'bg-warning' : 'bg-danger') ?>" role="progressbar" style="width: <?=$porcentaje?>%;">
      <?=$porcentaje?>%
    </div>
  </div>

  <form method="POST">
    <?php foreach ($etapas as $seccion => $items): ?>
      <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white fw-bold"><?=$seccion?></div>
        <div class="card-body">
          <div class="row g-2">
            <?php foreach ($items as $nombre => $peso): ?>
              <div class="col-md-6">
                <button name="etapa" value="<?=htmlspecialchars($nombre)?>" class="btn <?=in_array($nombre, $completadas) ? 'btn-success' : 'btn-outline-secondary'?> w-100 text-start mb-2">
                  <?=htmlspecialchars($nombre)?> (<?=$peso?>%)
                </button>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </form>

  <a href="../inventario.php" class="btn btn-outline-primary mt-3">← Volver al Inventario</a>
</div>
</body>
</html>