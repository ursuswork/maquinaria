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

$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECANICO" => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
  "SISTEMA HIDRAULICO" => ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"],
  "SISTEMA ELECTRICO Y ELECTRONICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)"],
  "ESTETICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
  "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
];

$pesos = [
  "MOTOR" => 15, "SISTEMA MECANICO" => 15,
  "SISTEMA HIDRAULICO" => 30, "SISTEMA ELECTRICO Y ELECTRONICO" => 25,
  "ESTETICO" => 5, "CONSUMIBLES" => 10
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
      background-color: #0b1d3a;
      color: #ffc107;
      padding: 20px;
    }
    .seccion {
      background-color: #102a4d;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 25px;
    }
    h3, h5 { color: #ffc107; }
    .progress-bar { background-color: #28a745; }
    .btn-opcion {
      font-size: 0.9rem;
      border-radius: 30px;
    }
    .btn-primary { background-color: #0056b3; border: none; }
    .btn-outline-primary { border-color: #0056b3; color: #0056b3; }
    .btn-outline-primary:hover { background-color: #0056b3; color: white; }
    label { font-weight: bold; }
    .barra-porcentaje { font-size: 0.85rem; position: absolute; left: 50%; transform: translateX(-50%); color: #fff; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="text-center mb-4">Recibo de Unidad</h3>
  <h4 class="text-center"><?= $maquinaria['nombre'] ?></h4>

  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>" id="recibo_unidad">
    <div class="row mb-3">
      <div class="col-md-6">
        <label>Empresa Origen</label>
        <input type="text" name="empresa_origen" class="form-control" value="<?= $recibo['empresa_origen'] ?? '' ?>">
      </div>
      <div class="col-md-6">
        <label>Empresa Destino</label>
        <input type="text" name="empresa_destino" class="form-control" value="<?= $recibo['empresa_destino'] ?? '' ?>">
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-4"><strong>Modelo:</strong> <?= $maquinaria['modelo'] ?></div>
      <div class="col-md-4"><strong>Ubicación:</strong> <?= $maquinaria['ubicacion'] ?></div>
      <div class="col-md-4"><strong>N° Serie:</strong> <?= $maquinaria['numero_serie'] ?></div>
    </div>

    <?php foreach ($secciones as $nombre => $componentes): 
      $clave = strtolower(str_replace(' ', '_', $nombre));
    ?>
      <div class="seccion">
        <h5><?= $nombre ?> (<?= $pesos[$nombre] ?>%)</h5>
        <div class="progress position-relative mb-3">
          <div id="barra_<?= $clave ?>" class="progress-bar" style="width: 0%"></div>
          <div id="porcentaje_<?= $clave ?>" class="barra-porcentaje">0%</div>
        </div>
        <div class="row">
          <?php foreach ($componentes as $comp): 
            $valor = $recibo[$comp] ?? '';
            $id = md5($comp);
          ?>
            <div class="col-md-6 mb-2">
              <label><?= $comp ?></label>
              <div class="btn-group btn-group-sm d-flex gap-1 mt-1">
                <?php foreach (['bueno', 'regular', 'malo'] as $op): 
                  $active = $valor === $op ? 'btn-primary' : 'btn-outline-primary';
                ?>
                  <button type="button" class="btn btn-opcion <?= $active ?>" onclick="seleccionar('<?= $id ?>','<?= $op ?>', this)">
                    <?= ucfirst($op) ?>
                  </button>
                <?php endforeach; ?>
              </div>
              <input type="hidden" name="componentes[<?= $comp ?>]" id="comp_<?= $id ?>" value="<?= $valor ?>">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="mb-3">
      <label>Observaciones</label>
      <textarea name="observaciones" class="form-control"><?= $recibo['observaciones'] ?? '' ?></textarea>
    </div>

    <div class="text-center my-4">
      <h4>Condición Estimada</h4>
      <h1 id="total_avance" style="color: yellow;">0%</h1>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-success px-5">Guardar</button>
      <button type="button" class="btn btn-outline-light ms-2" onclick="window.print()">Imprimir</button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', calcularAvance);

function seleccionar(id, valor, boton) {
  document.getElementById("comp_" + id).value = valor;
  let grupo = boton.parentNode.querySelectorAll("button");
  grupo.forEach(btn => btn.classList.replace("btn-primary", "btn-outline-primary"));
  boton.classList.replace("btn-outline-primary", "btn-primary");
  calcularAvance();
}

function calcularAvance() {
  const pesos = {
    "motor": 15, "sistema_mecanico": 15, "sistema_hidraulico": 30,
    "sistema_electrico_y_electronico": 25, "estetico": 5, "consumibles": 10
  };
  const secciones = <?= json_encode($secciones) ?>;
  let total = 0;

  for (const nombre in secciones) {
    const clave = nombre.toLowerCase().replace(/ /g, '_');
    const ids = secciones[nombre].map(txt => "comp_" + md5(txt));
    let buenos = 0;

    ids.forEach(id => {
      let el = document.getElementById(id);
      if (el && el.value === "bueno") buenos++;
    });

    const porcentaje = (buenos / ids.length) * pesos[nombre];
    total += porcentaje;

    let barra = document.getElementById("barra_" + clave);
    let texto = document.getElementById("porcentaje_" + clave);
    if (barra && texto) {
      let avance = (buenos / ids.length) * 100;
      barra.style.width = avance + "%";
      texto.innerText = Math.round(avance) + "%";
    }
  }

  document.getElementById("total_avance").innerText = Math.round(total) + "%";
}

function md5(str) {
  return str.split('').reduce((s, c) => s + c.charCodeAt(0).toString(16), '');
}
</script>
</body>
</html>
