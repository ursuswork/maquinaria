<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) die("ID inválido");

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
    body { background-color: #0b1d3a; color: #ffc107; padding: 20px; }
    .seccion { background-color: #112e51; padding: 20px; border-radius: 12px; margin-bottom: 25px; }
    .barra-avance { height: 20px; background-color: #28a745; font-size: 0.8rem; line-height: 20px; text-align: center; color: white; transition: width 0.5s ease-in-out; }
    .btn-opcion { font-size: 0.85rem; border-radius: 20px; margin-top: 4px; }
    .btn-primary { background-color: #0066ff; border: none; }
    .btn-outline-primary { border: 1px solid #0066ff; color: #0066ff; }
    textarea, input[type=text] { background-color: #fff; color: #000; }
    .bg-relieve { background-color: #004080 !important; box-shadow: 0 0 12px #003366; border-radius: 10px; }
    @media print {
      .no-print { display: none; }
      body { color: #000; background: white; }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="text-center mb-4">
    <h2>Recibo de Unidad</h2>
    <h4><?= htmlspecialchars($maquinaria['nombre']) . " (" . htmlspecialchars($maquinaria['modelo']) . ")" ?></h4>
  </div>

  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>" id="reciboForm">
    <div class="row bg-relieve text-white p-4 mb-4">
      <div class="col-md-6 mb-3">
        <label class="fw-bold">Empresa Origen</label>
        <input type="text" name="empresa_origen" class="form-control" value="<?= htmlspecialchars($recibo['empresa_origen'] ?? '') ?>">
      </div>
      <div class="col-md-6 mb-3">
        <label class="fw-bold">Empresa Destino</label>
        <input type="text" name="empresa_destino" class="form-control" value="<?= htmlspecialchars($recibo['empresa_destino'] ?? '') ?>">
      </div>
      <div class="col-md-6 mb-2">
        <label class="fw-bold">Número de Serie</label>
        <div class="form-control bg-secondary text-white"><?= htmlspecialchars($maquinaria['numero_serie']) ?></div>
      </div>
      <div class="col-md-6 mb-2">
        <label class="fw-bold">Ubicación</label>
        <div class="form-control bg-secondary text-white"><?= htmlspecialchars($maquinaria['ubicacion']) ?></div>
      </div>
      <div class="col-md-6 mb-2">
        <label class="fw-bold">Tipo</label>
        <div class="form-control bg-secondary text-white"><?= htmlspecialchars($maquinaria['tipo_maquinaria']) ?></div>
      </div>
      <div class="col-md-6 mb-2">
        <label class="fw-bold">Subtipo</label>
        <div class="form-control bg-secondary text-white"><?= htmlspecialchars($maquinaria['subtipo']) ?></div>
      </div>
    </div>
    <div class="mb-4">
      <label class="form-label fw-bold text-white">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($recibo['observaciones'] ?? '') ?></textarea>
    </div>

    <div class="text-center my-4">
      <h5 class="text-white">Condición Total Estimada</h5>
      <h1 id="total_avance" style="color: yellow; font-size: 3rem;">0%</h1>
    </div>

    <div class="text-center mb-5 no-print">
      <button type="submit" class="btn btn-warning px-4">Guardar Recibo</button>
      <button type="button" onclick="window.print()" class="btn btn-outline-light ms-2">Imprimir</button>
    </div>
<?php foreach ($secciones as $nombre => $componentes): 
  $clave = strtolower(str_replace([" ", "É", "Ó", "Á", "Í", "Ú", "Ñ"], ["_", "E", "O", "A", "I", "U", "N"], $nombre));
?>
  <div class="seccion mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="text-warning m-0"><?= $nombre ?></h5>
      <small class="text-white"><?= $pesos[$nombre] ?>%</small>
    </div>
    <div class="progress mb-3" style="height: 20px;">
      <div class="barra-avance" id="barra_<?= $clave ?>" style="width:0%">0%</div>
    </div>

    <div class="row">
      <?php foreach ($componentes as $componente):
        $hash = md5($componente);
        $valor = $recibo[$componente] ?? '';
      ?>
        <div class="col-md-4 mb-3">
          <div class="bg-light text-dark p-2 rounded">
            <label class="fw-bold d-block mb-1"><?= $componente ?></label>
            <input type="hidden" name="componentes[<?= $componente ?>]" id="comp_<?= $hash ?>" value="<?= $valor ?>">
            <div class="btn-group w-100" role="group">
              <button type="button" class="btn btn-sm <?= $valor=='bueno' ? 'btn-primary' : 'btn-outline-primary' ?>" onclick="seleccionar('<?= $hash ?>','bueno',this)">Bueno</button>
              <button type="button" class="btn btn-sm <?= $valor=='regular' ? 'btn-primary' : 'btn-outline-primary' ?>" onclick="seleccionar('<?= $hash ?>','regular',this)">Regular</button>
              <button type="button" class="btn btn-sm <?= $valor=='malo' ? 'btn-primary' : 'btn-outline-primary' ?>" onclick="seleccionar('<?= $hash ?>','malo',this)">Malo</button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endforeach; ?>
    <div class="text-center no-print mb-5">
      <button type="submit" class="btn btn-warning px-4">Guardar Recibo</button>
      <button type="button" onclick="window.print()" class="btn btn-outline-light ms-2">Imprimir</button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', calcularAvance);

function seleccionar(id, valor, boton) {
  document.getElementById("comp_" + id).value = valor;
  let botones = boton.parentNode.querySelectorAll("button");
  botones.forEach(b => b.classList.replace('btn-primary','btn-outline-primary'));
  boton.classList.replace('btn-outline-primary','btn-primary');
  calcularAvance();
}

function calcularAvance() {
  const pesos = {
    motor: 15, sistema_mecanico: 15,
    sistema_hidraulico: 30, sistema_electrico_y_electronico: 25,
    estetico: 5, consumibles: 10
  };

  const secciones = {
    motor: <?= json_encode(array_map(fn($x)=>md5($x), $secciones['MOTOR'])) ?>,
    sistema_mecanico: <?= json_encode(array_map(fn($x)=>md5($x), $secciones['SISTEMA MECANICO'])) ?>,
    sistema_hidraulico: <?= json_encode(array_map(fn($x)=>md5($x), $secciones['SISTEMA HIDRAULICO'])) ?>,
    sistema_electrico_y_electronico: <?= json_encode(array_map(fn($x)=>md5($x), $secciones['SISTEMA ELECTRICO Y ELECTRONICO'])) ?>,
    estetico: <?= json_encode(array_map(fn($x)=>md5($x), $secciones['ESTETICO'])) ?>,
    consumibles: <?= json_encode(array_map(fn($x)=>md5($x), $secciones['CONSUMIBLES'])) ?>
  };

  let total = 0;
  for (let clave in secciones) {
    let buenos = secciones[clave].filter(id => document.getElementById("comp_" + id)?.value === "bueno").length;
    let porcentaje = (buenos / secciones[clave].length) * pesos[clave];
    total += porcentaje;

    let barra = document.getElementById("barra_" + clave);
    if (barra) {
      let porBarra = (buenos / secciones[clave].length) * 100;
      barra.style.width = porBarra.toFixed(0) + "%";
      barra.textContent = porBarra.toFixed(0) + "%";
    }
  }

  document.getElementById("total_avance").textContent = Math.round(total) + "%";
}
</script>
</body>
</html>
