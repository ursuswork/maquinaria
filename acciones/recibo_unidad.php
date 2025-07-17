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
  $opciones = ['bueno', 'regular', 'malo'];
  $html = "<div class='col-md-6 mb-3'><label class='form-label fw-bold'>" . htmlspecialchars($nombre) . "</label><div class='btn-group w-100' role='group' aria-label='Estado'>";
  foreach ($opciones as $opcion) {
    $id = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $nombre)) . "_" . $opcion;
    $html .= "<input type='radio' class='btn-check' name='componentes[$nombre]' id='$id' value='$opcion' required>"
          . "<label class='btn btn-outline-secondary' for='$id'>" . ucfirst($opcion) . "</label>";
  }
  $html .= "</div></div>";
  return $html;
}

$secciones = [
  'MOTOR' => ["Cilindros", "Pistones", "Anillos", "Inyectores", "Block", "Cabeza", "Varillas", "Resortes", "Punterías", "Cigüeñal", "Árbol de levas", "Retenes", "Ligas", "Sensores", "Poleas", "Concha", "Cremallera", "Clutch", "Coples", "Bomba de inyección", "Juntas", "Marcha", "Tubería", "Alternador", "Filtros", "Bases", "Soportes", "Turbo", "Escape", "Chicotes"],
  'SISTEMA MECÁNICO' => ["Transmisión", "Diferenciales", "Cardán"],
  'SISTEMA HIDRÁULICO' => ["Banco de válvulas", "Bombas de tránsito", "Bombas de precarga", "Bombas de accesorios", "Coples", "Clutch hidráulico", "Gatos de levante", "Gatos de dirección", "Gatos de accesorios", "Mangueras", "Motores hidráulicos", "Orbitrol", "Torques HUV (Satélites)", "Válvulas de retención", "Reductores"],
  'SISTEMA ELÉCTRICO Y ELECTRÓNICO' => ["Alarmas", "Arneses", "Bobinas", "Botones", "Cables", "Cables de sensores", "Conectores", "Electro válvulas", "Fusibles", "Porta fusibles", "Indicadores", "Presión/Agua/Temperatura/Voltímetro", "Luces", "Módulos", "Torreta", "Relevadores", "Switch (llave)", "Sensores"],
  'ESTÉTICO' => ["Pintura", "Calcomanías", "Asiento", "Tapicería", "Tolvas", "Cristales", "Accesorios", "Sistema de riego"],
  'CONSUMIBLES' => ["Puntas", "Porta puntas", "Garras", "Cuchillas", "Cepillos", "Separadores", "Llantas", "Rines", "Bandas / Orugas"]
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

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label fw-bold">Empresa Origen</label>
        <input type="text" name="empresa_origen" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-bold">Empresa Destino</label>
        <input type="text" name="empresa_destino" class="form-control" required>
      </div>
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
