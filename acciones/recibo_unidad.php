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

// Lista completa de secciones y componentes con nombres exactos
$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECANICO" => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
  "SISTEMA HIDRAULICO" => ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"],
  "SISTEMA ELECTRICO Y ELECTRONICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)"],
  "ESTETICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
  "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
];

$recibo = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria")->fetch_assoc();

function crearBotones($nombre, $valor_existente) {
  $opciones = ['bueno', 'regular', 'malo'];
  $html = "<div class='d-flex gap-1'>";
  foreach ($opciones as $opcion) {
    $active = $valor_existente == $opcion ? 'btn-primary' : 'btn-outline-primary';
    $html .= "<button type='button' class='btn $active btn-sm' onclick=\"seleccionar('$nombre','$opcion', this)\">" . ucfirst($opcion) . "</button>";
  }
  $html .= "<input type='hidden' name='componentes[$nombre]' id='comp_$nombre' value='$valor_existente'>";
  $html .= "</div>";
  return $html;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recibo de Unidad</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #1a1a2e; color: white; padding: 20px; }
    .seccion { background-color: #16213e; padding: 15px; margin-bottom: 20px; border-radius: 10px; }
    label { font-weight: bold; }
    .btn-sm { font-size: 0.8rem; }
    .btn-primary, .btn-outline-primary { border-radius: 20px; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="text-center mb-4">Recibo de Unidad</h3>
  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>">
    <?php foreach ($secciones as $titulo => $componentes): ?>
      <div class="seccion">
        <h5><?= $titulo ?></h5>
        <div class="row">
          <?php foreach ($componentes as $nombre): ?>
            <div class="col-md-6 mb-2">
              <label><?= $nombre ?></label>
              <?= crearBotones($nombre, $recibo[$nombre] ?? '') ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <div class="mb-3">
      <label>Observaciones:</label>
      <textarea name="observaciones" class="form-control" rows="3"><?= $recibo['observaciones'] ?? '' ?></textarea>
    </div>
    <div class="text-center">
      <button type="submit" class="btn btn-warning px-4">Guardar</button>
    </div>
  </form>
</div>

<script>
function seleccionar(nombre, valor, boton) {
  document.getElementById('comp_' + nombre).value = valor;
  let botones = boton.parentNode.querySelectorAll('button');
  botones.forEach(btn => btn.classList.remove('btn-primary'));
  botones.forEach(btn => btn.classList.add('btn-outline-primary'));
  boton.classList.add('btn-primary');
  boton.classList.remove('btn-outline-primary');
}
</script>
</body>
</html>
