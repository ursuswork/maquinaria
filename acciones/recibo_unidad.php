<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) {
  die("Error: ID inválido.");
}

// PESOS por sección
$pesos = [
  "MOTOR" => 15,
  "SISTEMA MECÁNICO" => 15,
  "SISTEMA HIDRÁULICO" => 30,
  "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => 25,
  "ESTÉTICO" => 5,
  "CONSUMIBLES" => 10
];

// COMPONENTES por sección
$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECÁNICO" => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
  "SISTEMA HIDRÁULICO" => ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"],
  "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)", "SENSORES"],
  "ESTÉTICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
  "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
];

// CALCULAR porcentaje por componente
$porcentajes = [];
foreach ($secciones as $nombre => $lista) {
  $por_componente = $pesos[$nombre] / count($lista);
  foreach ($lista as $comp) {
    $porcentajes[$comp] = $por_componente;
  }
}

// GUARDADO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $componentes = $_POST['componentes'] ?? [];
  $observaciones = $conn->real_escape_string($_POST['observaciones'] ?? '');
  $avance_total = 0;

  $sql_check = $conn->query("SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria");
  $existe = $sql_check->fetch_assoc();

  if ($existe) {
    $updates = [];
    foreach ($componentes as $campo => $valor) {
      $updates[] = "`" . $conn->real_escape_string($campo) . "` = '" . $conn->real_escape_string($valor) . "'";
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
      $campos[] = "`" . $conn->real_escape_string($campo) . "`";
      $valores[] = "'" . $conn->real_escape_string($valor) . "'";
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

  // GUARDAR avance total
  $avance_total = round($avance_total, 2);
  $conn->query("UPDATE maquinaria SET condicion_estimada = $avance_total WHERE id = $id_maquinaria");
  header("Location: ../inventario.php");
  exit;
}

$recibo_existente = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria")->fetch_assoc();
$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recibo de Unidad</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body { background: #001f3f; color: #fff; padding: 2rem; }
    .progress { height: 22px; margin-bottom: 1rem; }
    .progress-bar { background-color: #28a745; }
    .btn-check:checked + .btn { background-color: #ffc107; color: #000; }
    .seccion { background: #002b5c; padding: 1rem; margin-bottom: 1.5rem; border-radius: .5rem; }
    .seccion h4 { border-bottom: 1px solid #ffc107; padding-bottom: .5rem; color: #ffc107; }
    .form-label { font-weight: bold; color: #ffc107; }
  </style>
</head>
<body>
  <div class="container">
    <h3 class="text-center mb-4">Recibo de Unidad</h3>
    <form method="POST">
      <div class="mb-3 row">
        <div class="col-md-4">
          <label class="form-label">Equipo</label>
          <input class="form-control" value="<?= $maquinaria['nombre'] ?>" readonly>
        </div>
        <div class="col-md-4">
          <label class="form-label">Marca</label>
          <input class="form-control" value="<?= $maquinaria['marca'] ?>" readonly>
        </div>
        <div class="col-md-4">
          <label class="form-label">Modelo</label>
          <input class="form-control" value="<?= $maquinaria['modelo'] ?>" readonly>
        </div>
      </div>

      <?php foreach ($secciones as $seccion => $componentes): 
        $id_barra = 'barra_' . preg_replace('/[^a-zA-Z0-9]/', '_', $seccion);
        $porcentaje_seccion = $pesos[$seccion];
        $acumulado = 0;
        foreach ($componentes as $c) {
          if (($recibo_existente[$c] ?? '') === 'bueno') $acumulado += $porcentajes[$c];
        }
      ?>
        <div class="seccion">
          <h4><?= $seccion ?> (<?= $porcentaje_seccion ?>%)</h4>
          <div class="progress">
            <div class="progress-bar" id="<?= $id_barra ?>" style="width: <?= ($acumulado / $porcentaje_seccion) * 100 ?>%">
              <?= round($acumulado, 2) ?>%
            </div>
          </div>
          <div class="row">
            <?php foreach ($componentes as $comp):
              $valor = $recibo_existente[$comp] ?? '';
              $id = preg_replace('/[^a-zA-Z0-9]/', '_', $comp);
              $peso = $porcentajes[$comp];
            ?>
              <div class="col-md-6 mb-2">
                <label class="form-label"><?= $comp ?> (<?= round($peso, 2) ?>%)</label><br>
                <div class="btn-group" role="group">
                  <?php foreach (["bueno", "regular", "malo"] as $op): ?>
                    <input type="radio" class="btn-check componente-radio" name="componentes[<?= $comp ?>]" value="<?= $op ?>" id="<?= $id ?>_<?= $op ?>"
                      data-seccion="<?= $seccion ?>" data-peso="<?= $peso ?>" data-valor="<?= $op ?>" <?= $valor === $op ? 'checked' : '' ?>>
                    <label class="btn btn-outline-primary" for="<?= $id ?>_<?= $op ?>"><?= ucfirst($op) ?></label>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="mb-3">
        <label class="form-label">Observaciones</label>
        <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($recibo_existente['observaciones'] ?? '') ?></textarea>
      </div>

      <div class="text-center">
        <button class="btn btn-warning px-4">Guardar</button>
      </div>
    </form>
  </div>

  <script>
    const pesos = <?= json_encode($pesos) ?>;
    document.querySelectorAll('.componente-radio').forEach(radio => {
      radio.addEventListener('change', () => {
        const avance = {};
        document.querySelectorAll('.componente-radio:checked').forEach(input => {
          const seccion = input.dataset.seccion;
          const valor = input.dataset.valor;
          const peso = parseFloat(input.dataset.peso);
          if (valor === 'bueno') {
            if (!avance[seccion]) avance[seccion] = 0;
            avance[seccion] += peso;
          }
        });
        for (const seccion in pesos) {
          const id = 'barra_' + seccion.replace(/[^a-zA-Z0-9]/g, '_');
          const barra = document.getElementById(id);
          const actual = avance[seccion] || 0;
          const porcentaje = (actual / pesos[seccion]) * 100;
          barra.style.width = porcentaje + '%';
          barra.innerText = actual.toFixed(2) + '%';
        }
      });
    });
  </script>
</body>
</html>
