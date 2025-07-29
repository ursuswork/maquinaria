<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) die("ID de maquinaria inválido");

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) die("Maquinaria no encontrada");

// Secciones y pesos
$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECANICO" => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
  "SISTEMA HIDRAULICO" => ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"],
  "SISTEMA ELECTRICO Y ELECTRONICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)"],
  "ESTETICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
  "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
];

$pesos = ["MOTOR"=>15,"SISTEMA MECANICO"=>15,"SISTEMA HIDRAULICO"=>30,"SISTEMA ELECTRICO Y ELECTRONICO"=>25,"ESTETICO"=>5,"CONSUMIBLES"=>10];
$recibo = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recibo de Unidad</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #0b1d3a; color: #ffcc00; }
    .seccion { background-color: #112e51; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
    .form-label { font-size: 0.85rem; font-weight: bold; }
    .btn-opcion { border-radius: 50px; font-size: 0.70rem; padding: 2px 10px; }
    .progress-bar { background-color: #28a745 !important; font-size: 0.75rem; font-weight: bold; }
    .barra-contenedor { background-color: #1c2a48; height: 20px; border-radius: 12px; }
    .datos-maquina { background: #112e51; padding: 10px 15px; border-radius: 8px; margin-bottom: 20px; }
    .datos-maquina p { margin: 0; font-size: 0.85rem; }
    .total-condicion { font-size: 3rem; font-weight: bold; color: yellow; text-align: center; margin-top: 30px; }
  </style>
</head>
<body>
<div class="container py-3">
  <h3 class="text-center mb-3">Recibo de Unidad</h3>

  <!-- Información de la maquinaria -->
  <div class="datos-maquina">
    <div class="row">
      <div class="col-md-4"><p><strong>Número de Serie:</strong> <?= $maquinaria['numero_serie'] ?></p></div>
      <div class="col-md-4"><p><strong>Modelo:</strong> <?= $maquinaria['modelo'] ?></p></div>
      <div class="col-md-4"><p><strong>Ubicación:</strong> <?= $maquinaria['ubicacion'] ?></p></div>
      <div class="col-md-6"><p><strong>Empresa Origen:</strong> <?= $maquinaria['empresa_origen'] ?? '' ?></p></div>
      <div class="col-md-6"><p><strong>Empresa Destino:</strong> <?= $maquinaria['empresa_destino'] ?? '' ?></p></div>
    </div>
  </div>

  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>" id="recibo_unidad">
    <?php foreach ($secciones as $nombre => $componentes): 
      $clave = strtolower(str_replace(' ', '_', $nombre));
    ?>
    <div class="seccion">
      <h5><?= $nombre ?> (<?= $pesos[$nombre] ?>%)</h5>
      <div class="barra-contenedor mb-2">
        <div id="barra_<?= $clave ?>" class="progress-bar text-center" style="width: 0%">0%</div>
      </div>
      <div class="row">
        <?php foreach ($componentes as $comp): 
          $id_input = "comp_" . md5($comp);
          $valor = $recibo[$comp] ?? '';
        ?>
        <div class="col-md-6 mb-2">
          <label class="form-label"><?= $comp ?></label>
          <div class="btn-group btn-group-sm" role="group">
            <?php foreach (['bueno','regular','malo'] as $opcion): 
              $clase = ($valor == $opcion) ? 'btn-primary' : 'btn-outline-primary';
            ?>
              <button type="button" class="btn btn-opcion <?= $clase ?>" onclick="seleccionar('<?= $id_input ?>', '<?= $opcion ?>', this)">
                <?= ucfirst($opcion) ?>
              </button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="componentes[<?= $comp ?>]" id="<?= $id_input ?>" value="<?= $valor ?>">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <div class="seccion">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control"><?= $recibo['observaciones'] ?? '' ?></textarea>
    </div>

    <div class="total-condicion">
      <span id="total_avance">0%</span>
    </div>

    <div class="text-center my-4">
      <button type="submit" class="btn btn-warning px-5">Guardar Recibo</button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', calcularAvance);

function calcularAvance() {
  const pesos = {
    "motor": 15, "sistema_mecanico": 15, "sistema_hidraulico": 30,
    "sistema_electrico_y_electronico": 25, "estetico": 5, "consumibles": 10
  };

  const secciones = {
    "motor": [<?= implode(",", array_map(fn($x) => "'" . md5($x) . "'", $secciones["MOTOR"])) ?>],
    "sistema_mecanico": [<?= implode(",", array_map(fn($x) => "'" . md5($x) . "'", $secciones["SISTEMA MECANICO"])) ?>],
    "sistema_hidraulico": [<?= implode(",", array_map(fn($x) => "'" . md5($x) . "'", $secciones["SISTEMA HIDRAULICO"])) ?>],
    "sistema_electrico_y_electronico": [<?= implode(",", array_map(fn($x) => "'" . md5($x) . "'", $secciones["SISTEMA ELECTRICO Y ELECTRONICO"])) ?>],
    "estetico": [<?= implode(",", array_map(fn($x) => "'" . md5($x) . "'", $secciones["ESTETICO"])) ?>],
    "consumibles": [<?= implode(",", array_map(fn($x) => "'" . md5($x) . "'", $secciones["CONSUMIBLES"])) ?>]
  };

  let total = 0;

  for (const seccion in secciones) {
    const elementos = secciones[seccion];
    const buenos = elementos.filter(id => document.getElementById('comp_' + id)?.value === 'bueno').length;
    const porcentaje = (buenos / elementos.length) * pesos[seccion];
    total += porcentaje;

    const barra = document.getElementById('barra_' + seccion);
    if (barra) {
      const pct = Math.round((buenos / elementos.length) * 100);
      barra.style.width = pct + '%';
      barra.textContent = pct + '%';
    }
  }

  document.getElementById('total_avance').textContent = `${Math.round(total)}%`;
}

function seleccionar(inputId, valor, btn) {
  document.getElementById(inputId).value = valor;
  const grupo = btn.parentNode.querySelectorAll("button");
  grupo.forEach(b => b.classList.replace("btn-primary", "btn-outline-primary"));
  btn.classList.replace("btn-outline-primary", "btn-primary");
  calcularAvance();
}
</script>
</body>
</html>

