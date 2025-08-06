<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

// ------ ROLES Y USUARIO ------
$rol = $_SESSION['rol'] ?? 'consulta'; // produccion, usada, consulta
$usuario = $_SESSION['usuario'] ?? '';

$id_maquinaria = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_maquinaria <= 0) {
  die("ID de maquinaria no válido");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) {
  die("Maquinaria no encontrada.");
}

$tipo = strtolower($maquinaria['tipo_maquinaria']);

// Permisos como inventario
$puede_modificar = false;
if ($usuario === 'jabri') {
    $puede_modificar = true;
} elseif ($rol == 'produccion' && ($tipo == 'nueva' || $tipo == 'camion')) {
    $puede_modificar = true;
} elseif ($rol == 'usada' && $tipo == 'usada') {
    $puede_modificar = true;
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa']) && $puede_modificar) {
  $etapa = $_POST['etapa'];
  $accion = $_POST['accion'];
  $now = date('Y-m-d H:i:s');

  if ($accion === 'marcar') {
    // Marca etapa y fecha
    $stmt = $conn->prepare("INSERT INTO avance_petrolizadora (id_maquinaria, etapa, updated_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at)");
    $stmt->bind_param("iss", $id_maquinaria, $etapa, $now);
    $stmt->execute();
  } elseif ($accion === 'desmarcar') {
    $stmt = $conn->prepare("DELETE FROM avance_petrolizadora WHERE id_maquinaria = ? AND etapa = ?");
    $stmt->bind_param("is", $id_maquinaria, $etapa);
    $stmt->execute();
  }

  header("Location: avance_petrolizadora.php?id=$id_maquinaria");
  exit;
}

// Etapas completadas
$completadas = [];
$res = $conn->query("SELECT etapa FROM avance_petrolizadora WHERE id_maquinaria = $id_maquinaria AND etapa IS NOT NULL");
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

// Guarda el avance actualizado en una fila especial (etapa IS NULL) para el porcentaje total
$now = date('Y-m-d H:i:s');
$conn->query("DELETE FROM avance_petrolizadora WHERE id_maquinaria = $id_maquinaria AND etapa IS NULL");
$conn->query("INSERT INTO avance_petrolizadora (id_maquinaria, etapa, avance, updated_at) VALUES ($id_maquinaria, NULL, $porcentaje, '$now')");

// Consulta la fecha de última actualización del avance general
$fecha_actualizacion = $conn->query("SELECT updated_at FROM avance_petrolizadora WHERE id_maquinaria = $id_maquinaria AND etapa IS NULL")->fetch_assoc()['updated_at'] ?? '';
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
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 1rem;
  font-size: 1rem;
  padding: 10px 28px 10px 20px; /* Más espacio a la derecha */
  text-align: left;
  color: white;
  background-color: #012a5c;
  border: 2px solid #004080;
  box-shadow: 0 2px 6px rgba(0,0,0,0.3);
  transition: all 0.2s ease-in-out;
  min-height: 54px; /* un poco más alto */
  position: relative;
}
.btn-toggle:hover {
  background-color: #003366;
  border-color: #0059b3;
}
.checkmark-box {
  width: 3em;
  height: 3em;
  background: rgba(255, 193, 7, 0.22); /* círculo amarillo translúcido */
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  box-shadow: 0 2px 6px rgba(0,0,0,0.11);
  pointer-events: none; /* evita clics accidentales en el círculo */
}
.checkmark-box svg {
  width: 2.2em;
  height: 2.2em;
  stroke: #ffc107;
  stroke-width: 6;
  stroke-linecap: round;
  stroke-linejoin: round;
  fill: none;
  filter: drop-shadow(0 0 2px #001f3f);
}
    .fecha-actualizacion {
      font-size: 1rem;
      color: #87d0ff;
      text-align: center;
      margin-top: 0.5rem;
      margin-bottom: 1.2rem;
    }
  </style>
</head>
<body>
  <div class="ficha">
    <h3>Avance Petrolizadora</h3>
    <h5><?= htmlspecialchars($maquinaria['nombre']) ?> (Modelo: <?= htmlspecialchars($maquinaria['modelo']) ?>)</h5>

    <div class="progress mb-4">
      <div class="progress-bar" role="progressbar" style="width: <?= $porcentaje ?>%;" aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100"><?= $porcentaje ?>%</div>
    </div>
    <?php if ($fecha_actualizacion): ?>
      <div class="fecha-actualizacion">
        Última actualización: <?= date('d/m/Y H:i', strtotime($fecha_actualizacion)) ?>
      </div>
    <?php endif; ?>

   <?php foreach ($etapas as $grupo => $pasos): ?>
  <h5 class="text-center text-info mt-4"><?= $grupo ?></h5>
  <?php foreach ($pasos as $etapa => $peso):
    $ya = in_array($etapa, $completadas);
    if ($puede_modificar):
  ?>
    <form method="POST" class="mb-1">
      <input type="hidden" name="etapa" value="<?= htmlspecialchars($etapa) ?>">
      <input type="hidden" name="accion" value="<?= $ya ? 'desmarcar' : 'marcar' ?>">
      <button type="submit" class="btn btn-toggle btn-outline-light">
        <span><?= $etapa ?> (<?= $peso ?>%)</span>
        <?php if ($ya): ?>
          <span class="checkmark-box">
            <svg viewBox="0 0 32 32"><polyline points="8,17 14,23 24,9"></polyline></svg>
          </span>
        <?php endif; ?>
      </button>
    </form>
  <?php else: ?>
    <div class="btn btn-toggle" style="pointer-events:none;">
      <span><?= $etapa ?> (<?= $peso ?>%)</span>
      <?php if ($ya): ?>
        <span class="checkmark-box">
          <svg viewBox="0 0 32 32"><polyline points="8,17 14,23 24,9"></polyline></svg>
        </span>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <?php endforeach; ?>
<?php endforeach; ?>

    <div class="text-center mt-4">
      <a href="inventario.php" class="btn btn-outline-light">← Volver al Inventario</a>
    </div>
  </div>
</body>
</html>
