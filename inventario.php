<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$sql = "SELECT * FROM maquinaria";
if (!empty($busqueda)) {
  $sql .= " WHERE nombre LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%' OR numero_serie LIKE '%$busqueda%'";
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
    .etiqueta-nueva { background-color: #0056b3; color: white; padding: 2px 8px; border-radius: 5px; font-size: 12px; }
    .etiqueta-usada { background-color: #ffc107; color: white; padding: 2px 8px; border-radius: 5px; font-size: 12px; }
    .card-img-top { height: 150px; object-fit: cover; }
    .barra-condicion { height: 10px; border-radius: 5px; }
    body { background-color: #001f3f; color: white; }
    .card { background-color: white; border-radius: 10px; }
    .titulo-app {
      background-color: #001f3f;
      color: white;
      padding: 15px;
      border-radius: 8px;
      text-align: center;
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .btn-primary { background-color: #0056b3; border: none; }
    .btn-warning { background-color: #0056b3; color: black; border: none; }
    .btn-success { background-color: #14ab14ff; border: none; }
    .btn-outline-secondary { border-color: white; color: white; }
    .nav-tabs .nav-link.active { background-color: #0056b3; color: black; }
    .nav-tabs .nav-link { color: #0056b3; }
  
    .bg-botella { background-color: #0bac0bff !important; color: white; }
    .btn-success { background-color: #0bac0bff; border: none; }
    .btn-success:hover { background-color: #0bac0bff; }
    .etiqueta-nueva { background-color: #0056b3; }
    .etiqueta-usada { background-color: #0bac0bff; }
</style>

</head>
<body class="pt-4">
<div class="titulo-app">Inventario de Maquinaria</div>

<div class="card shadow p-4 w-100" style="max-width: 1000px; margin: auto;">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <div class="w-100 d-flex justify-content-between align-items-center flex-wrap">
      <a href="agregar_maquinaria.php" class="btn btn-success me-2 mb-2">➕ Agregar Maquinaria</a>
      <a href="exportar_excel.php" class="btn btn-warning me-2 mb-2">📥 Exportar a Excel</a>
      <a href="logout.php" class="btn btn-outline-dark mb-2">🔒 Cerrar Sesión</a>
    </div>
  </div>

  <form method="GET" class="mb-4 d-flex flex-column flex-sm-row">
    <input type="text" name="busqueda" class="form-control me-sm-2 mb-2 mb-sm-0" placeholder="Buscar maquinaria..." value="<?= htmlspecialchars($busqueda) ?>">
    <button class="btn btn-outline-primary">Buscar</button>
  </form>

  <ul class="nav nav-tabs mb-3" id="tabMaquinaria" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="nueva-tab" data-bs-toggle="tab" data-bs-target="#nueva" type="button">Nueva</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="usada-tab" data-bs-toggle="tab" data-bs-target="#usada" type="button">Usada</button>
    </li>
  </ul>

  <div class="tab-content" id="tabContent">
    <div class="tab-pane fade show active" id="nueva" role="tabpanel">
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
<?php
if ($resultado) {
  $resultado->data_seek(0);
  while ($row = $resultado->fetch_assoc()) {
    if ($row['tipo_maquinaria'] === 'nueva') {
?>
        <div class="col">
          <div class="card h-100 shadow-sm">
            <img src="imagenes/<?= $row['imagen'] ?: 'no-imagen.png' ?>" class="card-img-top" alt="Imagen">
            <div class="card-body">
              <h5 class="card-title"><?= $row['nombre'] ?> <span class="etiqueta-nueva">Nueva</span></h5>
              <p class="card-text mb-1"><strong>Modelo:</strong> <?= $row['modelo'] ?></p>
              <p class="card-text mb-1"><strong>Ubicación:</strong> <?= $row['ubicacion'] ?></p>
              <div class="progress barra-condicion mb-2">
                <div class="progress-bar bg-botella" role="progressbar" style="width: <?= $row['condicion_estimada'] ?>%;">
                  <?= $row['condicion_estimada'] ?>%
                </div>
              </div>
              <div class="d-flex justify-content-between">
                <a href="editar_maquinaria.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                <a href="eliminar_maquinaria.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger">Eliminar</a>
              </div>
            </div>
          </div>
        </div>
<?php }}} ?>
      </div>
    </div>

    <div class="tab-pane fade" id="usada" role="tabpanel">
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
<?php
$resultado_usada = $conn->query($sql);
while ($row = $resultado_usada->fetch_assoc()) {
  if ($row['tipo_maquinaria'] === 'usada') {
?>
        <div class="col">
          <div class="card h-100 shadow-sm">
            <img src="imagenes/<?= $row['imagen'] ?: 'no-imagen.png' ?>" class="card-img-top" alt="Imagen">
            <div class="card-body">
              <h5 class="card-title"><?= $row['nombre'] ?> <span class="etiqueta-usada">Usada</span></h5>
              <p class="card-text mb-1"><strong>Modelo:</strong> <?= $row['modelo'] ?></p>
              <p class="card-text mb-1"><strong>Ubicación:</strong> <?= $row['ubicacion'] ?></p>
              <div class="progress barra-condicion mb-2">
                <div class="progress-bar bg-botella" role="progressbar" style="width: <?= $row['condicion_estimada'] ?>%;">
                  <?= $row['condicion_estimada'] ?>%
                </div>
              </div>
<?php
    $id_maquinaria = $row['id'];
    $recibo_guardado = false;
    $verifica = $conn->query("SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1");
    if ($verifica && $verifica->num_rows > 0) {
      $recibo_guardado = true;
    }
?>
              <div class="d-flex flex-column gap-2">
                <div class="d-flex justify-content-between">
                  <a href="editar_maquinaria.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                  <a href="eliminar_maquinaria.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger">Eliminar</a>
                </div>
                <div>
                  <?php if ($recibo_guardado): ?>
                    <a href="acciones/recibo_unidad.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success w-100">📝 Recibo</a>
                  <?php else: ?>
                    <a href="acciones/recibo_unidad.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning w-100">📝 Recibo</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
<?php }} ?>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>