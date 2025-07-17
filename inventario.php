
<?php
include 'conexion.php';
session_start();

if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}

$tipos_validos = ['nueva', 'usada'];
$filtro = isset($_GET['tipo']) && in_array($_GET['tipo'], $tipos_validos) ? $_GET['tipo'] : 'nueva';

$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$sql = "SELECT * FROM maquinaria WHERE tipo_maquinaria = ? AND (nombre LIKE ? OR modelo LIKE ? OR ubicacion LIKE ?) ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$like = "%$busqueda%";
$stmt->bind_param("ssss", $filtro, $like, $like, $like);
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario de Maquinaria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Inventario</span>
    <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
  </nav>
  <div class="container py-4">
    <form method="GET" class="mb-4 d-flex">
      <input type="hidden" name="tipo" value="<?= htmlspecialchars($filtro) ?>">
      <input type="text" name="busqueda" class="form-control me-2" placeholder="Buscar..." value="<?= htmlspecialchars($busqueda) ?>">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </form>
    <div class="row g-4">
      <?php while ($row = $resultado->fetch_assoc()): ?>
        <div class="col-md-4">
          <div class="card">
            <img src="imagenes/<?= $row['imagen'] ?>" class="card-img-top" alt="Imagen">
            <div class="card-body">
              <h5 class="card-title"><?= $row['nombre'] ?></h5>
              <p class="card-text"><?= $row['modelo'] ?> - <?= $row['ubicacion'] ?></p>
              <p class="text-warning">Condición: <?= $row['condicion_estimada'] ?>%</p>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</body>
</html>
