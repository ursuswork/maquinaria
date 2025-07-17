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
$resultado = $conn-&gt;query($sql);
?&gt;
<!DOCTYPE html>

<html lang="es">
<head>
<meta charset="utf-8"/>
<title>Inventario de Maquinaria</title>
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<style>
    .etiqueta-nueva { background-color: #007bff; color: white; padding: 2px 8px; border-radius: 5px; font-size: 12px; }
    .etiqueta-usada { background-color: #ffc107; color: black; padding: 2px 8px; border-radius: 5px; font-size: 12px; }
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
      font-size: 1.8rem;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .btn-primary { background-color: #0074D9; border: none; }
    .btn-warning { background-color: #FFDC00; color: black; border: none; }
    .btn-success { background-color: #2ECC40; border: none; }
    .btn-outline-secondary { border-color: white; color: white; }
    .nav-tabs .nav-link.active { background-color: #FFDC00; color: black; }
    .nav-tabs .nav-link { color: #0074D9; }
  </style>
</head>
<body class="bg-light d-flex justify-content-center align-items-start pt-4"><div class="text-white text-center w-100 mb-4" style="font-size: 2rem;">Inventario de Maquinaria</div>
<div class="card shadow p-4 w-100" style="max-width: 1000px; margin: auto; background-color: white;">
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
<div class="w-100 d-flex justify-content-between align-items-center flex-wrap">
<a class="btn btn-success me-2 mb-2" href="agregar_maquinaria.php">➕ Agregar Maquinaria</a>
<a class="btn btn-warning me-2 mb-2" href="exportar_excel.php">📥 Exportar a Excel</a>
<a class="btn btn-outline-dark mb-2" href="logout.php">🔒 Cerrar Sesión</a>
</div>
</div>
<form class="mb-4 d-flex flex-column flex-sm-row" method="GET">
<input class="form-control me-sm-2 mb-2 mb-sm-0" name="busqueda" placeholder="Buscar maquinaria..." type="text" value="&lt;?= htmlspecialchars($busqueda) ?&gt;"/>
<button class="btn btn-outline-primary">Buscar</button>
</form>
<ul class="nav nav-tabs mb-3" id="tabMaquinaria" role="tablist">
<li class="nav-item" role="presentation">
<button class="nav-link active" data-bs-target="#nueva" data-bs-toggle="tab" id="nueva-tab" type="button">Nueva</button>
</li>
<li class="nav-item" role="presentation">
<button class="nav-link" data-bs-target="#usada" data-bs-toggle="tab" id="usada-tab" type="button">Usada</button>
</li>
</ul>
<div class="tab-content" id="tabContent">
<div class="tab-pane fade show active" id="nueva" role="tabpanel">
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
<?php
if ($resultado) {
  $resultado->data_seek(0);
  while ($row = $resultado-&gt;fetch_assoc()) {
    if ($row['tipo_maquinaria'] === 'nueva') {
?&gt;
          <div class="col">
<div class="card h-100 shadow-sm">
<img alt="Imagen" class="card-img-top" src="imagenes/&lt;?= $row['imagen'] ?: 'no-imagen.png' ?&gt;"/>
<div class="card-body">
<h5 class="card-title"><?= $row['nombre'] ?> <span class="etiqueta-nueva">Nueva</span></h5>
<p class="card-text mb-1"><strong>Modelo:</strong> <?= $row['modelo'] ?></p>
<p class="card-text mb-1"><strong>Ubicación:</strong> <?= $row['ubicacion'] ?></p>
<div class="progress barra-condicion mb-2">
<div class="progress-bar bg-success" role="progressbar" style="width: &lt;?= $row['condicion_estimada'] ?&gt;%;">
<?= $row['condicion_estimada'] ?>%
                  </div>
</div>
<div class="d-flex justify-content-between">
<a class="btn btn-sm btn-primary" href="editar_maquinaria.php?id=&lt;?= $row['id'] ?&gt;">Editar</a>
<a class="btn btn-sm btn-danger" href="eliminar_maquinaria.php?id=&lt;?= $row['id'] ?&gt;">Eliminar</a>
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
$resultado->data_seek(0);
while ($row = $resultado-&gt;fetch_assoc()) {
  if ($row['tipo_maquinaria'] === 'usada') {
?&gt;
          <div class="col">
<div class="card h-100 shadow-sm">
<img alt="Imagen" class="card-img-top" src="imagenes/&lt;?= $row['imagen'] ?: 'no-imagen.png' ?&gt;"/>
<div class="card-body">
<h5 class="card-title"><?= $row['nombre'] ?> <span class="etiqueta-usada">Usada</span></h5>
<p class="card-text mb-1"><strong>Modelo:</strong> <?= $row['modelo'] ?></p>
<p class="card-text mb-1"><strong>Ubicación:</strong> <?= $row['ubicacion'] ?></p>
<div class="progress barra-condicion mb-2">
<div class="progress-bar bg-warning" role="progressbar" style="width: &lt;?= $row['condicion_estimada'] ?&gt;%;">
<?= $row['condicion_estimada'] ?>%
                  </div>
</div>
<div class="d-flex justify-content-between">
<a class="btn btn-sm btn-primary" href="editar_maquinaria.php?id=&lt;?= $row['id'] ?&gt;">Editar</a>
<a class="btn btn-sm btn-danger" href="eliminar_maquinaria.php?id=&lt;?= $row['id'] ?&gt;">Eliminar</a>
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
