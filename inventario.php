
<?php
include 'conexion.php';
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}

$filtro = isset($_GET['tipo']) ? $_GET['tipo'] : 'nueva';
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f4f6f9; }
    .navbar-custom { background-color: #001f3f; }
    .btn-agregar { background-color: #007bff; color: white; }
    .btn-agregar:hover { background-color: #0056b3; }
    .tarjeta-maquinaria {
      background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); height: 100%;
    }
    .img-maquinaria {
      height: 200px; width: 100%; object-fit: cover; border-radius: 12px;
    }
    .etiqueta-nueva { background-color: #007bff; color: white; padding: 2px 8px; border-radius: 6px; font-size: 12px; }
    .etiqueta-usada { background-color: #6c757d; color: white; padding: 2px 8px; border-radius: 6px; font-size: 12px; }
    .barra-condicion { height: 8px; background: #ffc107; border-radius: 4px; }
    .floating-export {
      position: fixed; bottom: 20px; right: 20px; z-index: 1000;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom px-3">
  <span class="navbar-brand mb-0 h1">Inventario de Maquinaria</span>
  <a href="agregar_maquinaria.php" class="btn btn-agregar"><i class="bi bi-plus-circle me-1"></i>Agregar Maquinaria</a>
</nav>

<div class="container py-4">
  <!-- Pestañas -->
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item">
      <a class="nav-link <?= $filtro == 'nueva' ? 'active' : '' ?>" href="?tipo=nueva">Nueva</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $filtro == 'usada' ? 'active' : '' ?>" href="?tipo=usada">Usada</a>
    </li>
  </ul>

  <!-- Buscador -->
  <form method="GET" class="mb-4">
    <input type="hidden" name="tipo" value="<?= htmlspecialchars($filtro) ?>">
    <div class="input-group">
      <input type="text" name="busqueda" class="form-control" placeholder="Buscar maquinaria..." value="<?= htmlspecialchars($busqueda) ?>">
      <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
    </div>
  </form>

  <!-- Tarjetas -->
  <div class="row g-4">
    <?php if ($resultado->num_rows > 0): ?>
      <?php while ($row = $resultado->fetch_assoc()): ?>
        <div class="col-12 col-md-6 col-lg-4">
          <div class="tarjeta-maquinaria p-3 d-flex flex-column">
            <div class="position-relative mb-3">
              <img src="imagenes/<?= $row['imagen']; ?>" class="img-maquinaria" alt="maquinaria">
              <div class="position-absolute top-0 start-0 m-2">
                <span class="<?= $filtro == 'nueva' ? 'etiqueta-nueva' : 'etiqueta-usada' ?>">
                  <?= ucfirst($filtro) ?>
                </span>
              </div>
            </div>
            <h5><?= htmlspecialchars($row['nombre']) ?></h5>
            <p class="mb-1"><strong>Modelo:</strong> <?= $row['modelo'] ?></p>
            <p class="mb-1"><strong>Ubicación:</strong> <?= $row['ubicacion'] ?></p>
            <p class="mb-2"><strong>Condición:</strong> <?= $row['condicion_estimada'] ?>%</p>
            <div class="barra-condicion mb-3">
              <div style="width: <?= $row['condicion_estimada'] ?>%; height: 100%; background-color: #ffc107;"></div>
            </div>
            <div class="mt-auto d-flex justify-content-between">
              <a href="editar_maquinaria.php?id=<?= $row['id']; ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
              <a href="eliminar_maquinaria.php?id=<?= $row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar esta maquinaria?')"><i class="bi bi-trash"></i></a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12 text-center"><p class="text-muted">No se encontraron resultados.</p></div>
    <?php endif; ?>
  </div>
</div>

<!-- Botón flotante de exportar -->
<a href="exportar_excel.php?tipo=<?= $filtro ?>" class="btn btn-warning btn-lg rounded-circle floating-export" title="Exportar a Excel">
  <i class="bi bi-file-earmark-excel"></i>
</a>

</body>
</html>
