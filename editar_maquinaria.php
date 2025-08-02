<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
  echo "ID inválido";
  exit;
}
$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id")->fetch_assoc();
if (!$maquinaria) {
  echo "Maquinaria no encontrada";
  exit;
}

$usuario = $_SESSION['usuario'] ?? '';
$rol = $_SESSION['rol'] ?? 'consulta';

// Permisos
$tipos_permitidos = [];
if ($usuario === 'jabri') {
  $tipos_permitidos = ['nueva', 'usada', 'camion'];
} elseif ($rol === 'produccion') {
  $tipos_permitidos = ['nueva', 'camion'];
} elseif ($rol === 'usada') {
  $tipos_permitidos = ['usada'];
} else {
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

  // Imagen (opcional)
  $nombre_imagen = $maquinaria['imagen'];
  if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombre_imagen = time() . '_' . uniqid() . '.' . $extension;
    if (!is_dir('imagenes')) mkdir('imagenes', 0755, true);
    $ruta_destino = 'imagenes/' . $nombre_imagen;
    move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino);
  }

  $stmt = $conn->prepare("UPDATE maquinaria SET nombre=?, marca=?, modelo=?, ubicacion=?, numero_serie=?, anio=?, tipo_maquinaria=?, subtipo=?, capacidad=?, imagen=? WHERE id=?");
  $stmt->bind_param("ssssssssssi", $nombre, $marca, $modelo, $ubicacion, $numero_serie, $anio, $tipo_maquinaria, $subtipo, $capacidad, $nombre_imagen, $id);

  if ($stmt->execute()) {
    header("Location: inventario.php");
    exit;
  } else {
    $error = "❌ Error al guardar: " . $stmt->error;
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<title>Editar Maquinaria</title>
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<style>
    body { background-color: #012a5c; color: #fff; padding: 2rem; font-family: 'Segoe UI', sans-serif; }
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
    .container-ficha {
        background-color: #002b5c;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 0 20px #000;
        max-width: 900px;
        margin: auto;
        border-left: 5px solid #ffc107;
    }
    h4 { color: #ffffff; border-bottom: 2px solid #ffc107; padding-bottom: .5rem; margin-bottom: 1.5rem; }
    .text-warning { color: #ffc107 !important; }
</style>
</head>
<body>
<div class="container-ficha">
<div class="contenedor-formulario">
<h4 class="text-center mb-4 text-primary">Editar Maquinaria</h4>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form action="" enctype="multipart/form-data" method="POST">
<div class="mb-3">
<label class="form-label text-warning">Nombre</label>
<input class="form-control" name="nombre" required type="text" value="<?= htmlspecialchars($_POST['nombre'] ?? $maquinaria['nombre']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Marca</label>
<input class="form-control" name="marca" required type="text" value="<?= htmlspecialchars($_POST['marca'] ?? $maquinaria['marca']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Modelo</label>
<input class="form-control" name="modelo" required type="text" value="<?= htmlspecialchars($_POST['modelo'] ?? $maquinaria['modelo']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Año</label>
<input class="form-control" name="anio" type="number" min="1950" max="<?= date('Y')+1 ?>" value="<?= htmlspecialchars($_POST['anio'] ?? $maquinaria['anio']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Número de serie</label>
<input class="form-control" name="numero_serie" required type="text" value="<?= htmlspecialchars($_POST['numero_serie'] ?? $maquinaria['numero_serie']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Ubicación</label>
<input class="form-control" name="ubicacion" required type="text" value="<?= htmlspecialchars($_POST['ubicacion'] ?? $maquinaria['ubicacion']) ?>"/>
</div>
<div class="mb-3">
<label class="form-label text-warning">Tipo</label>
<select class="form-select" id="tipo_maquinaria" name="tipo_maquinaria" onchange="mostrarSubtipo()" required>
  <option value="">Seleccionar</option>
  <?php if (in_array('nueva', $tipos_permitidos)): ?>
    <option value="nueva" <?= (($maquinaria['tipo_maquinaria']=='nueva' || ($_POST['tipo_maquinaria'] ?? '')=='nueva')?'selected':''); ?>>Producción Nueva</option>
  <?php endif; ?>
  <?php if (in_array('usada', $tipos_permitidos)): ?>
    <option value="usada" <?= (($maquinaria['tipo_maquinaria']=='usada' || ($_POST['tipo_maquinaria'] ?? '')=='usada')?'selected':''); ?>>Usada</option>
  <?php endif; ?>
  <?php if (in_array('camion', $tipos_permitidos)): ?>
    <option value="camion" <?= (($maquinaria['tipo_maquinaria']=='camion' || ($_POST['tipo_maquinaria'] ?? '')=='camion')?'selected':''); ?>>Camión</option>
  <?php endif; ?>
</select>
</div>
<div class="mb-3" id="subtipo_contenedor" style="display:none;">
<label class="form-label text-warning">Subtipo</label>
<select class="form-select" name="subtipo" id="subtipo_maquina" onchange="mostrarCapacidad()">
  <option value="">Seleccionar</option>
  <option value="petrolizadora" <?= (($maquinaria['subtipo']=='petrolizadora' || ($_POST['subtipo'] ?? '')=='petrolizadora')?'selected':''); ?>>Petrolizadora</option>
  <option value="esparcidor de sello" <?= (($maquinaria['subtipo']=='esparcidor de sello' || ($_POST['subtipo'] ?? '')=='esparcidor de sello')?'selected':''); ?>>Esparcidor de sello</option>
  <option value="tanque de almacén" <?= (($maquinaria['subtipo']=='tanque de almacén' || ($_POST['subtipo'] ?? '')=='tanque de almacén')?'selected':''); ?>>Tanque de almacén</option>
  <option value="bachadora" <?= (($maquinaria['subtipo']=='bachadora' || ($_POST['subtipo'] ?? '')=='bachadora')?'selected':''); ?>>Bachadora</option>
  <option value="planta de mezcla en frío" <?= (($maquinaria['subtipo']=='planta de mezcla en frío' || ($_POST['subtipo'] ?? '')=='planta de mezcla en frío')?'selected':''); ?>>Planta de mezcla en frío</option>
</select>
</div>
<div class="mb-3" id="capacidad_contenedor" style="display:none;">
<label class="form-label text-warning">Capacidad</label>
<select class="form-select" name="capacidad" id="capacidad_maquina">
  <option value="">Seleccionar capacidad</option>
  <!-- Opciones se llenan por JS -->
</select>
</div>
<div class="mb-3">
<label class="form-label text-warning">Imagen</label>
<input accept="image/*" class="form-control" name="imagen" type="file"/>
<?php if (!empty($maquinaria['imagen'])): ?>
  <img src="imagenes/<?= htmlspecialchars($maquinaria['imagen']) ?>" alt="Imagen actual" style="max-width:140px;border-radius:6px;border:2px solid #ffc107;">
<?php endif; ?>
</div>
<div class="d-grid mb-2">
    <button class="btn btn-success btn btn-warning w-100 mt-3" type="submit">Guardar Cambios</button>
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
  // Valor anterior: preferimos POST, sino del registro original
  let old = "<?= htmlspecialchars($_POST['capacidad'] ?? $maquinaria['capacidad'] ?? '') ?>";
  for (let op of opciones) {
    let texto = op;
    if (subtipo === 'petrolizadora' || subtipo === 'bachadora' || subtipo === 'tanque de almacén') texto += " litros";
    if (subtipo === 'planta de mezcla en frío') texto += " toneladas";
    let selected = (op == old) ? 'selected' : '';
    selectCap.innerHTML += `<option value="${op}" ${selected}>${texto}</option>`;
  }
}
// Al cargar, mostrar correctamente subtipo/capacidad según datos actuales
document.addEventListener('DOMContentLoaded', function() {
  mostrarSubtipo();
  <?php
  $isNueva = ($_POST['tipo_maquinaria'] ?? $maquinaria['tipo_maquinaria'] ?? '') == 'nueva';
  $hasSubtipo = ($_POST['subtipo'] ?? $maquinaria['subtipo'] ?? '');
  $hasCap = ($_POST['capacidad'] ?? $maquinaria['capacidad'] ?? '');
  if ($isNueva && $hasSubtipo) { ?>
    document.getElementById('subtipo_contenedor').style.display = 'block';
    mostrarCapacidad();
    <?php if ($hasCap) { ?>
      document.getElementById('capacidad_contenedor').style.display = 'block';
    <?php } ?>
  <?php } ?>
});
</script>
</div>
</body>
</html>
