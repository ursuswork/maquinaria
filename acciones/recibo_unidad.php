
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
        <label class='btn btn-warning text-dark btn-md px-4 py-2' for='{$nombre}_bueno'>Bueno</label>

        <input type='radio' class='btn-check' name='componentes[{$nombre}]' id='{$nombre}_regular' value='regular' " . ($valor_existente == 'regular' ? 'checked' : '') . ">
        <label class='btn btn-warning text-dark btn-md px-4 py-2' for='{$nombre}_regular'>Regular</label>

        <input type='radio' class='btn-check' name='componentes[{$nombre}]' id='{$nombre}_malo' value='malo' " . ($valor_existente == 'malo' ? 'checked' : '') . ">
        <label class='btn btn-warning text-dark btn-md px-4 py-2' for='{$nombre}_malo'>Malo</label>
      </div>
    </div>
  ";
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
      color: #ffffff;
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
    .btn-success { background-color: #28a745; border: none; font-weight: bold; }
    .btn-warning { background-color: #ffc107; border: none; font-weight: bold; color: #000; }
    .progress-bar.bg-success,
    .progress-bar.bg-warning,
    .progress-bar.bg-danger {
      background-color: #ffc107 !important;
      color: #000;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container py-4 formulario-recibo">
    <h3 class="text-center text-warning mb-4">Recibo de Unidad</h3>
    <!-- CONTENIDO OMITIDO PARA BREVIDAD -->
  </div>
</body>
</html>
