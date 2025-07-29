<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) {
  die("ID de maquinaria inválido");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) {
  die("Maquinaria no encontrada");
}

$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECANICO" => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
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

$recibo = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recibo de Unidad - <?= $maquinaria['nombre'] ?? '' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #0b1d3a;
      color: #ffc107;
      padding: 20px;
    }
    .seccion {
      background-color: #112e51;
      border-radius: 12px;
      padding: 15px;
      margin-bottom: 25px;
    }
    .form-label {
      font-weight: bold;
    }
    .barra-avance {
      height: 14px;
      border-radius: 10px;
      background-color: #28a745 !important;
      position: relative;
    }
    .barra-avance span {
      position: absolute;
      width: 100%;
      text-align: center;
      font-size: 0.75rem;
      color: #fff;
    }
    .btn-opcion {
      border-radius: 50px;
      font-size: 0.7rem;
    }
    .btn-warning {
      background-color: #ffc107;
      border: none;
      color: black;
    }
    .total-condicion {
      font-size: 3rem;
      font-weight: bold;
      color: #ffc107;
      text-align: center;
    }
    .info-maquina {
      background: #112e51;
      border-radius: 10px;
      padding: 10px 20px;
      margin-bottom: 20px;
    }
    .info-maquina p {
      margin: 5px 0;
    }
  </style>
</head>
<body>
<div class="container">
  <h3 class="text-center mb-3">Recibo de Unidad - <?= $maquinaria['nombre'] . " (" . $maquinaria['modelo'] . ")" ?></h3>

  <div class="info-maquina">
    <p><strong>Empresa Origen:</strong> <?= $maquinaria['empresa_origen'] ?? 'N/A' ?></p>
    <p><strong>Empresa Destino:</strong> <?= $maquinaria['empresa_destino'] ?? 'N/A' ?></p>
    <p><strong>Número de Serie:</strong> <?= $maquinaria['numero_serie'] ?? 'N/A' ?></p>
    <p><strong>Tipo:</strong> <?= ucfirst($maquinaria['tipo_maquinaria']) ?? 'N/A' ?></p>
  </div>

  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>" id="recibo_unidad">
    <?php foreach ($secciones as $nombre_seccion => $componentes):
      $peso = $pesos[$nombre_seccion];
      $id_barra = "barra_" . str_replace(' ', '_', strtolower($nombre_seccion));
    ?>
    <div class="seccion">
      <h5><?= $nombre_seccion ?> (<?= $peso ?>%)</h5>
      <div class="progress mb-3 bg-secondary">
        <div id="<?= $id_barra ?>" class="progress-bar barra-avance" role="progressbar" style="width: 0%">
          <span id="texto_<?= $id_barra ?>">0%</span>
        </div>
      </div>
      <div class="row">
        <?php foreach ($componentes as $nombre):
          $valor = $recibo[$nombre] ?? '';
          $id_input = "comp_" . md5($nombre);
        ?>
        <div class="col-md-6 mb-3">
          <label class="form-label"><?= $nombre ?></label>
          <div class="d-flex gap-1 flex-wrap">
            <?php foreach (['bueno', 'regular', 'malo'] as $opcion): 
              $btn_class = ($valor == $opcion) ? 'btn-warning' : 'btn-outline-light';
            ?>
              <button type="button" class="btn btn-opcion <?= $btn_class ?>" onclick="seleccionar('<?= $id_input ?>','<?= $opcion ?>', this)">
                <?= ucfirst($opcion) ?>
              </button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="componentes[<?= $nombre ?>]" id="<?= $id_input ?>" value="<?= $valor ?>">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <div class="seccion">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"><?= $recibo['observaciones'] ?? '' ?></textarea>
    </div>

    <div class="text-center">
      <h4>Condición Total Estimada</h4>
      <div class="total-condicion" id="total_avance">0%</div>
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-warning px-4">Guardar Recibo</button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', calcularAvance);

function calcularAvance() {
  const pesos = {
    "motor": 15,
    "sistema_mecanico": 15,
    "sistema_hidraulico": 30,
    "sistema_electrico_y_electronico": 25,
    "estetico": 5,
    "consumibles": 10
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
    let buenos = 0;
    const inputs = secciones[seccion];
    inputs.forEach(id => {
      const val = document.getElementById("comp_" + id).value;
      if (val === "bueno") buenos++;
    });
    const porcentaje = (buenos / inputs.length) * 100;
    const avance = (buenos / inputs.length) * pesos[seccion];
    total += avance;

    const barra = document.getElementById("barra_" + seccion);
    const texto = document.getElementById("texto_barra_" + seccion);
    if (barra) {
      barra.style.width = porcentaje + "%";
      barra.querySelector("span").textContent = Math.round(porcentaje) + "%";
    }
  }

  document.getElementById("total_avance").textContent = `${Math.round(total)}%`;
}

function seleccionar(id, valor, boton) {
  const input = document.getElementById(id);
  input.value = valor;

  const grupo = boton.parentNode.querySelectorAll("button");
  grupo.forEach(btn => btn.classList.remove("btn-warning"));
  grupo.forEach(btn => btn.classList.add("btn-outline-light"));
  boton.classList.add("btn-warning");
  boton.classList.remove("btn-outline-light");

  calcularAvance();
}
</script>
</body>
</html>
