<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$filtro_tipo = $_GET['filtro_tipo'] ?? '';
$filtro_subtipo = $_GET['filtro_subtipo'] ?? '';

$sql = "SELECT * FROM maquinaria WHERE 1";
if (!empty($busqueda)) {
  $sql .= " AND (nombre LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%' OR numero_serie LIKE '%$busqueda%')";
}
if (!empty($filtro_tipo)) {
  $sql .= " AND tipo_maquinaria = '$filtro_tipo'";
}
if (!empty($filtro_subtipo)) {
  $sql .= " AND subtipo_maquinaria = '$filtro_subtipo'";
}
$sql .= " ORDER BY tipo_maquinaria ASC, nombre ASC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario de Maquinaria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f5f5;
    }
    .tarjeta {
      border-radius: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      transition: 0.3s;
    }
    .tarjeta:hover {
      transform: scale(1.02);
    }
    .etiqueta-nueva {
      background-color: #2525ddff;
      color: white;
      padding: 2px 8px;
      border-radius: 5px;
      font-size: 0.8em;
    }
    .barra-condicion {
      height: 20px;
    }
    .btn-recibo {
      font-size: 0.8em;
    }
    .subtipo-selector {
      margin-top: -10px;
    }
  </style>
</head>
<body>
  <div class="container py-4">
    <h2 class="mb-4 text-center text-primary">Inventario de Maquinaria</h2>

    <form method="GET" class="row mb-4 g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Buscar</label>
        <input type="text" name="busqueda" class="form-control" value="<?= htmlspecialchars($busqueda) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tipo</label>
        <select name="filtro_tipo" class="form-select" onchange="this.form.submit()">
          <option value="">Todos</option>
          <option value="nueva" <?= $filtro_tipo == 'nueva' ? 'selected' : '' ?>>Nueva</option>
          <option value="usada" <?= $filtro_tipo == 'usada' ? 'selected' : '' ?>>Usada</option>
        </select>
      </div>
      <?php if ($filtro_tipo == 'nueva'): ?>
        <div class="col-md-3 subtipo-selector">
          <label class="form-label">Subtipo</label>
          <select name="filtro_subtipo" class="form-select" onchange="this.form.submit()">
            <option value="">Todos</option>
            <?php
              $subtipos = ["Petrolizadora", "Esparcidor de sello", "Tanque de almacén", "Bachadora", "Planta de mezcla en frío"];
              foreach ($subtipos as $s) {
                echo "<option value='$s' " . ($filtro_subtipo == $s ? 'selected' : '') . ">$s</option>";
              }
            ?>
          </select>
        </div>
      <?php endif; ?>
      <div class="col-md-2 d-grid">
        <button type="submit" class="btn btn-primary">Buscar</button>
      </div>
    </form>

    <div class="row">
      <?php while ($row = $resultado->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
          <div class="card tarjeta p-3">
            <?php if (!empty($row['imagen'])): ?>
              <img src="imagenes/<?= $row['imagen'] ?>" class="card-img-top" style="max-height: 180px; object-fit: cover;">
            <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['nombre']) ?></h5>
              <p class="card-text mb-1"><strong>Tipo:</strong> <?= htmlspecialchars($row['tipo_maquinaria']) ?>
                <?php if ($row['tipo_maquinaria'] == 'nueva'): ?>
                  <span class="etiqueta-nueva"><?= htmlspecialchars($row['subtipo_maquinaria']) ?></span>
                <?php endif; ?>
              </p>
              <p class="card-text mb-1"><strong>Modelo:</strong> <?= htmlspecialchars($row['modelo']) ?></p>
              <p class="card-text mb-2"><strong>Ubicación:</strong> <?= htmlspecialchars($row['ubicacion']) ?></p>
              <?php if (!is_null($row['condicion_estimada'])): ?>
                <label class="form-label small mb-0">Condición Estimada</label>
                <div class="progress barra-condicion">
                  <div class="progress-bar <?= $row['condicion_estimada'] >= 85 ? 'bg-success' : ($row['condicion_estimada'] >= 60 ? 'bg-warning' : 'bg-danger') ?>"
                    role="progressbar" style="width: <?= $row['condicion_estimada'] ?>%">
                    <?= $row['condicion_estimada'] ?>%
                  </div>
                </div>
              <?php endif; ?>

              <div class="d-flex justify-content-between mt-3">
                <a href="editar_maquinaria.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">✏️</a>
                <a href="eliminar_maquinaria.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta maquinaria?')">🗑️</a>
                <?php if ($row['tipo_maquinaria'] == 'usada'): ?>
                  <a href="acciones/recibo_unidad.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success btn-recibo">📋 Recibo</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <a href="agregar_maquinaria.php" class="btn btn-success rounded-circle position-fixed bottom-0 end-0 m-4" title="Agregar Maquinaria" style="width:60px;height:60px;font-size:24px;">＋</a>
  </div>
</body>
</html>
