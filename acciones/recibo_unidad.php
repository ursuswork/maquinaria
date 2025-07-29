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
    body {
      background-color: #0b1d3a;
      color: #ffc107;
      padding: 20px;
    }
    .seccion {
      background-color: #112e51;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 25px;
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
    }
    .barra-avance {
      height: 18px;
      font-size: 0.85rem;
      line-height: 18px;
      text-align: center;
      color: white;
      background-color: #28a745;
    }
    .barra-porcentaje {
      font-weight: bold;
      margin-bottom: 8px;
    }
    .btn-opcion {
      font-size: 0.9rem;
      border-radius: 20px;
      margin-top: 5px;
      padding: 6px 14px;
      font-weight: bold;
    }
    .btn-outline-primary {
      border: 2px solid #0066ff;
      color: #0066ff;
      background-color: #0b1d3a;
      box-shadow: 1px 1px 4px rgba(255,255,255,0.2);
    }
    .btn-primary {
      background-color: #0066ff;
      border: 2px solid #0066ff;
      box-shadow: 2px 2px 6px rgba(255,255,255,0.4);
    }
    input[type=text], textarea {
      background-color: white;
      color: black;
    }
    .info-box {
      background: #134c85;
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: inset 0 0 10px rgba(0,0,0,0.2);
    }
    @media print {
      .no-print { display: none; }
      body { color: black; background: white; }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="text-center mb-4">
    <h2 class="text-warning fw-bold">Recibo de Unidad</h2>
    <h4 class="text-light"><?= htmlspecialchars($maquinaria['nombre']) . " (" . htmlspecialchars($maquinaria['modelo']) . ")" ?></h4>
  </div>

  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>" id="reciboForm">

    <div class="info-box row">
      <div class="col-md-6 mb-3">
        <label class="text-white fw-bold">Empresa Origen</label>
        <input type="text" name="empresa_origen" class="form-control" value="<?= htmlspecialchars($recibo['empresa_origen'] ?? '') ?>">
      </div>
      <div class="col-md-6 mb-3">
        <label class="text-white fw-bold">Empresa Destino</label>
        <input type="text" name="empresa_destino" class="form-control" value="<?= htmlspecialchars($recibo['empresa_destino'] ?? '') ?>">
      </div>

      <div class="col-md-6 mb-2">
        <label class="text-white">Número de Serie</label>
        <div class="form-control bg-secondary text-white"><?= htmlspecialchars($maquinaria['numero_serie']) ?></div>
      </div>
      <div class="col-md-6 mb-2">
        <label class="text-white">Ubicación</label>
        <div class="form-control bg-secondary text-white"><?= htmlspecialchars($maquinaria['ubicacion']) ?></div>
      </div>

      <div class="col-md-6 mb-2">
        <label class="text-white">Tipo</label>
        <div class="form-control bg-secondary text-white"><?= htmlspecialchars($maquinaria['tipo_maquinaria']) ?></div>
      </div>
      <div class="col-md-6 mb-2">
        <label class="text-white">Subtipo</label>
        <div class="form-control bg-secondary text-white"><?= htmlspecialchars($maquinaria['subtipo']) ?></div>
      </div>
    </div>
    <?php foreach ($secciones as $nombre_seccion => $componentes): 
      $clave_id = strtolower(str_replace([' ', 'Ó', 'É', 'Á', 'Í', 'Ú', 'Ñ'], ['_', 'O', 'E', 'A', 'I', 'U', 'N'], $nombre_seccion));
    ?>
    <div class="seccion mb-4">
      <h5 class="text-warning"><?= $nombre_seccion ?> (<?= $pesos[$nombre_seccion] ?>%)</h5>
      <div class="progress mb-3">
        <div id="barra_<?= $clave_id ?>" class="progress-bar barra-avance" role="progressbar" style="width:0%">0%</div>
      </div>
      <div class="row">
        <?php foreach ($componentes as $comp): 
          $valor_actual = $recibo[$comp] ?? '';
          $id_hash = md5($comp);
        ?>
        <div class="col-md-4 mb-3">
          <div class="card p-2 bg-dark text-light shadow-sm">
            <div class="fw-bold mb-2 text-center"><?= $comp ?></div>
            <input type="hidden" name="componentes[<?= htmlspecialchars($comp) ?>]" id="comp_<?= $id_hash ?>" value="<?= $valor_actual ?>">
            <div class="text-center">
              <button type="button" class="btn btn-sm btn-outline-primary btn-opcion <?= $valor_actual === 'bueno' ? 'btn-primary' : '' ?>" onclick="seleccionar('<?= $id_hash ?>','bueno',this)">Bueno</button>
              <button type="button" class="btn btn-sm btn-outline-primary btn-opcion <?= $valor_actual === 'regular' ? 'btn-primary' : '' ?>" onclick="seleccionar('<?= $id_hash ?>','regular',this)">Regular</button>
              <button type="button" class="btn btn-sm btn-outline-primary btn-opcion <?= $valor_actual === 'malo' ? 'btn-primary' : '' ?>" onclick="seleccionar('<?= $id_hash ?>','malo',this)">Malo</button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <div class="mb-4">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($recibo['observaciones'] ?? '') ?></textarea>
    </div>

    <div class="text-center my-4">
      <h5>Condición Total Estimada</h5>
      <h1 id="total_avance" style="color: yellow; font-size: 3rem;">0%</h1>
    </div>

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
