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
      color: yellow;
      padding: 20px;
    }
    .seccion {
      background-color: #112e51;
      border-radius: 12px;
      padding: 15px;
      margin-bottom: 25px;
    }
    .seccion h5 {
      border-bottom: 2px solid #ffc107;
      padding-bottom: 5px;
      margin-bottom: 15px;
    }
    .form-label {
      font-weight: bold;
      font-size: 0.9rem;
    }
    .btn-opcion {
      border-radius: 50px;
      font-size: 0.7rem;
      padding: 2px 10px;
    }
    .progress {
      background-color: #2a2a2a;
      height: 18px;
      border-radius: 20px;
      margin-bottom: 10px;
    }
    .barra-avance {
      background-color: #28a745;
      text-align: center;
      font-weight: bold;
      color: black;
      border-radius: 20px;
    }
    .total-condicion {
      font-size: 3rem;
      font-weight: bold;
      color: yellow;
      text-align: center;
      margin-top: 30px;
    }
  </style>
</head>
<body>
<div class="container">
  <h3 class="text-center mb-4">Recibo de Unidad - <?= $maquinaria['nombre'] . " (" . $maquinaria['modelo'] . ")" ?></h3>
  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>" id="form_recibo">
    <?php foreach ($secciones as $nombre_seccion => $componentes): 
      $clave = strtolower(str_replace(' ', '_', $nombre_seccion));
      $peso = $pesos[$nombre_seccion];
    ?>
      <div class="seccion">
        <h5><?= $nombre_seccion ?> (<?= $peso ?>%)</h5>
        <div class="progress">
          <div class="progress-bar barra-avance" id="barra_<?= $clave ?>" style="width: 0%">0%</div>
        </div>
        <div class="row">
          <?php foreach ($componentes as $nombre): 
            $valor = $recibo[$nombre] ?? '';
            $id_input = "comp_" . md5($nombre);
          ?>
          <div class="col-md-6 mb-2">
            <label class="form-label"><?= $nombre ?></label>
            <div class="btn-group btn-group-sm" role="group">
              <?php foreach (['bueno', 'regular', 'malo'] as $opcion): 
                $btn_class = ($valor == $opcion) ? 'btn-primary' : 'btn-outline-primary';
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

    <div class="total-condicion">
      <div>Condición Total Estimada</div>
      <div id="total_avance">0%</div>
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-warning px-5">Guardar Recibo</button>
    </div>
  </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", calcularAvance);

function seleccionar(input_id, valor, boton) {
  const input = document.getElementById(input_id);
  if (!input) return;
  input.value = valor;

  const grupo = boton.parentNode.querySelectorAll("button");
  grupo.forEach(btn => btn.classList.replace("btn-primary", "btn-outline-primary"));
  boton.classList.replace("btn-outline-primary", "btn-primary");

  calcularAvance();
}

function calcularAvance() {
  const secciones = {
    motor: [<?= implode(",", array_map(fn($x) => "'" . md5($x) . "'", $secciones["MOTOR"])) ?>],
    sistema_mecanico: [<?= implode(",", array_map(fn($x) => "'" . md5($x) . "'", $secciones["SISTEMA MECANICO"])) ?>],
    sistema_hidraulico: [<?= implode(",", array_map(fn($x) => "'" . md5($x) . "'", $secciones["SISTEMA HIDRAULICO"])) ?>],
    sistema_electrico_y_electronico: [<?= implode(",", array_map(fn($x) => "'" . md5($x) . "'", $secciones["SISTEMA ELECTRICO Y ELECTRONICO"])) ?>],
    estetico: [<?= implode(",", array_map(fn($x) => "'" . md5($x) . "'", $secciones["ESTETICO"])) ?>],
    consumibles: [<?= implode(",", array_map(fn($x) => "'" . md5($x) . "'", $secciones["CONSUMIBLES"])) ?>]
  };

  const pesos = {
    motor: 15,
    sistema_mecanico: 15,
    sistema_hidraulico: 30,
    sistema_electrico_y_electronico: 25,
    estetico: 5,
    consumibles: 10
  };

  let total = 0;

  for (const key in secciones) {
    let buenos = 0;
    const campos = secciones[key];
    campos.forEach(id => {
      const valor = document.getElementById("comp_" + id).value;
      if (valor === "bueno") buenos++;
    });

    const porcentaje = campos.length > 0 ? (buenos / campos.length) * pesos[key] : 0;
    total += porcentaje;

    const barra = document.getElementById("barra_" + key);
    if (barra) {
      const pct = Math.round((buenos / campos.length) * 100);
      barra.style.width = pct + "%";
      barra.textContent = pct + "%";
    }
  }

  document.getElementById("total_avance").textContent = Math.round(total) + "%";
}
</script>
</body>
</html>
