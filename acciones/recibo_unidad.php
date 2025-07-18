<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

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
      <label class='form-label fw-bold'>" . htmlspecialchars($nombre) . "</label><br>
      <div class='btn-group' role='group'>
        <input type='radio' class='btn-check' name='componentes[{$nombre}]' id='{$nombre}_bueno' value='bueno' " . ($valor_existente == 'bueno' ? 'checked' : '') . ">
        <label class='btn btn-outline-success btn-sm' for='{$nombre}_bueno'>Bueno</label>

        <input type='radio' class='btn-check' name='componentes[{$nombre}]' id='{$nombre}_regular' value='regular' " . ($valor_existente == 'regular' ? 'checked' : '') . ">
        <label class='btn btn-outline-warning btn-sm' for='{$nombre}_regular'>Regular</label>

        <input type='radio' class='btn-check' name='componentes[{$nombre}]' id='{$nombre}_malo' value='malo' " . ($valor_existente == 'malo' ? 'checked' : '') . ">
        <label class='btn btn-outline-danger btn-sm' for='{$nombre}_malo'>Malo</label>
      </div>
    </div>
  ";
}

$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECÁNICO" => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
  "SISTEMA HIDRÁULICO" => ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "COPLES", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"],
  "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)", "SENSORES"],
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
    @media print {
      body * { visibility: hidden; }
      .formulario-recibo, .formulario-recibo * { visibility: visible; }
      .formulario-recibo { position: absolute; top: 0; left: 0; width: 100%; }
      .btn, .navbar, .no-imprimir { display: none !important; }
    }
  </style>
</head>
<body class="bg-light">
  <div class="container py-4 formulario-recibo">
    <h3 class="text-center text-primary mb-4">Recibo de Unidad</h3>
    <form method="POST" action="guardar_recibo.php">
      <input type="hidden" name="id_maquinaria" value="<?=$id_maquinaria?>">

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Empresa Origen</label>
          <input type="text" name="empresa_origen" class="form-control" value="<?=htmlspecialchars($recibo_existente['empresa_origen'] ?? '')?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Empresa Destino</label>
          <input type="text" name="empresa_destino" class="form-control" value="<?=htmlspecialchars($recibo_existente['empresa_destino'] ?? '')?>">
        </div>
      </div>

      <?php foreach ($secciones as $titulo => $componentes): ?>
        <hr>
        <h5 class="text-secondary"><?=htmlspecialchars($titulo)?></h5>
        <div class="row">
          <?php
          $unicos = array_unique($componentes);
          foreach ($unicos as $comp): ?>
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
        <button type="submit" class="btn btn-success">💾 Guardar</button>
        <button type="button" class="btn btn-primary" onclick="window.print()">🖨️ Imprimir Recibo</button>
      </div>
    </form>
  </div>
</body>
</html>
