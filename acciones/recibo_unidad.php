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

function componenteBotones($nombre) {
  return "
    <div class='col-md-4 mb-2'>
      <label class='form-label fw-bold d-block'>\$nombre</label>
      <div class='btn-group w-100' role='group'>
        <input type='radio' class='btn-check' name='componentes[\$nombre]' id='\$nombre-bueno' value='bueno' required>
        <label class='btn btn-outline-success' for='\$nombre-bueno'>Bueno</label>

        <input type='radio' class='btn-check' name='componentes[\$nombre]' id='\$nombre-regular' value='regular'>
        <label class='btn btn-outline-warning' for='\$nombre-regular'>Regular</label>

        <input type='radio' class='btn-check' name='componentes[\$nombre]' id='\$nombre-malo' value='malo'>
        <label class='btn btn-outline-danger' for='\$nombre-malo'>Malo</label>
      </div>
    </div>";
}

$secciones = [
  'MOTOR' => [...],  // Truncado aquí por simplicidad
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
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; color: black !important; }
    }
    body {
      background-color: #001f3f;
      color: white;
      padding: 20px;
    }
    .contenedor {
      background-color: white;
      color: black;
      padding: 30px;
      border-radius: 15px;
      max-width: 1000px;
      margin: auto;
    }
    h4 {
      background-color: #007bff;
      color: white;
      padding: 10px;
      border-radius: 8px;
    }
  </style>
</head>
<body>

<div class="contenedor">
  <h4 class="text-center mb-4">Recibo de Unidad</h4>
  <form action="guardar_recibo.php" method="POST">
    <input type="hidden" name="id_maquinaria" value="<?= $maquinaria['id'] ?>">

    <div class="mb-3 row">
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
      <strong>Equipo:</strong> <?= htmlspecialchars($maquinaria['nombre']) ?> &nbsp;&nbsp;
      <strong>Modelo:</strong> <?= htmlspecialchars($maquinaria['modelo']) ?> &nbsp;&nbsp;
      <strong>Ubicación:</strong> <?= htmlspecialchars($maquinaria['ubicacion']) ?>
    </div>

    <?php foreach ($secciones as $titulo => $componentes): ?>
      <h5 class="mt-4"><?= $titulo ?></h5>
      <div class="row">
        <?php foreach ($componentes as $componente): ?>
          <?= componenteBotones($componente) ?>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <div class="mb-3">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"></textarea>
    </div>

    <div class="d-flex justify-content-between no-print">
      <button type="submit" class="btn btn-primary">💾 Guardar Recibo</button>
      <button type="button" onclick="window.print()" class="btn btn-secondary">🖨️ Imprimir</button>
    </div>
  </form>
</div>

</body>
</html>
