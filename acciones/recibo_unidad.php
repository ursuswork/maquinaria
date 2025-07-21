<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Aquí puedes procesar el guardado si lo deseas
  header("Location: ../inventario.php");
  exit;
}

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) {
  die("❌ ID de maquinaria inválido.");
}
$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) {
  die("❌ Maquinaria no encontrada.");
}

$recibo_existente = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1")->fetch_assoc();

function botonOpciones($nombre, $valor_existente) {
  return "
    <div class='mb-2'>
      <label class='form-label fw-bold text-warning'>" . htmlspecialchars($nombre) . "</label><br>
      <div class='btn-group' role='group'>
        <input type='radio' class='btn-check' name='componentes[{$nombre}]' id='{$nombre}_bueno' value='bueno'" . ($valor_existente == 'bueno' ? ' checked' : '') . ">
        <label class='btn btn-warning btn-md px-4 py-2' for='{$nombre}_bueno'>Bueno</label>

        <input type='radio' class='btn-check' name='componentes[{$nombre}]' id='{$nombre}_regular' value='regular'" . ($valor_existente == 'regular' ? ' checked' : '') . ">
        <label class='btn btn-warning btn-md px-4 py-2' for='{$nombre}_regular'>Regular</label>

        <input type='radio' class='btn-check' name='componentes[{$nombre}]' id='{$nombre}_malo' value='malo'" . ($valor_existente == 'malo' ? ' checked' : '') . ">
        <label class='btn btn-warning btn-md px-4 py-2' for='{$nombre}_malo'>Malo</label>
      </div>
    </div>
  ";
}

$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECÁNICO" => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
  "SISTEMA HIDRÁULICO" => ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"],
  "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)"],
  "ESTÉTICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
  "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recibo de Unidad</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #001f3f;
      color: #ffffff;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      background-color: #002b5c;
      padding: 2rem;
      border-radius: 1rem;
      max-width: 1200px;
      margin: auto;
      box-shadow: 0 0 20px #000;
    }
    h3, h4, h5 {
      color: #ffc107;
      border-bottom: 2px solid #ffc107;
      padding-bottom: .5rem;
    }
    .form-label {
      color: #ffc107;
      font-weight: bold;
    }
    .form-control, .form-select {
      background-color: #003366;
      color: #ffffff;
      border: 1px solid #0059b3;
      margin-bottom: 1rem;
    }
    .btn-primary { background-color: #0056b3; border: none; font-weight: bold; }
    .btn-warning { background-color: #ffc107; border: none; font-weight: bold; color: #000; }
    .progress-bar { background-color: #ffc107 !important; color: #000; font-weight: bold; }
  </style>
</head>
<body>
  <div class="container py-4">
    <h3 class="text-center">Recibo de Unidad</h3>
    <?php if (isset($recibo_existente['condicion_estimada'])): ?>
      <div class="my-3 text-center">
        <label class="form-label fw-bold">Condición Estimada</label>
        <div class="progress" style="height: 30px;">
          <div class="progress-bar" role="progressbar" style="width: <?=$recibo_existente['condicion_estimada']?>%;" aria-valuenow="<?=$recibo_existente['condicion_estimada']?>" aria-valuemin="0" aria-valuemax="100">
            <?=$recibo_existente['condicion_estimada']?>%
          </div>
        </div>
      </div>
    <?php endif; ?>
    <form method="POST">
      <div class="row mb-3">
        <div class="col-md-4">
          <label class="form-label">Equipo</label>
          <input type="text" class="form-control" value="<?=htmlspecialchars($maquinaria['nombre'])?>" readonly>
        </div>
        <div class="col-md-4">
          <label class="form-label">Marca</label>
          <input type="text" class="form-control" value="<?=htmlspecialchars($maquinaria['marca'])?>" readonly>
        </div>
        <div class="col-md-4">
          <label class="form-label">Modelo</label>
          <input type="text" class="form-control" value="<?=htmlspecialchars($maquinaria['modelo'])?>" readonly>
        </div>
      </div>
      <?php foreach ($secciones as $titulo => $componentes): ?>
        <hr>
        <h5><?=htmlspecialchars($titulo)?></h5>
        <div class="row">
        <?php foreach ($componentes as $comp): ?>
          <div class="col-md-6">
            <?=botonOpciones($comp, $recibo_existente[$comp] ?? '')?>
          </div>
        <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
      <div class="mt-4">
        <label class="form-label">Observaciones</label>
        <textarea name="observaciones" class="form-control" rows="3"><?=htmlspecialchars($recibo_existente['observaciones'] ?? '')?></textarea>
      </div>
      <div class="text-center mt-4">
        <button type="submit" class="btn btn-warning px-5 py-2">Guardar</button>
      </div>
    </form>
  </div>
</body>
</html>
