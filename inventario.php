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
    .etiqueta-nueva { background-color: #2525ddff; color: white; padding: 2px 8px; border-radius: 5px; font-size: 12px; }
    .etiqueta-usada { background-color: #2525ddff; color: white; padding: 2px 8px; border-radius: 5px; font-size: 12px; }
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
  <!-- contenido omitido por brevedad -->
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
