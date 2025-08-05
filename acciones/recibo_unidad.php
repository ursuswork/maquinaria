<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

$rol = $_SESSION['rol'] ?? '';
$usuario = $_SESSION['usuario'] ?? '';
$permitir_modificar = false;
$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) die("ID inválido");

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id_maquinaria")->fetch_assoc();
if (!$maquinaria) die("Maquinaria no encontrada");

$tipo_maquinaria = strtolower($maquinaria['tipo_maquinaria']);

if ($usuario === 'jabri') {
    $permitir_modificar = true;
} elseif ($rol === 'admin') {
    $permitir_modificar = true;
} elseif ($rol === 'usada' && ($tipo_maquinaria === 'usada' || $tipo_maquinaria === 'camion')) {
    $permitir_modificar = true;
} elseif ($rol === 'produccion' && ($tipo_maquinaria === 'nueva' || $tipo_maquinaria === 'camion')) {
    $permitir_modificar = true;
} elseif ($rol === 'camiones' && $tipo_maquinaria === 'camion') {
    $permitir_modificar = true;
} elseif ($rol === 'consulta') {
    $permitir_modificar = false;
} else {
    die("Acceso denegado para su usuario.");
}

$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES"],
  "SISTEMA MECANICO" => ["TRANSMISIÓN"],
  "SISTEMA HIDRAULICO" => ["BANCO DE VÁLVULAS"],
  "SISTEMA ELECTRICO Y ELECTRONICO" => ["ALARMAS"],
  "ESTETICO" => ["PINTURA"],
  "CONSUMIBLES" => ["PUNTAS"]
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
    @media print {
      button, .no-print {
        display: none !important;
      }
      .seleccion-valor {
        display: inline-block !important;
        font-weight: bold;
        color: #000 !important;
        background: #ffc107;
        padding: 4px 10px;
        border-radius: 6px;
        margin-top: 6px;
      }
    }
  </style>
</head>
<body>
  <form method="POST" action="guardar_recibo.php?id=<?= $id_maquinaria ?>">
  <?php foreach ($secciones as $seccion => $componentes): ?>
    <h3><?= $seccion ?></h3>
    <div class="row">
      <?php foreach ($componentes as $componente):
        $id_hash = md5($componente);
        $valor = $recibo[$componente] ?? '';
      ?>
        <div class="col-md-4 mb-3">
          <label><?= $componente ?></label>
          <input type="hidden" name="componentes[<?= $componente ?>]" id="comp_<?= $id_hash ?>" value="<?= $valor ?>">
          <div class="d-flex gap-2 align-items-center flex-wrap">
            <?php if ($permitir_modificar): ?>
              <button type="button" class="btn <?= $valor === 'bueno' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm w-100" onclick="seleccionar('<?= $id_hash ?>','bueno',this)">Bueno</button>
              <button type="button" class="btn <?= $valor === 'regular' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm w-100" onclick="seleccionar('<?= $id_hash ?>','regular',this)">Regular</button>
              <button type="button" class="btn <?= $valor === 'malo' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm w-100" onclick="seleccionar('<?= $id_hash ?>','malo',this)">Malo</button>
              <span class="seleccion-valor d-none" id="print_<?= $id_hash ?>"><?= ucfirst($valor ?: '-') ?></span>
            <?php else: ?>
              <span class="badge bg-secondary"><?= ucfirst($valor ?: '-') ?></span>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
  </form>
<script>
function seleccionar(id, valor, boton) {
  document.getElementById("comp_" + id).value = valor;
  let botones = boton.parentNode.querySelectorAll("button");
  botones.forEach(b => b.classList.replace('btn-primary','btn-outline-primary'));
  boton.classList.replace('btn-outline-primary','btn-primary');
  document.getElementById("print_" + id).textContent = valor.charAt(0).toUpperCase() + valor.slice(1);
}
</script>
</body>
</html>