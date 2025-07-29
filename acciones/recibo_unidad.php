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

$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECANICO" => ["TRANSMISION", "DIFERENCIALES", "CARDÁN"],
  "SISTEMA HIDRAULICO" => ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"],
  "SISTEMA ELECTRICO Y ELECTRONICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)"],
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
  $porcentaje_por_componente = round($pesos[$seccion] / count($componentes), 2);
  foreach ($componentes as $componente) {
    $porcentajes[$componente] = $porcentaje_por_componente;
  }
}

$recibo_existente = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1")->fetch_assoc();

function botonOpciones($nombre, $valor_existente, $porcentaje, $seccion) {
  $id_base = preg_replace("/[^a-zA-Z0-9]/", "_", $nombre);
  return "
  <div class='mb-2'>
    <label class='form-label fw-bold text-warning'>" . htmlspecialchars($nombre) . " <small class='text-light'>($porcentaje%)</small></label><br>
    <div class='btn-group' role='group'>
      <input type='radio' class='btn-check componente-radio' data-seccion='$seccion' data-componente='$nombre' data-peso='$porcentaje' data-valor='bueno' name='componentes[$nombre]' id='{$id_base}_bueno' value='bueno'" . ($valor_existente == 'bueno' ? ' checked' : '') . ">
      <label class='btn btn-outline-primary' for='{$id_base}_bueno'>Bueno</label>

      <input type='radio' class='btn-check componente-radio' data-seccion='$seccion' data-componente='$nombre' data-peso='0' data-valor='regular' name='componentes[$nombre]' id='{$id_base}_regular' value='regular'" . ($valor_existente == 'regular' ? ' checked' : '') . ">
      <label class='btn btn-outline-primary' for='{$id_base}_regular'>Regular</label>

      <input type='radio' class='btn-check componente-radio' data-seccion='$seccion' data-componente='$nombre' data-peso='0' data-valor='malo' name='componentes[$nombre]' id='{$id_base}_malo' value='malo'" . ($valor_existente == 'malo' ? ' checked' : '') . ">
      <label class='btn btn-outline-primary' for='{$id_base}_malo'>Malo</label>
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
    body { background-color: #001f3f; color: #ffffff; }
    .container { background-color: #002b5c; padding: 2rem; border-radius: 1rem; max-width: 1200px; margin: auto; }
    .form-label { color: #ffc107; }
    .form-control, .form-select { background-color: #003366; color: #ffffff; }
    .btn-primary, .btn-warning { font-weight: bold; }
    .progress-bar.total { background-color: #ffc107 !important; color: black; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="text-center">Recibo de Unidad</h3>
  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>">

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
      <?php
        $peso_total = $pesos[$titulo];
        $componentes_seccion = count($componentes);
        $peso_por_componente = $peso_total / $componentes_seccion;
        $avance_actual = 0;
        foreach ($componentes as $componente) {
          $valor = $recibo_existente[$componente] ?? '';
          if ($valor == 'bueno') {
            $avance_actual += $peso_por_componente;
          }
        }
        $avance_actual = round($avance_actual, 2);
        $barra_id = 'barra_' . strtolower(str_replace(' ', '_', $titulo));
      ?>
      <hr>
      <h5><?= htmlspecialchars($titulo) ?> (<?= $peso_total ?>%)</h5>
      <div class="progress mb-3" style="height: 20px; width: 100%;">
        <div class="progress-bar bg-success" id="<?= $barra_id ?>" role="progressbar" style="width: <?= ($avance_actual / $peso_total * 100) ?>%;">
          <?= $avance_actual ?>%
        </div>
      </div>
      <div class="row">
        <?php foreach ($componentes as $comp): ?>
          <div class="col-md-6">
            <?= botonOpciones($comp, $recibo_existente[$comp] ?? '', $porcentajes[$comp], $titulo) ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <div class="mb-3">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($recibo_existente['observaciones'] ?? '') ?></textarea>
    </div>

    <div class="mb-4">
      <label class="form-label fw-bold text-warning">Avance Total (Condición Estimada)</label>
      <div class="progress" style="height: 24px;">
        <div class="progress-bar total" id="barra_total" style="width: 0%;">0%</div>
      </div>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-warning">Guardar</button>
      <button type="button" onclick="window.print()" class="btn btn-primary">Imprimir</button>
    </div>
  </form>
</div>
<script>
const pesos = <?= json_encode($pesos) ?>;
document.querySelectorAll('.componente-radio').forEach(input => {
  input.addEventListener('change', calcularAvance);
});

function calcularAvance() {
  let totalAvance = 0;
  document.querySelectorAll('.componente-radio:checked').forEach(radio => {
    if (radio.dataset.valor === 'bueno') {
      totalAvance += parseFloat(radio.dataset.peso);
    }
  });
  const barra = document.getElementById('barra_total');
  barra.style.width = totalAvance.toFixed(2) + '%';
  barra.innerText = totalAvance.toFixed(2) + '%';
}
window.onload = calcularAvance;
</script>
</body>
</html>
