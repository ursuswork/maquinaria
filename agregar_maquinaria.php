<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<title>Agregar Maquinaria</title>
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="estilos_colores.css" rel="stylesheet"/>
<style>
    body { background-color: #001f3f; color: #ffffff; padding: 2rem; font-family: 'Segoe UI', sans-serif; }
    .form-control {
        margin-bottom: 1rem;
        background-color: #003366;
        color: white;
        border: 1px solid #0059b3;
    }
    .form-label { color: #ffc107; font-weight: 600; }
    .btn-warning, .btn-success, .btn-primary {
        width: auto;
        font-weight: bold;
        border: none;
    }
    .btn-outline-success, .btn-outline-warning, .btn-outline-danger {
        font-weight: bold;
    }
    .container-ficha {
        background-color: #002b5c;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 0 20px #000;
        max-width: 900px;
        margin: auto;
        border-left: 5px solid #ffc107;
    }
    h3, h5 {
        color: #ffffff;
        border-bottom: 2px solid #ffc107;
        padding-bottom: .5rem;
        margin-bottom: 1.5rem;
    }
</style>
</head>
<body class="bg-dark text-white">
<div class="container-ficha">
<div class="contenedor-formulario">
<h4 class="text-center mb-4 text-primary">Agregar Maquinaria</h4>
<form action="procesar_agregar.php" enctype="multipart/form-data" method="POST">
<div class="mb-3">
<label class="form-label text-warning">Nombre</label>
<input class="form-control mb-3" name="nombre" required="" type="text"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Marca</label>
<input class="form-control mb-3" name="marca" required="" type="text"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Modelo</label>
<input class="form-control mb-3" name="modelo" required="" type="text"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Año</label>
<input class="form-control mb-3" name="anio" type="number" min="1950" max="<?= date('Y')+1 ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Número de serie</label>
<input class="form-control mb-3" name="numero_serie" required="" type="text"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Ubicación</label>
<input class="form-control mb-3" name="ubicacion" required="" type="text"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Tipo</label>
<select class="form-select mb-3" id="tipo_maquinaria" name="tipo_maquinaria" onchange="mostrarSubtipo()" required="">
<option value="">Seleccionar</option>
<option value="nueva">Producción Nueva</option>
<option value="usada">Usada</option>
<option value="camion">Camión</option>
</select>
</div>
<div class="mb-3" id="subtipo_contenedor" style="display: none;">
<label class="form-label text-warning">Subtipo</label>
<select class="form-select mb-3" name="subtipo">
<option value="Petrolizadora">Petrolizadora</option>
<option value="Esparcidor de sello">Esparcidor de sello</option>
<option value="Tanque de almacén">Tanque de almacén</option>
<option value="Bachadora">Bachadora</option>
<option value="Planta de mezcla en frío">Planta de mezcla en frío</option>
</select>
</div>
<div class="mb-3">
<label class="form-label text-warning">Imagen</label>
<input accept="image/*" class="form-control mb-3" name="imagen" type="file"/>
</div>
<div class="d-grid mb-2">
    <button class="btn btn-success btn btn-warning w-100 mt-3" type="submit">Agregar Maquinaria</button>
</div>
</form>
<div class="text-center mt-2">
    <a href="inventario.php" class="btn btn-outline-info w-100">
        ← Volver a Inventario
    </a>
</div>
</div>
<script>
    function mostrarSubtipo() {
      const tipo = document.getElementById('tipo_maquinaria').value;
      const subtipo = document.getElementById('subtipo_contenedor');
      subtipo.style.display = (tipo === 'nueva') ? 'block' : 'none';
    }
</script>
</div>
</body>
</html>
