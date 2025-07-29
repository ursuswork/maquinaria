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
  <title>Recibo de Unidad</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #0b1d3a;
      color: #ffcc00;
      padding: 20px;
    }
    .seccion {
      background-color: #112e51;
      border-radius: 12px;
      padding: 15px;
      margin-bottom: 30px;
    }
    .form-label {
      font-weight: bold;
    }
    .barra-avance {
      height: 16px;
      border-radius: 8px;
      font-size: 0.75rem;
      line-height: 16px;
      font-weight: bold;
      text-align: center;
    }
    .btn-opcion {
      border-radius: 50px;
      font-size: 0.75rem;
    }
    .total-condicion {
      font-size: 3.5rem;
      font-weight: bold;
      color: #ffcc00;
      text-align: center;
    }
  </style>
</head>
<body>
<div class="container">
  <h3 class="text-center mb-4">Recibo de Unidad - <?= htmlspecialchars($maquinaria['nombre'] . " (" . $maquinaria['modelo'] . ")") ?></h3>
  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>" id="recibo_unidad">
    <?php foreach ($secciones as $nombre_seccion => $componentes): 
      $peso = $pesos[$nombre_seccion];
      $clave = strtolower(str_replace([' ', 'É', 'Ó'], ['_', 'E', 'O'], $nombre_seccion));
    ?>
    <div class="seccion">
      <h5><?= $nombre_seccion ?> (<?= $peso ?>%)</h5>
      <div class="mb-2">
        <div class="progress bg-dark">
          <div id="barra_<?= $clave ?>" class="progress-bar bg-success barra-avance" style="width: 0%">0%</div>
        </div>
      </div>
      <div class="row">
        <?php foreach ($componentes as $componente): 
          $valor = $recibo[$componente] ?? '';
          $input_id = md5($componente);
        ?>
        <div class="col-md-6 mb-2">
          <label class="form-label"><?= $componente ?></label>
          <div class="btn-group btn-group-sm d-flex" role="group">
            <?php foreach (['bueno', 'regular', 'malo'] as $opcion): 
              $clase = ($valor == $opcion) ? 'btn-primary' : 'btn-outline-primary';
            ?>
            <button type="button" class="btn btn-opcion flex-fill <?= $clase ?>" onclick="seleccionar('<?= $input_id ?>','<?= $opcion ?>', this)">
              <?= ucfirst($opcion) ?>
            </button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="componentes[<?= $componente ?>]" id="comp_<?= $input_id ?>" value="<?= $valor ?>">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <div class="seccion">
      <label class="form-label">Observaciones:</label>
      <textarea name="observaciones" class="form-control" rows="3"><?= $recibo['observaciones'] ?? '' ?></textarea>
    </div>

    <div class="text-center my-4">
      <h5>Condición Total Estimada</h5>
      <div class="total-condicion" id="total_avance">0%</div>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-warning px-5">Guardar Recibo</button>
    </div>
  </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", calcularAvance);

function calcularAvance() {
  const pesos = {
    "motor": 15,
    "sistema_mecanico": 15,
    "sistema_hidraulico": 30,
    "sistema_electrico_y_electronico": 25,
    "estetico": 5,
    "consumibles": 10
  };

  const secciones = <?= json_encode(array_map(function($componentes) {
    return array_map(fn($nombre) => md5($nombre), $componentes);
  }, $secciones)) ?>;

  let total = 0;

  for (let clave in secciones) {
    let buenos = 0;
    let totalCampos = secciones[clave].length;
    secciones[clave].forEach(id => {
      const valor = document.getElementById('comp_' + id).value;
      if (valor === 'bueno') buenos++;
    });

    const porcentaje = (buenos / totalCampos) * pesos[clave];
    total += porcentaje;

    const porcentajeVisual = Math.round((buenos / totalCampos) * 100);
    const barra = document.getElementById('barra_' + clave);
    if (barra) {
      barra.style.width = porcentajeVisual + '%';
      barra.textContent = porcentajeVisual + '%';
    }
  }

  document.getElementById('total_avance').textContent = Math.round(total) + '%';
}

function seleccionar(id, valor, boton) {
  const input = document.getElementById('comp_' + id);
  if (!input) return;
  input.value = valor;

  const grupo = boton.parentNode.querySelectorAll('button');
  grupo.forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
  boton.classList.replace('btn-outline-primary', 'btn-primary');

  calcularAvance();
}
</script>
</body>
</html>
