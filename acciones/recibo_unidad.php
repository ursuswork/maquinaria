<<?php
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
        <input type='radio' class='btn-check componente-radio' data-seccion='" . $seccion . "' data-componente='" . $nombre . "' data-peso='" . $porcentaje . "' data-valor='bueno' name='componentes[{$nombre}]' id='{$id_base}_bueno' value='bueno'" . ($valor_existente == 'bueno' ? ' checked' : '') . ">
        <label class='btn btn-outline-primary' for='{$id_base}_bueno'>Bueno</label>

        <input type='radio' class='btn-check componente-radio' data-seccion='" . $seccion . "' data-componente='" . $nombre . "' data-peso='0' data-valor='regular' name='componentes[{$nombre}]' id='{$id_base}_regular' value='regular'" . ($valor_existente == 'regular' ? ' checked' : '') . ">
        <label class='btn btn-outline-primary' for='{$id_base}_regular'>Regular</label>

        <input type='radio' class='btn-check componente-radio' data-seccion='" . $seccion . "' data-componente='" . $nombre . "' data-peso='0' data-valor='malo' name='componentes[{$nombre}]' id='{$id_base}_malo' value='malo'" . ($valor_existente == 'malo' ? ' checked' : '') . ">
        <label class='btn btn-outline-primary' for='{$id_base}_malo'>Malo</label>
      </div>
    </div>
  ";
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $componentes = $_POST['componentes'] ?? [];
  $observaciones = $conn->real_escape_string($_POST['observaciones'] ?? '');
  $avance_total = 0;

  $sql_check = $conn->query("SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria");
  $existe = $sql_check->fetch_assoc();

  if ($existe) {
    $updates = [];
    foreach ($componentes as $campo => $valor) {
      $campo_sql = $conn->real_escape_string($campo);
      $valor_sql = $conn->real_escape_string($valor);
      $updates[] = "`$campo_sql` = '$valor_sql'";
      if ($valor === 'bueno') {
        $avance_total += $porcentajes[$campo] ?? 0;
      }
    }
    $updates[] = "`observaciones` = '$observaciones'";
    $conn->query("UPDATE recibo_unidad SET " . implode(', ', $updates) . " WHERE id_maquinaria = $id_maquinaria");
  } else {
    $campos = [];
    $valores = [];
    foreach ($componentes as $campo => $valor) {
      $campo_sql = $conn->real_escape_string($campo);
      $valor_sql = $conn->real_escape_string($valor);
      $campos[] = "`$campo_sql`";
      $valores[] = "'$valor_sql'";
      if ($valor === 'bueno') {
        $avance_total += $porcentajes[$campo] ?? 0;
      }
    }
    $campos[] = "`id_maquinaria`";
    $valores[] = $id_maquinaria;
    $campos[] = "`observaciones`";
    $valores[] = "'$observaciones'";
    $conn->query("INSERT INTO recibo_unidad (" . implode(',', $campos) . ") VALUES (" . implode(',', $valores) . ")");
  }

  // Actualizar en tabla maquinaria
  $avance_total = round($avance_total, 2);
  $conn->query("UPDATE maquinaria SET condicion_estimada = $avance_total WHERE id = $id_maquinaria");

  // Redirigir
  header("Location: ../inventario.php");
  exit;
}
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
    body { background-color: #001f3f; color: #ffffff; font-family: 'Segoe UI', sans-serif; }
    .container { background-color: #002b5c; padding: 2rem; border-radius: 1rem; max-width: 1200px; margin: auto; box-shadow: 0 0 20px #000000; }
    h3, h4, h5 { color: #ffc107; border-bottom: 2px solid #ffc107; padding-bottom: .5rem; margin-bottom: 1rem; }
    .form-label { color: #ffc107; font-weight: bold; }
    .form-control, .form-select { background-color: #003366; color: #ffffff; border: 1px solid #0059b3; margin-bottom: 1rem; }
    .btn-primary { background-color: #0056b3; border: none; font-weight: bold; }
    .btn-warning { background-color: #ffc107; border: none; font-weight: bold; color: #000000; }
    .progress-bar { transition: width 0.4s ease; background-color: #28a745 !important; }
    @media print {
      .btn, textarea, input[type="radio"], label.btn { display: none !important; }
      body { background: #ffffff; color: #000000; }
    }
  </style>
</head>
<body>
  <div class="container py-4">
    <h3 class="text-center">Recibo de Unidad</h3>
    <form method="POST">
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
    <div class="progress-bar" id="<?= $barra_id ?>" role="progressbar" style="width: <?= ($avance_actual / $peso_total * 100) ?>%;">
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
      <div class="text-center mt-4">
        <button type="submit" class="btn btn-warning px-5 py-2">Guardar</button>
        <button type="button" onclick="window.print()" class="btn btn-primary px-4 py-2 ms-2">Imprimir</button>
      </div>
    </form>
  </div>
  <script>
    document.querySelectorAll('.componente-radio').forEach(input => {
      input.addEventListener('change', () => {
        const secciones = {};
        document.querySelectorAll('.componente-radio:checked').forEach(radio => {
          if (radio.dataset.valor === 'bueno') {
            const seccion = radio.dataset.seccion;
            const peso = parseFloat(radio.dataset.peso);
            if (!secciones[seccion]) secciones[seccion] = 0;
            secciones[seccion] += peso;
          }
        });
        for (const [seccion, avance] of Object.entries(secciones)) {
          const id = 'barra_' + seccion.toLowerCase().replace(/ /g, '_');
          const barra = document.getElementById(id);
          const pesos = <?= json_encode($pesos) ?>;
          const normalizada = seccion.toUpperCase();
          const pesoTotal = pesos[normalizada];
          if (barra && pesoTotal) {
            const porcentaje = (avance / pesoTotal * 100).toFixed(2);
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
      });
    });
  </script>
</body>
</html>
