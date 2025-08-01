<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

// ------ INTEGRACION DE ROL ------
$usuario = $_SESSION['usuario'] ?? '';
$rol = $_SESSION['rol'] ?? 'consulta'; // produccion, usada, consulta

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
  die("❌ ID inválido.");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id")->fetch_assoc();
if (!$maquinaria) {
  die("❌ Maquinaria no encontrada.");
}

$tipo = strtolower($maquinaria['tipo_maquinaria']);

// Permisos: jabri = todo; produccion=nueva/camion, usada=usada/camion, consulta=ninguno
$puede_editar = false;
if ($usuario === 'jabri') {
    $puede_editar = true;
} elseif ($rol == 'produccion' && ($tipo == 'nueva' || $tipo == 'camion')) {
    $puede_editar = true;
} elseif ($rol == 'usada' && ($tipo == 'usada' || $tipo == 'camion')) {
    $puede_editar = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<title>Editar Maquinaria</title>
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
    .preview { max-width:140px; display:block; margin-bottom:18px; border-radius:8px; }
</style>
</head>
<body class="bg-dark text-white">
<div class="container-ficha">
<div class="contenedor-editar">
<h4 class="text-center mb-4 text-primary">Editar Maquinaria</h4>

<?php if ($puede_editar): ?>
<form action="procesar_editar.php" enctype="multipart/form-data" method="POST">
<input class="form-control mb-3" name="id" type="hidden" value="<?= $maquinaria['id'] ?>"/>
<div class="mb-3">
<label class="form-label form-label text-warning">Nombre</label>
<input class="form-control form-control mb-3" name="nombre" required type="text" value="<?= htmlspecialchars($maquinaria['nombre']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label form-label text-warning">Modelo</label>
<input class="form-control form-control mb-3" name="modelo" required type="text" value="<?= htmlspecialchars($maquinaria['modelo']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label form-label text-warning">Marca</label>
<input class="form-control form-control mb-3" name="marca" required type="text" value="<?= htmlspecialchars($maquinaria['marca']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label form-label text-warning">Año</label>
<input class="form-control form-control mb-3" name="anio" type="number" value="<?= htmlspecialchars($maquinaria['anio']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label form-label text-warning">Ubicación</label>
<input class="form-control form-control mb-3" name="ubicacion" required type="text" value="<?= htmlspecialchars($maquinaria['ubicacion']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label form-label text-warning">Tipo</label>
<select class="form-select form-control mb-3" id="tipo" name="tipo_maquinaria" required>
<option value="nueva" <?= $maquinaria['tipo_maquinaria'] == 'nueva' ? 'selected' : '' ?>>Nueva</option>
<option value="usada" <?= $maquinaria['tipo_maquinaria'] == 'usada' ? 'selected' : '' ?>>Usada</option>
<option value="camion" <?= $maquinaria['tipo_maquinaria'] == 'camion' ? 'selected' : '' ?>>Camión</option>
</select>
</div>
<div class="mb-3" id="subtipo-container" style="display: none;">
<label class="form-label form-label text-warning">Subtipo</label>
<select class="form-select form-control mb-3" name="subtipo" id="subtipo">
<option value="">Selecciona una opción</option>
<option value="Petrolizadora" <?= $maquinaria['subtipo'] == 'Petrolizadora' ? 'selected' : '' ?>>Petrolizadora</option>
<option value="Esparcidor de sello" <?= $maquinaria['subtipo'] == 'Esparcidor de sello' ? 'selected' : '' ?>>Esparcidor de sello</option>
<option value="Tanque de almacén" <?= $maquinaria['subtipo'] == 'Tanque de almacén' ? 'selected' : '' ?>>Tanque de almacén</option>
<option value="Bachadora" <?= $maquinaria['subtipo'] == 'Bachadora' ? 'selected' : '' ?>>Bachadora</option>
<option value="Planta de mezcla en frío" <?= $maquinaria['subtipo'] == 'Planta de mezcla en frío' ? 'selected' : '' ?>>Planta de mezcla en frío</option>
</select>
</div>
<!-- Petrolizadora -->
<div class="mb-3" id="capacidad_petrolizadora_contenedor" style="display:none;">
  <label class="form-label text-warning">Capacidad Petrolizadora</label>
  <select class="form-select mb-3" name="capacidad_petrolizadora" id="capacidad_petrolizadora">
    <option value="">Seleccionar capacidad</option>
    <option value="6000" <?= ($maquinaria['capacidad_petrolizadora'] ?? '') == '6000' ? 'selected' : '' ?>>6,000 L</option>
    <option value="8000" <?= ($maquinaria['capacidad_petrolizadora'] ?? '') == '8000' ? 'selected' : '' ?>>8,000 L</option>
    <option value="10000" <?= ($maquinaria['capacidad_petrolizadora'] ?? '') == '10000' ? 'selected' : '' ?>>10,000 L</option>
    <option value="12000" <?= ($maquinaria['capacidad_petrolizadora'] ?? '') == '12000' ? 'selected' : '' ?>>12,000 L</option>
    <option value="15000" <?= ($maquinaria['capacidad_petrolizadora'] ?? '') == '15000' ? 'selected' : '' ?>>15,000 L</option>
    <option value="18000" <?= ($maquinaria['capacidad_petrolizadora'] ?? '') == '18000' ? 'selected' : '' ?>>18,000 L</option>
    <option value="20000" <?= ($maquinaria['capacidad_petrolizadora'] ?? '') == '20000' ? 'selected' : '' ?>>20,000 L</option>
  </select>
</div>
<!-- Bachadora -->
<div class="mb-3" id="capacidad_bachadora_contenedor" style="display:none;">
  <label class="form-label text-warning">Capacidad Bachadora</label>
  <select class="form-select mb-3" name="capacidad_bachadora" id="capacidad_bachadora">
    <option value="">Seleccionar capacidad</option>
    <option value="1000" <?= ($maquinaria['capacidad_bachadora'] ?? '') == '1000' ? 'selected' : '' ?>>1,000 L</option>
    <option value="2000" <?= ($maquinaria['capacidad_bachadora'] ?? '') == '2000' ? 'selected' : '' ?>>2,000 L</option>
  </select>
</div>
<!-- Tanque de Almacén -->
<div class="mb-3" id="capacidad_tanque_contenedor" style="display:none;">
  <label class="form-label text-warning">Capacidad Tanque de Almacén</label>
  <select class="form-select mb-3" name="capacidad_tanque" id="capacidad_tanque">
    <option value="">Seleccionar capacidad</option>
    <option value="40" <?= ($maquinaria['capacidad_tanque'] ?? '') == '40' ? 'selected' : '' ?>>40,000 L</option>
    <option value="60" <?= ($maquinaria['capacidad_tanque'] ?? '') == '60' ? 'selected' : '' ?>>60,000 L</option>
    <option value="80" <?= ($maquinaria['capacidad_tanque'] ?? '') == '80' ? 'selected' : '' ?>>80,000 L</option>
  </select>
</div>
<!-- Planta de Mezcla en Frío -->
<div class="mb-3" id="capacidad_planta_contenedor" style="display:none;">
  <label class="form-label text-warning">Capacidad Planta de mezcla en frío</label>
  <select class="form-select mb-3" name="capacidad_planta" id="capacidad_planta">
    <option value="">Seleccionar capacidad</option>
    <option value="70" <?= ($maquinaria['capacidad_planta'] ?? '') == '70' ? 'selected' : '' ?>>70 toneladas</option>
    <option value="150" <?= ($maquinaria['capacidad_planta'] ?? '') == '150' ? 'selected' : '' ?>>150 toneladas</option>
  </select>
</div>

<div class="mb-3">
<label class="form-label form-label text-warning">Número de serie</label>
<input class="form-control mb-3" name="numero_serie" type="text" value="<?= htmlspecialchars($maquinaria['numero_serie']) ?>"/>
</div>

<div class="mb-3">
<?php if (!empty($maquinaria['imagen'])): ?>
<img class="preview" src="imagenes/<?= htmlspecialchars($maquinaria['imagen']) ?>" alt="Imagen previa"/>
<?php endif; ?>
<label class="form-label form-label text-warning">Imagen (opcional)</label>
<input accept="image/*" class="form-control mb-3" name="imagen" type="file"/>
</div>

<div class="d-grid mb-2">
    <button class="btn btn-success btn btn-warning w-100 mt-3" type="submit">Guardar Cambios</button>
</div>
</form>
<div class="text-center mt-2">
    <a href="inventario.php" class="btn btn-outline-info w-100">← Volver a Inventario</a>
</div>
<?php else: ?>
<div class="alert alert-danger">No tienes permisos para editar esta maquinaria.</div>
<?php endif; ?>
</div>
<script>
function mostrarSubtipo() {
  const tipo = document.getElementById('tipo').value;
  const subtipoContainer = document.getElementById('subtipo-container');
  subtipoContainer.style.display = (tipo === 'nueva') ? 'block' : 'none';
  mostrarCapacidad();
}
function mostrarCapacidad() {
  const tipo = document.getElementById('tipo').value;
  const subtipo = document.getElementById('subtipo')?.value;
  document.getElementById('capacidad_petrolizadora_contenedor').style.display = (tipo === 'nueva' && subtipo === 'Petrolizadora') ? 'block' : 'none';
  document.getElementById('capacidad_bachadora_contenedor').style.display = (tipo === 'nueva' && subtipo === 'Bachadora') ? 'block' : 'none';
  document.getElementById('capacidad_tanque_contenedor').style.display = (tipo === 'nueva' && subtipo === 'Tanque de almacén') ? 'block' : 'none';
  document.getElementById('capacidad_planta_contenedor').style.display = (tipo === 'nueva' && subtipo === 'Planta de mezcla en frío') ? 'block' : 'none';
}
// Inicializar valores
document.getElementById('tipo').addEventListener('change', mostrarSubtipo);
document.getElementById('subtipo').addEventListener('change', mostrarCapacidad);
window.onload = function() {
  mostrarSubtipo();
  mostrarCapacidad();
};
</script>
</div>
</body>
</html>

