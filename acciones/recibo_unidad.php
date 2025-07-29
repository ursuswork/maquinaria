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
    body { background-color: #0b1d3a; color: #ffc107; padding: 20px; }
    .seccion { background-color: #112e51; padding: 20px; border-radius: 12px; margin-bottom: 25px; }
    .barra-avance { height: 16px; background-color: #28a745; font-size: 0.8rem; line-height: 16px; text-align: center; color: white; }
    .btn-opcion { font-size: 0.8rem; border-radius: 20px; margin-top: 5px; }
    .btn-primary { background-color: #0066ff; border: none; }
    .btn-outline-primary { border: 1px solid #0066ff; color: #0066ff; }
    textarea, input[type=text] { background-color: #fff; color: #000; }
    .label-info { font-weight: bold; margin-bottom: 3px; }
    @media print {
      .no-print { display: none; }
      body { color: #000; background: white; }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="text-center mb-3">
    <h2>Recibo de Unidad</h2>
    <h4><?= htmlspecialchars($maquinaria['nombre']) . " (" . htmlspecialchars($maquinaria['modelo']) . ")" ?></h4>
  </div>

  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>" id="reciboForm">
    <div class="row mb-4">
  <div class="col-md-6">
    <label class="label-info">Empresa Origen</label>
    <input type="text" name="empresa_origen" class="form-control" value="<?= htmlspecialchars($recibo['empresa_origen'] ?? '') ?>">
  </div>
  <div class="col-md-6">
    <label class="label-info">Empresa Destino</label>
    <input type="text" name="empresa_destino" class="form-control" value="<?= htmlspecialchars($recibo['empresa_destino'] ?? '') ?>">
  </div>
  <div class="col-md-6 mt-2">
    <label class="label-info">Número de Serie</label>
    <div class="form-control-plaintext"><?= htmlspecialchars($maquinaria['numero_serie']) ?></div>
  </div>
  <div class="col-md-6 mt-2">
    <label class="label-info">Ubicación</label>
    <div class="form-control-plaintext"><?= htmlspecialchars($maquinaria['ubicacion']) ?></div>
  </div>
  <div class="col-md-6 mt-2">
    <label class="label-info">Tipo</label>
    <div class="form-control-plaintext"><?= htmlspecialchars($maquinaria['tipo_maquinaria']) ?></div>
  </div>
  <div class="col-md-6 mt-2">
    <label class="label-info">Subtipo</label>
    <div class="form-control-plaintext"><?= htmlspecialchars($maquinaria['subtipo']) ?></div>
  </div>
</div>

    <?php foreach ($secciones as $nombre => $componentes):
      $clave = strtolower(str_replace(' ', '_', $nombre));
      $peso = $pesos[$nombre];
    ?>
    <div class="seccion">
      <h5><?= $nombre ?> (<?= $peso ?>%)</h5>
      <div class="progress mb-3">
        <div id="barra_<?= $clave ?>" class="barra-avance" style="width:0%">0%</div>
      </div>
      <div class="row">
        <?php foreach ($componentes as $componente):
          $id = md5($componente);
          $valor = $recibo[$componente] ?? '';
        ?>
        <div class="col-md-6 mb-2">
          <label class="d-block mb-1"><?= $componente ?></label>
          <div class="btn-group btn-group-sm d-flex gap-1" role="group">
            <?php foreach (['bueno', 'regular', 'malo'] as $opcion):
              $clase = ($valor == $opcion) ? 'btn-primary' : 'btn-outline-primary';
            ?>
              <button type="button" class="btn btn-opcion <?= $clase ?>" onclick="seleccionar('<?= $id ?>', '<?= $opcion ?>', this)"><?= ucfirst($opcion) ?></button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="componentes[<?= $componente ?>]" id="comp_<?= $id ?>" value="<?= $valor ?>">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <div class="mb-3">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($recibo['observaciones'] ?? '') ?></textarea>
    </div>

    <div class="text-center my-4">
      <h5>Condición Total Estimada</h5>
      <h1 id="total_avance" style="color: yellow; font-size: 3rem;">0%</h1>
    </div>

    <div class="text-center no-print">
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
