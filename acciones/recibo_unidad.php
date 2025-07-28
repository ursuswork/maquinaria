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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $componentes = $_POST['componentes'] ?? [];
  $observaciones = $conn->real_escape_string($_POST['observaciones'] ?? '');
  $columnas = "";
  $valores = "";
  $total_condicion = 0;

  $secciones_pesos = [
    "MOTOR" => 15,
    "SISTEMA MECÁNICO" => 15,
    "SISTEMA HIDRÁULICO" => 30,
    "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => 25,
    "ESTÉTICO" => 5,
    "CONSUMIBLES" => 10
  ];

  $secciones_componentes = [
    "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
    "SISTEMA MECÁNICO" => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
    "SISTEMA HIDRÁULICO" => ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"],
    "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)", "SENSORES"],
    "ESTÉTICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
    "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
  ];

  foreach ($secciones_componentes as $seccion => $componentes_seccion) {
    $peso_seccion = $secciones_pesos[$seccion];
    $peso_por_componente = $peso_seccion / count($componentes_seccion);
    foreach ($componentes_seccion as $componente) {
      $estado = $componentes[$componente] ?? '';
      if ($estado === 'bueno') {
        $total_condicion += $peso_por_componente;
      }
    }
  }

  foreach ($componentes as $nombre => $estado) {
    $columna = "`" . $conn->real_escape_string($nombre) . "`";
    $valor = "'" . $conn->real_escape_string($estado) . "'";
    $columnas .= "$columna, ";
    $valores .= "$valor, ";
  }

  $columnas .= "`id_maquinaria`, `observaciones`, `condicion_estimada`";
  $valores .= "$id_maquinaria, '$observaciones', $total_condicion";

  $sql_existente = "SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1";
  $existe = $conn->query($sql_existente)->fetch_assoc();

  if ($existe) {
    $updates = [];
    foreach ($componentes as $nombre => $estado) {
      $col = "`" . $conn->real_escape_string($nombre) . "`";
      $val = "'" . $conn->real_escape_string($estado) . "'";
      $updates[] = "$col = $val";
    }
    $updates[] = "`observaciones` = '$observaciones'";
    $updates[] = "`condicion_estimada` = $total_condicion";
    $update_sql = "UPDATE recibo_unidad SET " . implode(", ", $updates) . " WHERE id_maquinaria = $id_maquinaria";
    $conn->query($update_sql);
  } else {
    $insert_sql = "INSERT INTO recibo_unidad ($columnas) VALUES ($valores)";
    $conn->query($insert_sql);
  }

  $conn->query("UPDATE maquinaria SET condicion_estimada = $total_condicion WHERE id = $id_maquinaria");

  echo "<script>alert('✅ Recibo guardado correctamente'); window.location.href = '../inventario.php';</script>";
  exit;
}

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
  "SISTEMA MECÁNICO" => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
  "SISTEMA HIDRÁULICO" => ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"],
  "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)", "SENSORES"],
  "ESTÉTICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
  "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
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
        <input type='radio' class='btn-check componente-radio' data-seccion='" . $seccion . "' data-componente='" . $nombre . "' data-peso='" . $porcentaje . "' data-valor='bueno' name='componentes[{" . $nombre . "}]' id='{$id_base}_bueno' value='bueno'" . ($valor_existente == 'bueno' ? ' checked' : '') . ">
        <label class='btn btn-outline-primary' for='{$id_base}_bueno'>Bueno</label>

        <input type='radio' class='btn-check componente-radio' data-seccion='" . $seccion . "' data-componente='" . $nombre . "' data-peso='0' data-valor='regular' name='componentes[{" . $nombre . "}]' id='{$id_base}_regular' value='regular'" . ($valor_existente == 'regular' ? ' checked' : '') . ">
        <label class='btn btn-outline-primary' for='{$id_base}_regular'>Regular</label>

        <input type='radio' class='btn-check componente-radio' data-seccion='" . $seccion . "' data-componente='" . $nombre . "' data-peso='0' data-valor='malo' name='componentes[{" . $nombre . "}]' id='{$id_base}_malo' value='malo'" . ($valor_existente == 'malo' ? ' checked' : '') . ">
        <label class='btn btn-outline-primary' for='{$id_base}_malo'>Malo</label>
      </div>
    </div>
  ";
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
          const pesoTotal = <?= json_encode($pesos) ?>[seccion];
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
