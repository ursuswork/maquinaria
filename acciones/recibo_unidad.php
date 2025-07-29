<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) {
  die("Error: ID de maquinaria inválido.");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) {
  die("Error: Maquinaria no encontrada.");
}

// Secciones y componentes
$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGUEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECANICO" => ["TRANSMISION", "DIFERENCIALES", "CARDAN"],
  "SISTEMA HIDRAULICO" => ["BANCO DE VALVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRAULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCION", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRAULICOS", "ORBITROL", "TORQUES HUV (SATELITES)", "VALVULAS DE RETENCION", "REDUCTORES"],
  "SISTEMA ELECTRICO Y ELECTRONICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VALVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESION/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MODULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)"],
  "ESTETICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
  "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
];

$pesos = [
  "MOTOR" => 15,
  "SISTEMA MECANICO" => 15,
  "SISTEMA HIDRAULICO" => 30,
  "SISTEMA ELECTRICO Y ELECTRONICO" => 25,
  "ESTETICO" => 5,
  "CONSUMIBLES" => 10
];

$porcentajes = [];
foreach ($secciones as $seccion => $componentes) {
  $por_componente = round($pesos[$seccion] / count($componentes), 2);
  foreach ($componentes as $comp) {
    $porcentajes[$comp] = $por_componente;
  }
}

$recibo_existente = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1")->fetch_assoc();

function botonOpciones($nombre, $valor, $porcentaje, $seccion) {
  $id = preg_replace("/[^a-zA-Z0-9]/", "_", $nombre);
  return "
    <div class='mb-2'>
      <label class='form-label text-warning fw-bold'>$nombre <small class='text-light'>($porcentaje%)</small></label><br>
      <div class='btn-group' role='group'>
        <input type='radio' class='btn-check componente-radio' data-seccion='$seccion' data-peso='$porcentaje' name='componentes[$nombre]' id='{$id}_bueno' value='bueno' ".($valor == 'bueno' ? 'checked' : '').">
        <label class='btn btn-outline-success' for='{$id}_bueno'>Bueno</label>
        <input type='radio' class='btn-check componente-radio' data-seccion='$seccion' data-peso='0' name='componentes[$nombre]' id='{$id}_regular' value='regular' ".($valor == 'regular' ? 'checked' : '').">
        <label class='btn btn-outline-warning' for='{$id}_regular'>Regular</label>
        <input type='radio' class='btn-check componente-radio' data-seccion='$seccion' data-peso='0' name='componentes[$nombre]' id='{$id}_malo' value='malo' ".($valor == 'malo' ? 'checked' : '').">
        <label class='btn btn-outline-danger' for='{$id}_malo'>Malo</label>
      </div>
    </div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recibo de Unidad</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #001f3f; color: #fff; }
    .container { background-color: #003366; padding: 2rem; border-radius: 1rem; max-width: 1200px; }
    .form-label { color: #ffc107; }
    .progress-bar.total { background-color: #ffc107 !important; color: black; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="text-center">Recibo de Unidad</h3>
  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>">

    <!-- Datos generales -->
    <div class="row mb-3">
      <div class="col-md-4">
        <label class="form-label">Equipo</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($maquinaria['nombre']) ?>" readonly>
      </div>
      <div class="col-md-4">
        <label class="form-label">Marca</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($maquinaria['marca']) ?>" readonly>
      </div>
      <div class="col-md-4">
        <label class="form-label">Modelo</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($maquinaria['modelo']) ?>" readonly>
      </div>
    </div>

    <?php foreach ($secciones as $titulo => $componentes): ?>
      <hr>
      <h5><?= htmlspecialchars($titulo) ?> (<?= $pesos[$titulo] ?>%)</h5>
      <div class="progress mb-3" style="height: 20px;">
        <div class="progress-bar bg-info" id="barra_<?= strtolower(str_replace(' ', '_', $titulo)) ?>" role="progressbar" style="width: 0%;">0%</div>
      </div>
      <div class="row">
        <?php foreach ($componentes as $comp): ?>
          <div class="col-md-6"><?= botonOpciones($comp, $recibo_existente[$comp] ?? '', $porcentajes[$comp], $titulo) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <!-- Observaciones -->
    <div class="mb-3">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($recibo_existente['observaciones'] ?? '') ?></textarea>
    </div>

    <!-- Barra total -->
    <div class="mb-4">
      <label class="form-label fw-bold text-warning">Avance Total (Condición Estimada)</label>
      <div class="progress" style="height: 24px;">
        <div class="progress-bar total" id="barra_total" style="width: 0%;">0%</div>
      </div>
    </div>

    <!-- Botones -->
    <div class="text-center">
      <button type="submit" class="btn btn-warning">Guardar</button>
      <button type="button" onclick="window.print()" class="btn btn-primary">Imprimir</button>
    </div>
  </form>
</div>

<script>
document.querySelectorAll('.componente-radio').forEach(input => {
  input.addEventListener('change', calcularAvance);
});

function calcularAvance() {
  const pesos = <?= json_encode($pesos) ?>;
  const avancePorSeccion = {};
  let totalAvance = 0;

  document.querySelectorAll('.componente-radio:checked').forEach(radio => {
    const seccion = radio.dataset.seccion;
    const peso = parseFloat(radio.dataset.peso);
    if (radio.value === 'bueno') {
      totalAvance += peso;
      avancePorSeccion[seccion] = (avancePorSeccion[seccion] || 0) + peso;
    }
  });

  for (let seccion in pesos) {
    let barra = document.getElementById("barra_" + seccion.toLowerCase().replace(/ /g, "_"));
    let porcentaje = avancePorSeccion[seccion] || 0;
    barra.style.width = (porcentaje / pesos[seccion] * 100).toFixed(2) + "%";
    barra.innerText = porcentaje.toFixed(2) + "%";
  }

  let barraTotal = document.getElementById("barra_total");
  barraTotal.style.width = totalAvance.toFixed(2) + "%";
  barraTotal.innerText = totalAvance.toFixed(2) + "%";
}
window.onload = calcularAvance;
</script>
</body>
</html>
