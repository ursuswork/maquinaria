<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) die("Error: ID inválido.");

$secciones = [
  "MOTOR" => [...], // tus componentes
  "SISTEMA MECÁNICO" => [...],
  "SISTEMA HIDRÁULICO" => [...],
  "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => [...],
  "ESTÉTICO" => [...],
  "CONSUMIBLES" => [...]
];

$pesos = [
  "MOTOR" => 15,
  "SISTEMA MECÁNICO" => 15,
  "SISTEMA HIDRÁULICO" => 30,
  "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => 25,
  "ESTÉTICO" => 5,
  "CONSUMIBLES" => 10
];

$porcentajes = [];
foreach ($secciones as $seccion => $componentes) {
  $peso_comp = round($pesos[$seccion] / count($componentes), 4);
  foreach ($componentes as $c) $porcentajes[$c] = $peso_comp;
}

$recibo_existente = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1")->fetch_assoc();

function botonOpciones($nombre, $valor, $porcentaje, $seccion) {
  $id = preg_replace("/[^a-zA-Z0-9]/", "_", $nombre);
  return "
  <div class='mb-2'>
    <label class='form-label fw-bold text-warning'>$nombre <small class='text-light'>($porcentaje%)</small></label><br>
    <div class='btn-group' role='group'>
      <input type='radio' class='btn-check componente-radio' data-seccion='$seccion' data-componente='$nombre' data-peso='$porcentaje' data-valor='bueno' name='componentes[$nombre]' id='{$id}_bueno' value='bueno'" . ($valor == 'bueno' ? ' checked' : '') . ">
      <label class='btn btn-outline-success' for='{$id}_bueno'>Bueno</label>

      <input type='radio' class='btn-check componente-radio' data-seccion='$seccion' data-componente='$nombre' data-peso='0' data-valor='regular' name='componentes[$nombre]' id='{$id}_regular' value='regular'" . ($valor == 'regular' ? ' checked' : '') . ">
      <label class='btn btn-outline-warning' for='{$id}_regular'>Regular</label>

      <input type='radio' class='btn-check componente-radio' data-seccion='$seccion' data-componente='$nombre' data-peso='0' data-valor='malo' name='componentes[$nombre]' id='{$id}_malo' value='malo'" . ($valor == 'malo' ? ' checked' : '') . ">
      <label class='btn btn-outline-danger' for='{$id}_malo'>Malo</label>
    </div>
  </div>";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $componentes = $_POST['componentes'] ?? [];
  $observaciones = $conn->real_escape_string($_POST['observaciones'] ?? '');
  $columnas = "";
  $valores = "";
  $total_condicion = 0;

  foreach ($componentes as $nombre => $estado) {
    $nombre_sql = "`" . $conn->real_escape_string($nombre) . "`";
    $estado_sql = "'" . $conn->real_escape_string($estado) . "'";
    $columnas .= "$nombre_sql, ";
    $valores .= "$estado_sql, ";

    if ($estado == "bueno" && isset($porcentajes[$nombre])) {
      $total_condicion += $porcentajes[$nombre];
    }
  }

  $columnas .= "`id_maquinaria`, `observaciones`";
  $valores .= "$id_maquinaria, '$observaciones'";

  $existe = $conn->query("SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1")->fetch_assoc();

  if ($existe) {
    $updates = [];
    foreach ($componentes as $nombre => $estado) {
      $col = "`" . $conn->real_escape_string($nombre) . "`";
      $val = "'" . $conn->real_escape_string($estado) . "'";
      $updates[] = "$col = $val";
    }
    $updates[] = "`observaciones` = '$observaciones'";
    $sql = "UPDATE recibo_unidad SET " . implode(", ", $updates) . " WHERE id_maquinaria = $id_maquinaria";
    $conn->query($sql);
  } else {
    $sql = "INSERT INTO recibo_unidad ($columnas) VALUES ($valores)";
    $conn->query($sql);
  }

  $total_condicion = round($total_condicion, 2);
  $conn->query("UPDATE maquinaria SET condicion_estimada = $total_condicion WHERE id = $id_maquinaria");

  header("Location: ../inventario.php");
  exit;
}
?>
<!-- HTML sigue debajo -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recibo de Unidad</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #001f3f; color: #fff; font-family: 'Segoe UI', sans-serif; }
    .container { background-color: #002b5c; padding: 2rem; border-radius: 1rem; max-width: 1200px; margin: auto; box-shadow: 0 0 20px #000000; }
    h3, h4, h5 { color: #ffc107; border-bottom: 2px solid #ffc107; padding-bottom: .5rem; margin-bottom: 1rem; }
    .form-label { color: #ffc107; font-weight: bold; }
    .form-control, .form-select { background-color: #003366; color: #ffffff; border: 1px solid #0059b3; margin-bottom: 1rem; }
    .btn-primary { background-color: #0056b3; border: none; font-weight: bold; }
    .btn-warning { background-color: #ffc107; border: none; font-weight: bold; color: #000; }
    .progress-bar { transition: width 0.4s ease; background-color: #28a745 !important; }
    @media print {
      .btn, textarea, input[type="radio"], label.btn { display: none !important; }
      body { background: #fff; color: #000; }
    }
  </style>
</head>
<body>
<div class="container py-4">
  <h3 class="text-center">Recibo de Unidad</h3>
  <form method="POST">
    <!-- Puedes añadir encabezados si deseas -->
    <?php foreach ($secciones as $titulo => $componentes): ?>
      <?php
        $peso_total = $pesos[$titulo];
        $avance_actual = 0;
        foreach ($componentes as $comp) {
          $val = $recibo_existente[$comp] ?? '';
          if ($val === 'bueno') $avance_actual += $porcentajes[$comp];
        }
        $barra_id = 'barra_' . strtolower(str_replace(' ', '_', $titulo));
      ?>
      <hr>
      <h5><?= $titulo ?> (<?= $peso_total ?>%)</h5>
      <div class="progress mb-3" style="height: 20px;">
        <div class="progress-bar" id="<?= $barra_id ?>" style="width: <?= ($avance_actual / $peso_total * 100) ?>%">
          <?= round($avance_actual, 2) ?>%
        </div>
      </div>
      <div class="row">
        <?php foreach ($componentes as $comp): ?>
          <div class="col-md-6"><?= botonOpciones($comp, $recibo_existente[$comp] ?? '', $porcentajes[$comp], $titulo) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <div class="mb-3">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($recibo_existente['observaciones'] ?? '') ?></textarea>
    </div>
    <div class="text-center mt-4">
      <button type="submit" class="btn btn-warning px-5 py-2">Guardar</button>
      <button type="button" onclick="window.print()" class="btn btn-primary px-4 py-2 ms-2">Imprimir</button>
    </div>
    <div class="text-center mt-4">
      <div id="condicion_total" class="text-warning display-5 fw-bold"></div>
    </div>
  </form>
</div>
<script>
document.querySelectorAll('.componente-radio').forEach(input => {
  input.addEventListener('change', () => {
    const secciones = {};
    let total = 0;

    document.querySelectorAll('.componente-radio:checked').forEach(radio => {
      if (radio.dataset.valor === 'bueno') {
        const seccion = radio.dataset.seccion;
        const peso = parseFloat(radio.dataset.peso);
        if (!secciones[seccion]) secciones[seccion] = 0;
        secciones[seccion] += peso;
        total += peso;
      }
    });

    for (const [seccion, avance] of Object.entries(secciones)) {
      const barra = document.getElementById('barra_' + seccion.toLowerCase().replace(/ /g, '_'));
      const max = <?= json_encode($pesos) ?>[seccion];
      if (barra && max) {
        const porcentaje = (avance / max * 100).toFixed(2);
        barra.style.width = porcentaje + '%';
        barra.innerText = avance.toFixed(2) + '%';
      }
    }

    document.querySelectorAll('.progress-bar').forEach(bar => {
      const id = bar.id;
      const seccion = id.replace('barra_', '').replace(/_/g, ' ').toUpperCase();
      if (!secciones[seccion]) {
        bar.style.width = '0%';
        bar.innerText = '0%';
      }
    });

    document.getElementById('condicion_total').innerText = total.toFixed(2) + '%';
  });
});
</script>
</body>
</html>
