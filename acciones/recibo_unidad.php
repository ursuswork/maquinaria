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

$pesos = [
  "MOTOR" => 15,
  "SISTEMA MECÁNICO" => 15,
  "SISTEMA HIDRÁULICO" => 30,
  "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => 25,
  "ESTÉTICO" => 5,
  "CONSUMIBLES" => 10
];

$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECÁNICO" => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
  "SISTEMA HIDRÁULICO" => ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"],
  "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)", "SENSORES"],
  "ESTÉTICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
  "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
];

$porcentajes = [];
foreach ($secciones as $nombre => $lista) {
  $por_componente = $pesos[$nombre] / count($lista);
  foreach ($lista as $comp) {
    $porcentajes[$comp] = $por_componente;
  }
}

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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recibo de Unidad</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
<div class="container py-4">
  <h2 class="text-center mb-4">Recibo de Unidad: <?php echo htmlspecialchars($maquinaria['nombre']); ?></h2>
  <form method="post">
    <?php foreach ($secciones as $nombre => $componentes): ?>
      <h4 class="mt-4"><?php echo $nombre; ?></h4>
      <div class="progress mb-2">
        <div id="barra_<?php echo preg_replace('/[^a-zA-Z0-9]/', '_', $nombre); ?>" class="progress-bar bg-success" style="width: 0%">0%</div>
      </div>
      <div class="row">
        <?php foreach ($componentes as $componente): 
          $valor = $recibo_existente[$componente] ?? '';
        ?>
          <div class="col-md-4 mb-2">
            <label class="form-label fw-bold"><?php echo $componente; ?></label><br>
            <?php foreach (["bueno", "regular", "malo"] as $opcion): ?>
              <div class="form-check form-check-inline">
                <input class="form-check-input componente-radio" type="radio" name="componentes[<?php echo $componente; ?>]" value="<?php echo $opcion; ?>"
                  data-seccion="<?php echo $nombre; ?>" data-peso="<?php echo $porcentajes[$componente]; ?>"
                  <?php if ($valor === $opcion) echo 'checked'; ?>>
                <label class="form-check-label"><?php echo ucfirst($opcion); ?></label>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    <div class="mt-4">
      <label for="observaciones" class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"><?php echo htmlspecialchars($recibo_existente['observaciones'] ?? ''); ?></textarea>
    </div>
    <div class="mt-4">
      <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
  </form>
  <div class="mt-5">
    <label class="form-label">Avance Total Estimado</label>
    <div class="progress">
      <div id="barra_total" class="progress-bar bg-warning" style="width: 0%">0%</div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  function actualizarBarras() {
    const pesos = <?php echo json_encode($pesos); ?>;
    const porcentajes = <?php echo json_encode($porcentajes); ?>;
    const avanceSecciones = {};
    let total = 0;

    document.querySelectorAll('.componente-radio:checked').forEach(input => {
      const seccion = input.dataset.seccion;
      const peso = parseFloat(input.dataset.peso);
      if (input.value === 'bueno') {
        avanceSecciones[seccion] = (avanceSecciones[seccion] || 0) + peso;
        total += peso;
      }
    });

    for (const seccion in pesos) {
      const barra = document.getElementById('barra_' + seccion.replace(/[^a-zA-Z0-9]/g, '_'));
      const porcentaje = (avanceSecciones[seccion] || 0) / pesos[seccion] * 100;
      if (barra) {
        barra.style.width = porcentaje + '%';
        barra.innerText = (avanceSecciones[seccion] || 0).toFixed(2) + '%';
      }
    }

    const barraTotal = document.getElementById('barra_total');
    if (barraTotal) {
      barraTotal.style.width = total + '%';
      barraTotal.innerText = total.toFixed(2) + '%';
    }
  }

  document.querySelectorAll('.componente-radio').forEach(r => r.addEventListener('change', actualizarBarras));
  actualizarBarras();
});
</script>
</body>
</html>
