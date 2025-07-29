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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #0a1a2f;
      color: #ffeb3b;
    }
    .seccion {
      background-color: #10243f;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 25px;
    }
    .barra-avance {
      height: 18px;
      font-weight: bold;
    }
    .btn-opcion {
      border-radius: 25px;
      font-size: 0.75rem;
    }
    .form-label {
      font-weight: bold;
    }
    .info-maquina .row > div {
      padding: 5px 15px;
    }
    h3, h5 {
      color: #fff;
    }
  </style>
</head>
<body>
<div class="container my-4">
  <h3 class="text-center mb-3">Recibo de Unidad</h3>

  <div class="seccion info-maquina">
    <div class="row">
      <div class="col-md-4"><strong>Número de Serie:</strong> <?= $maquinaria['numero_serie'] ?></div>
      <div class="col-md-4"><strong>Modelo:</strong> <?= $maquinaria['modelo'] ?></div>
      <div class="col-md-4"><strong>Ubicación:</strong> <?= $maquinaria['ubicacion'] ?></div>
    </div>
    <div class="row">
      <div class="col-md-4"><strong>Tipo:</strong> <?= $maquinaria['tipo_maquinaria'] ?></div>
      <div class="col-md-4"><strong>Subtipo:</strong> <?= $maquinaria['subtipo'] ?></div>
      <div class="col-md-4">
        <label><strong>Empresa Origen:</strong></label>
        <input type="text" class="form-control" name="empresa_origen" form="formulario" value="<?= $recibo['empresa_origen'] ?? '' ?>">
      </div>
    </div>
    <div class="row mt-2">
      <div class="col-md-6">
        <label><strong>Empresa Destino:</strong></label>
        <input type="text" class="form-control" name="empresa_destino" form="formulario" value="<?= $recibo['empresa_destino'] ?? '' ?>">
      </div>
    </div>
  </div>

  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>" id="formulario">
    <?php foreach ($secciones as $nombre_seccion => $componentes): 
      $peso = $pesos[$nombre_seccion];
      $id_barra = "barra_" . md5($nombre_seccion);
    ?>
    <div class="seccion">
      <h5><?= $nombre_seccion ?> (<?= $peso ?>%)</h5>
      <div class="progress bg-dark mb-3">
        <div class="progress-bar bg-success barra-avance" id="<?= $id_barra ?>" role="progressbar">0%</div>
      </div>
      <div class="row">
        <?php foreach ($componentes as $componente): 
          $valor = $recibo[$componente] ?? '';
          $id_input = "comp_" . md5($componente);
        ?>
        <div class="col-md-6 mb-2">
          <label class="form-label"><?= $componente ?></label><br>
          <div class="btn-group btn-group-sm" role="group">
            <?php foreach (['bueno', 'regular', 'malo'] as $opcion): 
              $class = ($valor == $opcion) ? 'btn-primary' : 'btn-outline-primary';
            ?>
            <button type="button" class="btn btn-opcion <?= $class ?>" onclick="seleccionar('<?= $id_input ?>','<?= $opcion ?>', this)">
              <?= ucfirst($opcion) ?>
            </button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="componentes[<?= $componente ?>]" id="<?= $id_input ?>" value="<?= $valor ?>">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <div class="seccion">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"><?= $recibo['observaciones'] ?? '' ?></textarea>
    </div>

    <div class="text-center my-4">
      <h5>Condición Total Estimada</h5>
      <h1 id="total_avance" style="color: yellow; font-size: 3.5rem;">0%</h1>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-warning px-4">Guardar Recibo</button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', calcularAvance);

function calcularAvance() {
  const pesos = {
    "<?= md5('MOTOR') ?>": 15,
    "<?= md5('SISTEMA MECANICO') ?>": 15,
    "<?= md5('SISTEMA HIDRAULICO') ?>": 30,
    "<?= md5('SISTEMA ELECTRICO Y ELECTRONICO') ?>": 25,
    "<?= md5('ESTETICO') ?>": 5,
    "<?= md5('CONSUMIBLES') ?>": 10
  };

  let total = 0;

  <?php foreach ($secciones as $nombre_seccion => $componentes): 
    $id_barra = "barra_" . md5($nombre_seccion);
    $ids = array_map(fn($x) => "comp_" . md5($x), $componentes);
  ?>
  let buenos_<?= md5($nombre_seccion) ?> = 0;
  let total_<?= md5($nombre_seccion) ?> = <?= count($componentes) ?>;

  <?php foreach ($ids as $id): ?>
  if (document.getElementById("<?= $id ?>").value === "bueno") {
    buenos_<?= md5($nombre_seccion) ?>++;
  }
  <?php endforeach; ?>

  let porcentaje_<?= md5($nombre_seccion) ?> = (buenos_<?= md5($nombre_seccion) ?> / total_<?= md5($nombre_seccion) ?>) * 100;
  let barra_<?= md5($nombre_seccion) ?> = document.getElementById("<?= $id_barra ?>");
  barra_<?= md5($nombre_seccion) ?>.style.width = porcentaje_<?= md5($nombre_seccion) ?> + "%";
  barra_<?= md5($nombre_seccion) ?>.innerText = Math.round(porcentaje_<?= md5($nombre_seccion) ?>) + "%";

  total += (buenos_<?= md5($nombre_seccion) ?> / total_<?= md5($nombre_seccion) ?>) * pesos["<?= md5($nombre_seccion) ?>"];
  <?php endforeach; ?>

  document.getElementById("total_avance").innerText = Math.round(total) + "%";
}

function seleccionar(id, valor, boton) {
  let input = document.getElementById(id);
  input.value = valor;

  let grupo = boton.parentNode.querySelectorAll("button");
  grupo.forEach(btn => {
    btn.classList.remove("btn-primary");
    btn.classList.add("btn-outline-primary");
  });

  boton.classList.add("btn-primary");
  boton.classList.remove("btn-outline-primary");

  calcularAvance();
}
</script>
</body>
</html>
