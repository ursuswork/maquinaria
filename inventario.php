<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$tipo_filtro = $_GET['tipo'] ?? 'todas';

$sql = "
  SELECT m.*, r.condicion_estimada, r.observaciones 
  FROM maquinaria m
  LEFT JOIN recibo_unidad r ON m.id = r.id_maquinaria
";
if (!empty($busqueda)) {
  $sql .= " WHERE (m.nombre LIKE '%$busqueda%' OR m.modelo LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'nueva') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "m.tipo_maquinaria = 'nueva'";
} elseif ($tipo_filtro === 'usada') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "m.tipo_maquinaria = 'usada'";
}
$sql .= " ORDER BY m.tipo_maquinaria ASC, m.nombre ASC";
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
    body { background-color: #001f3f; color: #ffffff; }
    .table thead th { background-color: #004080; color: #ffffff; border: none; }
    .table tbody tr { border-bottom: 1px solid #004f8c; }
    .table tbody tr:nth-child(even) { background-color: #003366; }
    .table tbody tr:nth-child(odd) { background-color: #002b5c; }
    .table td, .table th { padding: 1rem 1.2rem; vertical-align: middle; }
    .badge-nueva { background-color: #ffc107; color: #001f3f; padding: 6px 12px; border-radius: 6px; font-size: 0.9rem; font-weight: bold; }
    .progress { height: 22px; border-radius: 20px; background-color: #002b5c; overflow: hidden; }
    .progress-bar { font-weight: bold; background-color: #ffcc00 !important; color: black; border-radius: 20px; }
    .nav-tabs .nav-link.active { background-color: #ffc107; color: #001f3f; border-radius: 0.375rem 0.375rem 0 0; }
    .nav-tabs .nav-link { color: #ffffff; margin-right: 0.5rem; }
    .btn-outline-primary { color: #0074cc; border-color: #0074cc; transition: all 0.2s; }
    .btn-outline-primary:hover { background-color: #0074cc; color: white; transform: scale(1.05); }
    .btn-outline-danger { transition: all 0.2s; }
    .btn-outline-danger:hover { background-color: #dc3545; color: white; transform: scale(1.05); }
    .btn-outline-success { transition: all 0.2s; }
    .btn-outline-success:hover { background-color: #28a745; color: white; transform: scale(1.05); }
    .table-hover tbody tr:hover { background-color: #004d80; }
    .btn-flotante {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #ffc107;
      color: #001f3f;
      padding: 12px 20px;
      border: none;
      border-radius: 30px;
      font-weight: bold;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
      z-index: 1000;
      transition: all 0.3s;
    }
    .btn-flotante:hover {
      transform: scale(1.1);
      background-color: #e0a800;
      color: white;
    }
    .imagen-thumbnail {
      max-width: 80px;
      max-height: 80px;
      cursor: pointer;
      transition: transform 0.3s ease-in-out;
    }
    .imagen-thumbnail:hover {
      transform: scale(1.1);
      z-index: 999;
    }
    .modal-img {
      display: none;
      position: fixed;
      z-index: 9999;
      padding-top: 60px;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.9);
    }
    .modal-img-content {
      margin: auto;
      display: block;
      max-width: 90%;
      max-height: 90%;
    }
    .modal-img:hover { cursor: pointer; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
    <h3 class="text-white">Inventario de Maquinaria</h3>
    <div class="d-flex gap-2">
      <a href="agregar_maquinaria.php" class="btn btn-primary">+ Agregar Maquinaria</a>
      <a href="logout.php" class="btn btn-secondary">Cerrar sesión</a>
    </div>
  </div>
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link <?= $tipo_filtro == 'todas' ? 'active' : '' ?>" href="?tipo=todas">Todas</a></li>
    <li class="nav-item"><a class="nav-link <?= $tipo_filtro == 'nueva' ? 'active' : '' ?>" href="?tipo=nueva">Producción Nueva</a></li>
    <li class="nav-item"><a class="nav-link <?= $tipo_filtro == 'usada' ? 'active' : '' ?>" href="?tipo=usada">Usada</a></li>
  </ul>
  <form class="mb-3" method="GET">
    <div class="input-group">
      <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_filtro) ?>">
      <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, modelo o número de serie" value="<?= htmlspecialchars($busqueda) ?>">
    </div>
  </form>
  <table class="table table-hover table-bordered">
    <thead>
      <tr>
        <th>Imagen</th>
        <th>Nombre</th>
        <th>Modelo</th>
        <th>Ubicación</th>
        <th>Tipo</th>
        <th>Subtipo</th>
        <th>Avance/Condición</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($fila = $resultado->fetch_assoc()): ?>
      <tr>
        <td>
          <?php if (!empty($fila['imagen'])): ?>
            <img src="imagenes/<?= htmlspecialchars($fila['imagen']) ?>" alt="Imagen" class="imagen-thumbnail" onclick="ampliarImagen(this)">
          <?php else: ?>
            <span class="text-muted">Sin imagen</span>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($fila['nombre']) ?></td>
        <td><?= htmlspecialchars($fila['modelo']) ?></td>
        <td><?= htmlspecialchars($fila['ubicacion']) ?></td>
        <td>
          <?= $fila['tipo_maquinaria'] == 'nueva' ? '<span class="badge-nueva">Producción Nueva</span>' : 'Usada' ?>
        </td>
        <td><?= htmlspecialchars($fila['subtipo']) ?></td>
        <td>
          <?php
            if ($fila['tipo_maquinaria'] == 'usada') {
              $cond = intval($fila['condicion_estimada']);
              echo "<div class='progress'><div class='progress-bar' role='progressbar' style='width: {$cond}%'>{$cond}%</div></div>";
            } else {
              $avance = 0;
              echo "<div class='progress'><div class='progress-bar' role='progressbar' style='width: {$avance}%'>{$avance}%</div></div>";
            }
          ?>
        </td>
        <td>
          <a href="editar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">🖉</a>
          <a href="eliminar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger me-1" title="Eliminar" onclick="return confirm('¿Seguro que deseas eliminar esta maquinaria?')">🗑️</a>
          <?php if ($fila['tipo_maquinaria'] == 'nueva' && in_array($fila['subtipo'], ['esparcidor de sello', 'petrolizadora', 'bachadora'])): ?>
          <a href="avance_<?= str_replace(' ', '_', strtolower($fila['subtipo'])) ?>.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-success" title="Ver Avance">📊</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<a href="exportar_excel.php" class="btn-flotante" title="Exportar a Excel">📥 Exportar Excel</a>

<div id="modalImg" class="modal-img" onclick="cerrarImagen()">
  <img class="modal-img-content" id="imagenAmpliada">
</div>

<script>
  function ampliarImagen(img) {
    var modal = document.getElementById("modalImg");
    var modalImg = document.getElementById("imagenAmpliada");
    modal.style.display = "block";
    modalImg.src = img.src;
  }
  function cerrarImagen() {
    document.getElementById("modalImg").style.display = "none";
  }
</script>
</body>
</html>
