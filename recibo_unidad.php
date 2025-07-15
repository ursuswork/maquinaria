<?php include 'conexion.php'; ?>
<?php $id = $_GET['id'] ?? null; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recibo de Unidad</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f5f7fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .section-title {
      background: #e9ecef;
      padding: .5rem 1rem;
      font-weight: bold;
      border-radius: .5rem .5rem 0 0;
    }
    .radio-group label {
      margin-right: 15px;
    }
    .progress {
      height: 1rem;
    }
    .guardar-btn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 999;
    }
  </style>
</head>
<body>
<div class="container py-4">
  <form action="acciones/guardar_recibo.php?id=<?= $id ?>" method="POST">
    <div class="card p-4 mb-4">
      <h3 class="mb-3">📋 Recibo de Unidad</h3>

      <!-- Datos generales -->
      <div class="row mb-3">
        <div class="col-md-4">
          <label class="form-label">Equipo:</label>
          <input type="text" class="form-control" name="equipo">
        </div>
        <div class="col-md-4">
          <label class="form-label">Marca:</label>
          <input type="text" class="form-control" name="marca">
        </div>
        <div class="col-md-4">
          <label class="form-label">Modelo:</label>
          <input type="text" class="form-control" name="modelo">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Empresa Origen:</label>
          <input type="text" class="form-control" name="empresa_origen">
        </div>
        <div class="col-md-6">
          <label class="form-label">Empresa Destino:</label>
          <input type="text" class="form-control" name="empresa_destino">
        </div>
      </div>
    </div>

    <!-- Secciones con componentes -->
    <?php
    $secciones = [
      'MOTOR' => ['Cilindros','Pistones','Anillos','Inyectores','Árbol de levas','Levas','Turbocargador','Block','Cabeza','Bujías','Chicotes','Batería','Fan Clutch','Banda','Bandas de tiempo','Poleas','Radiador','Reservorio','Mangueras','Ventilador','Motor de arranque','Alternador','Módulo ECM','Sensor de oxígeno','Sensor MAP','Sensor de cigüeñal','Sensor de árbol de levas','Sensor de temperatura','Sensor MAF','Sensor TPS'],
      'SISTEMA MECÁNICO' => ['Transmisión','Diferenciales','Cardán'],
      'SISTEMA HIDRÁULICO' => ['Bombas','Válvulas','Mangueras hidráulicas','Acumuladores','Filtros hidráulicos'],
      'SISTEMA ELÉCTRICO Y ELECTRÓNICO' => ['Luces','Fusibles','Relés','Cables','Interruptores','Módulos electrónicos','Tablero','Sensor de presión de aceite'],
      'ESTÉTICO' => ['Carrocería','Pintura','Cabina','Cristales','Loderas','Calcomanías'],
      'CONSUMIBLES' => ['Aceite','Filtros','Refrigerante','Combustible','Líquido hidráulico']
    ];

    foreach ($secciones as $titulo => $componentes): ?>
      <div class="card p-3 mb-4">
        <div class="section-title"><?= $titulo ?></div>
        <div class="row pt-3">
          <?php foreach ($componentes as $nombre): ?>
            <div class="col-md-4 mb-3">
              <label><?= $nombre ?></label><br>
              <div class="radio-group">
                <label><input type="radio" name="<?= strtolower(str_replace(' ', '_', $nombre)) ?>" value="bueno"> Bueno</label>
                <label><input type="radio" name="<?= strtolower(str_replace(' ', '_', $nombre)) ?>" value="regular"> Regular</label>
                <label><input type="radio" name="<?= strtolower(str_replace(' ', '_', $nombre)) ?>" value="malo"> Malo</label>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="card p-3 mb-4">
      <div class="section-title">Condición estimada</div>
      <div class="progress my-2">
        <div class="progress-bar bg-success" role="progressbar" style="width: 87%;">87%</div>
      </div>
    </div>

    <button type="submit" class="btn btn-primary guardar-btn">💾 Guardar</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>