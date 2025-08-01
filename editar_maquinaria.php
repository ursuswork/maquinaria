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
    body { background-color: #012a5c; color: #ffffff; padding: 2rem; font-family: 'Segoe UI', sans-serif; }
    .form-control, .form-select {
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
        background-color: #012a5c;
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
<body>
<div class="container-ficha">
<div class="contenedor-editar">
<h4 class="text-center mb-4 text-primary">Editar Maquinaria</h4>

<?php if ($puede_editar): ?>
<form action="procesar_editar.php" enctype="multipart/form-data" method="POST">
<input class="form-control mb-3" name="id" type="hidden" value="<?= $maquinaria['id'] ?>"/>
<div class="mb-3">
<label class="form-label text-warning">Nombre</label>
<input class="form-control mb-3" name="nombre" required type="text" value="<?= htmlspecialchars($maquinaria['nombre']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Marca</label>
<input class="form-control mb-3" name="marca" required type="text" value="<?= htmlspecialchars($maquinaria['marca']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Modelo</label>
<input class="form-control mb-3" name="modelo" required type="text" value="<?= htmlspecialchars($maquinaria['modelo']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Año</label>
<input class="form-control mb-3" name="anio" type="number" min="1950" max="<?= date('Y')+1 ?>" value="<?= htmlspecialchars($maquinaria['anio']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Número de serie</label>
<input class="form-control mb-3" name="numero_serie" required type="text" value="<?= htmlspecialchars($maquinaria['numero_serie']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Ubicación</label>
<input class="form-control mb-3" name="ubicacion" required type="text" value="<?= htmlspecialchars($maquinaria['ubicacion']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Tipo</label>
<select class="form-select mb-3" id="tipo_maquinaria" name="tipo_maquinaria" onchange="mostrarSubtipo()" required>
  <option value="nueva" <?= $maquinaria['tipo_maquinaria'] == 'nueva' ? 'selected' : '' ?>>Producción Nueva</option>
  <option value="usada" <?= $maquinaria['tipo_maquinaria'] == 'usada' ? 'selected' : '' ?>>Usada</option>
  <option value="camion" <?= $maquinaria['tipo_maquinaria'] == 'camion' ? 'selected' : '' ?>>Camión</option>
</select>
</div>
<!-- Subtipo y capacidad juntos -->
<div class="mb-3" id="subtipo_contenedor" style="display: none;">
  <label class="form-label text-warning">Subtipo / Capacidad</label>
  <div class="row g-2">
    <div class="col-12 col-md-6">
      <select class="form-select" id="subtipo_maquina" name="subtipo" onchange="mostrarCapacidad()">
        <option value="">Selecciona una opción</option>
        <option value="petrolizadora" <?= $maquinaria['subtipo']=='petrolizadora' ? 'selected' : '' ?>>Petrolizadora</option>
        <option value="esparcidor de sello" <?= $maquinaria['subtipo']=='esparcidor de sello' ? 'selected' : '' ?>>Esparcidor de sello</option>
        <option value="tanque de almacen" <?= $maquinaria['subtipo']=='tanque de almacen' ? 'selected' : '' ?>>Tanque de almacén</option>
        <option value="bachadora" <?= $maquinaria['subtipo']=='bachadora' ? 'selected' : '' ?>>Bachadora</option>
        <option value="planta de mezcla en frio" <?= $maquinaria['subtipo']=='planta de mezcla en frio' ? 'selected' : '' ?>>Planta de mezcla en frío</option>
      </select>
    </div>
    <div class="col-12 col-md-6" id="capacidad_contenedor" style="display:none;">
      <select class="form-select" name="capacidad" id="capacidad_maquina"></select>
    </div>
  </div>
</div>
<div class="mb-3">
<label class="form-label text-warning">Imagen actual:</label><br>
<?php if (!empty($maquinaria['imagen'])): ?>
  <img class="preview" src="imagenes/<?= $maquinaria['imagen'] ?>"/>
<?php endif; ?>
</div>
<div class="mb-3">
<label class="form-label text-warning">Nueva imagen (opcional)</label>
<input accept="image/*" class="form-control mb-3" name="imagen" type="file"/>
</div>
<div class="d-grid mb-2">
<button class="btn btn-primary btn btn-warning w-100 mt-3" type="submit">Guardar Cambios</button>
</div>
</form>
<?php else: ?>
<div class="alert alert-warning text-center">
  <b>No tienes permisos para editar esta maquinaria.</b>
</div>
<!-- Solo muestra los datos -->
<ul class="list-group">
  <li class="list-group-item bg-dark text-warning"><b>Nombre:</b> <?= htmlspecialchars($maquinaria['nombre']) ?></li>
  <li class="list-group-item bg-dark text-warning"><b>Marca:</b> <?= htmlspecialchars($maquinaria['marca']) ?></li>
  <li class="list-group-item bg-dark text-warning"><b>Modelo:</b> <?= htmlspecialchars($maquinaria['modelo']) ?></li>
  <li class="list-group-item bg-dark text-warning"><b>Año:</b> <?= htmlspecialchars($maquinaria['anio']) ?></li>
  <li class="list-group-item bg-dark text-warning"><b>Número de serie:</b> <?= htmlspecialchars($maquinaria['numero_serie']) ?></li>
  <li class="list-group-item bg-dark text-warning"><b>Ubicación:</b> <?= htmlspecialchars($maquinaria['ubicacion']) ?></li>
  <li class="list-group-item bg-dark text-warning"><b>Tipo:</b> <?= htmlspecialchars($maquinaria['tipo_maquinaria']) ?></li>
  <li class="list-group-item bg-dark text-warning"><b>Subtipo:</b> <?= htmlspecialchars($maquinaria['subtipo']) ?></li>
  <li class="list-group-item bg-dark text-warning"><b>Capacidad:</b> <?= htmlspecialchars($maquinaria['capacidad']) ?></li>
  <?php if (!empty($maquinaria['imagen'])): ?>
  <li class="list-group-item bg-dark text-warning"><img class="preview" src="imagenes/<?= $maquinaria['imagen'] ?>"/></li>
  <?php endif; ?>
</ul>
<?php endif; ?>
<a class="btn btn-regresar w-100 text-center" href="inventario.php">← Volver al Inventario</a>
</div>
<script>
// Subtipo y capacidad
function mostrarSubtipo() {
  const tipo = document.getElementById('tipo_maquinaria').value;
  const subtipoCont = document.getElementById('subtipo_contenedor');
  if (tipo === 'nueva') {
    subtipoCont.style.display = 'block';
    mostrarCapacidad();
  } else {
    subtipoCont.style.display = 'none';
    document.getElementById('capacidad_contenedor').style.display = 'none';
  }
}

function mostrarCapacidad() {
  const subtipo = document.getElementById('subtipo_maquina').value;
  const capacidadCont = document.getElementById('capacidad_contenedor');
  const selectCap = document.getElementById('capacidad_maquina');
  let opciones = [];
  capacidadCont.style.display = 'none';

  // Capacidad según subtipo:
  if (subtipo === 'petrolizadora') {
    opciones = [
      {v: '6000', t: '6,000 litros'},
      {v: '8000', t: '8,000 litros'},
      {v: '10000', t: '10,000 litros'},
      {v: '12000', t: '12,000 litros'},
      {v: '15000', t: '15,000 litros'},
      {v: '18000', t: '18,000 litros'},
      {v: '20000', t: '20,000 litros'}
    ];
  }
  if (subtipo === 'bachadora') {
    opciones = [
      {v: '1000', t: '1,000 litros'},
      {v: '2000', t: '2,000 litros'}
    ];
  }
  if (subtipo === 'tanque de almacen') {
    opciones = [
      {v: '40', t: '40 litros'},
      {v: '60', t: '60 litros'},
      {v: '80', t: '80 litros'}
    ];
  }
  if (subtipo === 'planta de mezcla en frio') {
    opciones = [
      {v: '70', t: '70 toneladas'},
      {v: '150', t: '150 toneladas'}
    ];
  }

  selectCap.innerHTML = '';
  if (opciones.length > 0) {
    capacidadCont.style.display = 'block';
    let capacidadActual = '<?= isset($maquinaria['capacidad']) ? $maquinaria['capacidad'] : '' ?>';
    selectCap.innerHTML = '<option value="">Selecciona capacidad</option>';
    opciones.forEach(function(opt) {
      const selected = (opt.v == capacidadActual) ? 'selected' : '';
      selectCap.innerHTML += `<option value="${opt.v}" ${selected}>${opt.t}</option>`;
    });
  }
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', function() {
  mostrarSubtipo();
  setTimeout(mostrarCapacidad, 120); // Garantiza pintado tras el DOM cargado
});
</script>
</body>
</html>
