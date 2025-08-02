<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$usuario = $_SESSION['usuario'] ?? '';
$rol = $_SESSION['rol'] ?? 'consulta';

// Permisos
if (!($usuario === 'jabri' || $rol === 'produccion' || $rol === 'usada')) {
  header("Location: inventario.php");
  exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre']);
  $marca = trim($_POST['marca']);
  $modelo = trim($_POST['modelo']);
  $ubicacion = trim($_POST['ubicacion']);
  $numero_serie = trim($_POST['numero_serie']);
  $anio = intval($_POST['anio']);
  $tipo_maquinaria = $_POST['tipo_maquinaria'];
  $subtipo = ($tipo_maquinaria == 'nueva') ? ($_POST['subtipo'] ?? '') : null;
  $capacidad = isset($_POST['capacidad']) ? trim($_POST['capacidad']) : null;

  // Subida de imagen
  $nombre_imagen = null;
  if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombre_imagen = time() . '_' . uniqid() . '.' . $extension;
    if (!is_dir('imagenes')) mkdir('imagenes', 0755, true);
    $ruta_destino = 'imagenes/' . $nombre_imagen;
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
      $error = "❌ Error al mover la imagen a '$ruta_destino'. Verifica permisos.";
    }
  }

  if (!$error) {
    $stmt = $conn->prepare("INSERT INTO maquinaria (nombre, marca, modelo, ubicacion, numero_serie, anio, tipo_maquinaria, subtipo, capacidad, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $nombre, $marca, $modelo, $ubicacion, $numero_serie, $anio, $tipo_maquinaria, $subtipo, $capacidad, $nombre_imagen);

    if ($stmt->execute()) {
      header("Location: inventario.php");
      exit;
    } else {
      $error = "❌ Error al guardar: " . $stmt->error;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<title>Agregar Maquinaria</title>
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
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
    .text-warning { color: #ffc107 !important; }
</style>
</head>
<body>
<div class="container-ficha">
<div class="contenedor-formulario">
<h4 class="text-center mb-4 text-primary">Agregar Maquinaria</h4>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form action="" enctype="multipart/form-data" method="POST">
<div class="mb-3">
<label class="form-label text-warning">Nombre</label>
<input class="form-control" name="nombre" required type="text"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Marca</label>
<input class="form-control" name="marca" required type="text"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Modelo</label>
<input class="form-control" name="modelo" required type="text"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Año</label>
<input class="form-control" name="anio" type="number" min="1950" max="<?= date('Y')+1 ?>" />
</div>
<div class="mb-3">
<label class="form-label text-warning">Número de serie</label>
<input class="form-control" name="numero_serie" required type="text"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Ubicación</label>
<input class="form-control" name="ubicacion" required type="text"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Tipo</label>
<select class="form-select" id="tipo_maquinaria" name="tipo_maquinaria" onchange="mostrarSubtipo()" required>
  <option value="">Seleccionar</option>
  <option value="nueva">Producción Nueva</option>
  <option value="usada">Usada</option>
  <option value="camion">Camión</option>
</select>
</div>
<div class="mb-3" id="subtipo_contenedor" style="display:none;">
<label class="form-label text-warning">Subtipo</label>
<select class="form-select" name="subtipo" id="subtipo_maquina" onchange="mostrarCapacidad()">
  <option value="">Seleccionar</option>
  <option value="petrolizadora">Petrolizadora</option>
  <option value="esparcidor de sello">Esparcidor de sello</option>
  <option value="tanque de almacén">Tanque de almacén</option>
  <option value="bachadora">Bachadora</option>
  <option value="planta de mezcla en frío">Planta de mezcla en frío</option>
</select>
</div>
<div class="mb-3" id="capacidad_contenedor" style="display:none;">
<label class="form-label text-warning">Capacidad</label>
<select class="form-select" name="capacidad" id="capacidad_maquina">
  <option value="">Seleccionar capacidad</option>
  <!-- Las opciones se llenan por JS -->
</select>
</div>
<div class="mb-3">
<label class="form-label text-warning">Imagen</label>
<input accept="image/*" class="form-control" name="imagen" type="file"/>
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
  const subtipoCont = document.getElementById('subtipo_contenedor');
  subtipoCont.style.display = (tipo === 'nueva') ? 'block' : 'none';
  mostrarCapacidad();
}
function mostrarCapacidad() {
  const subtipo = document.getElementById('subtipo_maquina') ? document.getElementById('subtipo_maquina').value : '';
  const capacidadCont = document.getElementById('capacidad_contenedor');
  const selectCap = document.getElementById('capacidad_maquina');
  capacidadCont.style.display = 'none';
  selectCap.innerHTML = "<option value=''>Seleccionar capacidad</option>";
  let opciones = [];
  if (subtipo === 'petrolizadora') {
    opciones = ['6000','8000','10000','12000','15000','18000','20000'];
    capacidadCont.style.display = 'block';
  }
  if (subtipo === 'bachadora') {
    opciones = ['1000','2000'];
    capacidadCont.style.display = 'block';
  }
  if (subtipo === 'tanque de almacén') {
    opciones = ['40','60','80'];
    capacidadCont.style.display = 'block';
  }
  if (subtipo === 'planta de mezcla en frío') {
    opciones = ['70','150'];
    capacidadCont.style.display = 'block';
  }
  for (let op of opciones) {
    let texto = op;
    if (subtipo === 'petrolizadora' || subtipo === 'bachadora' || subtipo === 'tanque de almacén') texto += " litros";
    if (subtipo === 'planta de mezcla en frío') texto += " toneladas";
    selectCap.innerHTML += `<option value="${op}">${texto}</option>`;
  }
}
// Mostrar correcto subtipo/capacidad al volver atrás
document.addEventListener('DOMContentLoaded', function() {
  mostrarSubtipo();
});
</script>
</div>
</body>
</html>
