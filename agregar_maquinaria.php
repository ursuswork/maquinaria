<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}

// --- SOLO "jabri" puede todo, pero roles deciden qué pueden agregar ---
$usuario = $_SESSION['usuario'] ?? '';
$rol = $_SESSION['rol'] ?? 'consulta';

$tipos_permitidos = [];
if ($usuario === 'jabri') {
  $tipos_permitidos = ['nueva', 'usada', 'camion'];
} elseif ($rol === 'produccion') {
  $tipos_permitidos = ['nueva', 'camion'];
} elseif ($rol === 'usada') {
  $tipos_permitidos = ['usada'];
} else {
  // Consulta u otro: No puede entrar
  header("Location: inventario.php");
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
  <?php if (in_array('nueva', $tipos_permitidos)): ?>
    <option value="nueva">Producción Nueva</option>
  <?php endif; ?>
  <?php if (in_array('usada', $tipos_permitidos)): ?>
    <option value="usada">Usada</option>
  <?php endif; ?>
  <?php if (in_array('camion', $tipos_permitidos)): ?>
    <option value="camion">Camión</option>
  <?php endif; ?>
</select>
</div>
<div class="mb-3" id="subtipo_contenedor" style="display: none;">
<label class="form-label text-warning">Subtipo</label>
<select class="form-select mb-3" name="subtipo" id="subtipo" onchange="mostrarCapacidad()">
  <option value="">Selecciona una opción</option>
  <option value="Petrolizadora">Petrolizadora</option>
  <option value="Esparcidor de sello">Esparcidor de sello</option>
  <option value="Tanque de almacén">Tanque de almacén</option>
  <option value="Bachadora">Bachadora</option>
  <option value="Planta de mezcla en frío">Planta de mezcla en frío</option>
</select>
</div>

<!-- Petrolizadora -->
<div class="mb-3" id="capacidad_petrolizadora_contenedor" style="display:none;">
  <label class="form-label text-warning">Capacidad Petrolizadora</label>
  <select class="form-select mb-3" name="capacidad_petrolizadora" id="capacidad_petrolizadora">
    <option value="">Seleccionar capacidad</option>
    <option value="6000">6,000 L</option>
    <option value="8000">8,000 L</option>
    <option value="10000">10,000 L</option>
    <option value="12000">12,000 L</option>
    <option value="15000">15,000 L</option>
    <option value="18000">18,000 L</option>
    <option value="20000">20,000 L</option>
  </select>
</div>
<!-- Bachadora -->
<div class="mb-3" id="capacidad_bachadora_contenedor" style="display:none;">
  <label class="form-label text-warning">Capacidad Bachadora</label>
  <select class="form-select mb-3" name="capacidad_bachadora" id="capacidad_bachadora">
    <option value="">Seleccionar capacidad</option>
    <option value="1000">1,000 L</option>
    <option value="2000">2,000 L</option>
  </select>
</div>
<!-- Tanque de Almacén -->
<div class="mb-3" id="capacidad_tanque_contenedor" style="display:none;">
  <label class="form-label text-warning">Capacidad Tanque de Almacén</label>
  <select class="form-select mb-3" name="capacidad_tanque" id="capacidad_tanque">
    <option value="">Seleccionar capacidad</option>
    <option value="40">40,000 L</option>
    <option value="60">60,000 L</option>
    <option value="80">80,000 L</option>
  </select>
</div>
<!-- Planta de Mezcla en Frío -->
<div class="mb-3" id="capacidad_planta_contenedor" style="display:none;">
  <label class="form-label text-warning">Capacidad Planta de mezcla en frío</label>
  <select class="form-select mb-3" name="capacidad_planta" id="capacidad_planta">
    <option value="">Seleccionar capacidad</option>
    <option value="70">70 toneladas</option>
    <option value="150">150 toneladas</option>
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
  const subtipoContainer = document.getElementById('subtipo_contenedor');
  subtipoContainer.style.display = (tipo === 'nueva') ? 'block' : 'none';
  mostrarCapacidad();
}
function mostrarCapacidad() {
  const tipo = document.getElementById('tipo_maquinaria').value;
  const subtipo = document.getElementById('subtipo')?.value;
  document.getElementById('capacidad_petrolizadora_contenedor').style.display = (tipo === 'nueva' && subtipo === 'Petrolizadora') ? 'block' : 'none';
  document.getElementById('capacidad_bachadora_contenedor').style.display = (tipo === 'nueva' && subtipo === 'Bachadora') ? 'block' : 'none';
  document.getElementById('capacidad_tanque_contenedor').style.display = (tipo === 'nueva' && subtipo === 'Tanque de almacén') ? 'block' : 'none';
  document.getElementById('capacidad_planta_contenedor').style.display = (tipo === 'nueva' && subtipo === 'Planta de mezcla en frío') ? 'block' : 'none';
}
document.getElementById('tipo_maquinaria').addEventListener('change', mostrarSubtipo);
document.getElementById('subtipo').addEventListener('change', mostrarCapacidad);
</script>
</div>
</body>
</html>
