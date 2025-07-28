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
<div class="mt-5">
  <label class="form-label">Avance Total Estimado</label>
  <div class="progress">
    <div id="barra_total" class="progress-bar bg-warning" style="width: 0%">0%</div>
  </div>
</div>