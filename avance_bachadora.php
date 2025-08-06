<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

// ------ CONTROL DE ROLES Y USUARIO ------
$rol = $_SESSION['rol'] ?? 'consulta'; // produccion, usada, consulta
$usuario = $_SESSION['usuario'] ?? '';

$id_maquinaria = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_maquinaria <= 0) {
  die("ID de maquinaria no válido");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) {
  die("❌ Maquinaria no encontrada.");
}

$tipo = strtolower($maquinaria['tipo_maquinaria']);

// Permisos: jabri = todo; produccion=nueva/camion, usada=usada, consulta=ver
$puede_editar = false;
if ($usuario === 'jabri') {
    $puede_editar = true;
} elseif ($rol == 'produccion' && ($tipo == 'nueva' || $tipo == 'camion')) {
    $puede_editar = true;
} elseif ($rol == 'usada' && $tipo == 'usada') {
    $puede_editar = true;
}

// Etapas
$etapas_arma = [
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
];

$etapas_bachadora = [
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

$etapas = $etapas_arma + $etapas_bachadora;

// Solo se procesa el POST si puede editar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa']) && $puede_editar) {
  $etapa = $conn->real_escape_string($_POST['etapa']);
  $existe = $conn->query("SELECT 1 FROM avance_bachadora WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'");
  if ($existe->num_rows == 0) {
    $conn->query("INSERT INTO avance_bachadora (id_maquinaria, etapa, updated_at) VALUES ($id_maquinaria, '$etapa', NOW())");
  } else {
    $conn->query("DELETE FROM avance_bachadora WHERE id_maquinaria = $id_maquinaria AND etapa = '$etapa'");
  }
}

$realizadas = [];
$res = $conn->query("SELECT etapa FROM avance_bachadora WHERE id_maquinaria = $id_maquinaria AND etapa IS NOT NULL");
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

// Guarda el avance actualizado en la fila especial (etapa IS NULL) para el porcentaje total
$conn->query("DELETE FROM avance_bachadora WHERE id_maquinaria = $id_maquinaria AND etapa IS NULL");
$conn->query("INSERT INTO avance_bachadora (id_maquinaria, etapa, avance, updated_at) VALUES ($id_maquinaria, NULL, $porcentaje, NOW())");

// Consultar fecha de última actualización
$fecha_actualizacion = '';
$fechaRes = $conn->query("SELECT updated_at FROM avance_bachadora WHERE id_maquinaria = $id_maquinaria AND etapa IS NULL");
if ($fechaRes && $rowF = $fechaRes->fetch_assoc()) {
  $fecha_actualizacion = $rowF['updated_at'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Avance Bachadora</title>
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
  max-width: 900px;
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
  width: 90%;
  margin: 8px auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 1rem;
  font-size: 1rem;
  padding: 12px 24px;
  text-align: left;
  color: white;
  background-color: #012a5c;
  border: 2px solid #004080;
  box-shadow: 0 2px 6px rgba(0,0,0,0.3);
  transition: all 0.2s ease-in-out;
  position: relative;
  min-height: 50px;
}
.btn-toggle:hover {
  background-color: #003366;
  border-color: #0059b3;
}
.completed {
  background-color: #1857c1 !important; /* Azul rey */
  color: #fff !important;
  font-weight: bold;
  border: 2px solid #0d327a !important;
}
.checkmark {
  margin-left: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
}
.checkmark svg {
  width: 2em;
  height: 2em;
  stroke: #fff;
  stroke-width: 5;
  stroke-linecap: round;
  stroke-linejoin: round;
  fill: none;
  filter: drop-shadow(0 0 4px #012a5c);
}
.fecha-actualizacion {
  color: #eee;
  font-size: 1.1rem;
  text-align: center;
  margin-top: 16px;
  margin-bottom: -10px;
}
  </style>
</head>
<body>
  <div class="ficha">
    <h3>Avance de Bachadora</h3>
    <h5><?= htmlspecialchars($maquinaria['nombre']) ?> (Modelo: <?= htmlspecialchars($maquinaria['modelo']) ?>)</h5>
    <?php if ($fecha_actualizacion): ?>
      <div class="fecha-actualizacion">Actualizado: <?= date('d/m/Y H:i', strtotime($fecha_actualizacion)) ?></div>
    <?php endif; ?>

    <div class="mb-4">
      <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: <?= $porcentaje ?>%;" aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100"><?= $porcentaje ?>%</div>
      </div>
    </div>

    <!-- Solo muestra el formulario si puede editar -->
    <?php if ($puede_editar): ?>
    <form method="post">
      <h5 class="mt-4 text-white text-center">ARMAR TANQUE</h5>
<?php foreach ($etapas_arma as $etapa => $peso): ?>
  <button type="submit" name="etapa" value="<?= htmlspecialchars($etapa) ?>" class="btn btn-toggle <?= in_array($etapa, $realizadas) ? 'completed' : '' ?>">
    <span><?= htmlspecialchars($etapa) ?> (<?= $peso ?>%)</span>
    <?php if (in_array($etapa, $realizadas)): ?>
      <span class="checkmark">
        <svg viewBox="0 0 32 32"><polyline points="8,17 14,23 24,9"></polyline></svg>
      </span>
    <?php endif; ?>
  </button>
<?php endforeach; ?>

<h5 class="mt-4 text-white text-center">BACHADORA</h5>
<?php foreach ($etapas_bachadora as $etapa => $peso): ?>
  <button type="submit" name="etapa" value="<?= htmlspecialchars($etapa) ?>" class="btn btn-toggle <?= in_array($etapa, $realizadas) ? 'completed' : '' ?>">
    <span><?= htmlspecialchars($etapa) ?> (<?= $peso ?>%)</span>
    <?php if (in_array($etapa, $realizadas)): ?>
      <span class="checkmark">
        <svg viewBox="0 0 32 32"><polyline points="8,17 14,23 24,9"></polyline></svg>
      </span>
    <?php endif; ?>
  </button>
<?php endforeach; ?>
    </form>
    <?php else: ?>
      <!-- Para usuarios sin permiso, solo muestra el listado -->
      <h5 class="mt-4 text-white text-center">ARMAR TANQUE</h5>
      <?php foreach ($etapas_arma as $etapa => $peso): ?>
        <div class="btn btn-toggle <?= in_array($etapa, $realizadas) ? 'completed' : '' ?>" style="pointer-events:none;">
          <?= htmlspecialchars($etapa) ?> (<?= $peso ?>%)
        </div>
      <?php endforeach; ?>

      <h5 class="mt-4 text-white text-center">BACHADORA</h5>
      <?php foreach ($etapas_bachadora as $etapa => $peso): ?>
        <div class="btn btn-toggle <?= in_array($etapa, $realizadas) ? 'completed' : '' ?>" style="pointer-events:none;">
          <?= htmlspecialchars($etapa) ?> (<?= $peso ?>%)
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div class="text-center mt-4">
      <a href="inventario.php" class="btn btn-outline-light">← Volver al Inventario</a>
    </div>
  </div>
</body>
</html>
