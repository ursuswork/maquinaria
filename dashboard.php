<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';
$nuevas = $conn->query("SELECT * FROM maquinaria WHERE tipo='nueva'");
$usadas = $conn->query("SELECT * FROM maquinaria WHERE tipo='usada'");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario de Maquinaria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilos.css">
</head>
<body class="bg-light">
  <div class="container mt-4">
    <h2>Inventario de Maquinaria</h2>
    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nuevas">Nuevas</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#usadas">Usadas</button>
      </li>
    </ul>
    <div class="tab-content mt-3">
      <div class="tab-pane fade show active" id="nuevas">
        <div class="row">
          <?php while($row = $nuevas->fetch_assoc()): ?>
          <div class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card h-100">
              <img src="imagenes/<?= $row['imagen'] ?>" class="card-img-top">
              <div class="card-body p-2">
                <h6><?= $row['nombre'] ?></h6>
                <small><?= $row['modelo'] ?></small>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
      <div class="tab-pane fade" id="usadas">
        <div class="row">
          <?php while($row = $usadas->fetch_assoc()): ?>
          <div class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card h-100">
              <img src="imagenes/<?= $row['imagen'] ?>" class="card-img-top">
              <div class="card-body p-2">
                <h6><?= $row['nombre'] ?></h6>
                <small><?= $row['modelo'] ?></small>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
    <a href="logout.php" class="btn btn-outline-danger w-100 mt-4">Cerrar sesión</a>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>