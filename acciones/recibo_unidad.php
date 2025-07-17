<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
  die("❌ ID inválido.");
}
$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id")->fetch_assoc();
if (!$maquinaria) {
  die("❌ Maquinaria no encontrada.");
}

function botonOpcion($nombre, $componente) {
  $id_html = strtolower(str_replace(' ', '_', $componente));
  return "<div class='col-md-4 mb-3'>
    <label class='form-label fw-bold'>$componente</label><br>
    <div class='btn-group' role='group'>
      <input type='radio' class='btn-check' name='componentes[$componente]' id='{$id_html}_bueno' value='bueno' required>
      <label class='btn btn-outline-success' for='{$id_html}_bueno'>Bueno</label>

      <input type='radio' class='btn-check' name='componentes[$componente]' id='{$id_html}_regular' value='regular'>
      <label class='btn btn-outline-warning' for='{$id_html}_regular'>Regular</label>

      <input type='radio' class='btn-check' name='componentes[$componente]' id='{$id_html}_malo' value='malo'>
      <label class='btn btn-outline-danger' for='{$id_html}_malo'>Malo</label>
    </div>
  </div>";
}

$secciones = [
  'MOTOR' => [...],
  'SISTEMA MECÁNICO' => [...],
  'SISTEMA HIDRÁULICO' => [...],
  'SISTEMA ELÉCTRICO Y ELECTRÓNICO' => [...],
  'ESTÉTICO' => [...],
  'CONSUMIBLES' => [...]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recibo de Unidad</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #001f3f; color: white; }
    .formulario { background: white; color: black; padding: 30px; border-radius: 15px; max-width: 1000px; margin: auto; }
    .seccion { margin-top: 30px; }
    @media print {
      body * { visibility: hidden; }
      .formulario, .formulario * { visibility: visible; }
      .formulario { position: absolute; top: 0; left: 0; width: 100%; }
      .btn { display: none; }
    }
  </style>
</head>
<body>
<div class="formulario">
  <h3 class="text-center mb-4">Recibo de Unidad</h3>
  <form method="POST" action="guardar_recibo.php">
    <input type="hidden" name="id_maquinaria" value="<?= $maquinaria['id'] ?>">

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Empresa Origen</label>
        <input type="text" name="empresa_origen" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Empresa Destino</label>
        <input type="text" name="empresa_destino" class="form-control" required>
      </div>
    </div>

    <div class="mb-3">
      <strong>Equipo:</strong> <?= $maquinaria['nombre'] ?> | <strong>Modelo:</strong> <?= $maquinaria['modelo'] ?> | <strong>Ubicación:</strong> <?= $maquinaria['ubicacion'] ?>
    </div>

    <?php foreach ($secciones as $titulo => $componentes): ?>
      <div class="seccion">
        <h5 class="bg-primary text-white p-2"><?= $titulo ?></h5>
        <div class="row">
          <?php foreach ($componentes as $c): echo botonOpcion($titulo, $c); endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="mb-3">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"></textarea>
    </div>

    <div class="d-flex justify-content-between">
      <button type="submit" class="btn btn-success">Guardar</button>
      <button type="button" onclick="window.print()" class="btn btn-outline-primary">Imprimir</button>
    </div>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
