<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../login.php");
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

function componenteSelect($nombre) {
  return "
    <div class='col-md-6 mb-2'>
      <label class='form-label fw-bold'>$nombre</label>
      <select name='componentes[$nombre]' class='form-select' required>
        <option value='bueno'>Bueno</option>
        <option value='regular'>Regular</option>
        <option value='malo'>Malo</option>
      </select>
    </div>";
}

$secciones = [
  'MOTOR' => ["Cilindros", "Pistones", "Anillos", "Inyectores", "Árbol de levas", "Balancines", "Bielas", "Block", "Culata", "Válvulas", "Turbo", "Múltiple de escape", "Radiador", "Termostato", "Bomba de agua", "Bomba de aceite", "Cárter", "Filtro de aceite", "Sensor de oxígeno", "Computadora", "Chicotes", "Arrancador", "Alternador", "Fajas", "Poleas", "Tapa de punterías", "Ventilador", "Soportes de motor", "Depósito de refrigerante", "Sensor de temperatura"],
  'SISTEMA MECÁNICO' => ["Transmisión", "Diferenciales", "Cardán"],
  'SISTEMA HIDRÁULICO' => ["Bombas hidráulicas", "Cilindros", "Válvulas", "Mangueras"],
  'SISTEMA ELÉCTRICO Y ELECTRÓNICO' => ["Luces", "Tablero", "Sensores", "Fusibles"],
  'ESTÉTICO' => ["Pintura", "Cabina", "Cristales", "Asientos"],
  'CONSUMIBLES' => ["Aceite motor", "Filtro de aire", "Filtro combustible", "Filtro hidráulico"]
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
    .btn-primary {
      background-color: #007bff;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

<div class="contenedor">
  <h4 class="text-center mb-4">Recibo de Unidad</h4>
  <form action="guardar_recibo.php" method="POST">
    <input type="hidden" name="id_maquinaria" value="<?= $maquinaria['id'] ?>">

    <div class="mb-3">
      <strong>Equipo:</strong> <?= htmlspecialchars($maquinaria['nombre']) ?> &nbsp;&nbsp;
      <strong>Modelo:</strong> <?= htmlspecialchars($maquinaria['modelo']) ?> &nbsp;&nbsp;
      <strong>Ubicación:</strong> <?= htmlspecialchars($maquinaria['ubicacion']) ?>
    </div>

    <?php foreach ($secciones as $titulo => $componentes): ?>
      <h5 class="mt-4"><?= $titulo ?></h5>
      <div class="row">
        <?php foreach ($componentes as $componente): ?>
          <?= componenteSelect($componente) ?>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <div class="mb-3">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"></textarea>
    </div>

    <div class="d-grid">
      <button type="submit" class="btn btn-primary">Guardar Recibo</button>
    </div>
  </form>
</div>

</body>
</html>
