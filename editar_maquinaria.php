<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
  die("❌ ID inválido.");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id")-&gt;fetch_assoc();
if (!$maquinaria) {
  die("❌ Maquinaria no encontrada.");
}
?&gt;
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
</style></head>
<body class="bg-dark text-white"><div class="container-ficha">
<div class="contenedor-editar">
<h4 class="text-center mb-4 text-primary">Editar Maquinaria</h4>
<form action="procesar_editar.php" enctype="multipart/form-data" method="POST">
<input class="form-control mb-3" name="id" type="hidden" value="&lt;?= $maquinaria['id'] ?&gt;"/>
<div class="mb-3">
<label class="form-label form-label text-warning">Nombre</label>
<input class="form-control form-control mb-3" name="nombre" required="" type="text" value="&lt;?= htmlspecialchars($maquinaria['nombre']) ?&gt;"/>
</div>
<div class="mb-3">
<label class="form-label form-label text-warning">Modelo</label>
<input class="form-control form-control mb-3" name="modelo" required="" type="text" value="&lt;?= htmlspecialchars($maquinaria['modelo']) ?&gt;"/>
</div>
<div class="mb-3">
<label class="form-label form-label text-warning">Ubicación</label>
<input class="form-control form-control mb-3" name="ubicacion" required="" type="text" value="&lt;?= htmlspecialchars($maquinaria['ubicacion']) ?&gt;"/>
</div>
<div class="mb-3">
<label class="form-label form-label text-warning">Tipo</label>
<select class="form-select form-control mb-3" id="tipo" name="tipo_maquinaria" required="">
<option ''="" 'selected'="" :="" <?="$maquinaria['tipo_maquinaria']" =="nueva" ?="" value="nueva">&gt;Nueva</option>
<option ''="" 'selected'="" :="" <?="$maquinaria['tipo_maquinaria']" =="usada" ?="" value="usada">&gt;Usada</option>
</select>
</div>
<div class="mb-3" id="subtipo-container" style="display: none;">
<label class="form-label form-label text-warning">Subtipo</label>
<select class="form-select form-control mb-3" name="subtipo">
<option value="">Selecciona una opción</option>
<option ''="" 'selected'="" :="" <?="$maquinaria['subtipo']" =="petrolizadora" ?="" value="petrolizadora">&gt;Petrolizadora</option>
<option ''="" 'selected'="" :="" <?="$maquinaria['subtipo']" =="esparcidor de sello" ?="" value="esparcidor de sello">&gt;Esparcidor de sello</option>
<option ''="" 'selected'="" :="" <?="$maquinaria['subtipo']" =="tanque de almacen" ?="" value="tanque de almacen">&gt;Tanque de almacén</option>
<option ''="" 'selected'="" :="" <?="$maquinaria['subtipo']" =="bachadora" ?="" value="bachadora">&gt;Bachadora</option>
<option ''="" 'selected'="" :="" <?="$maquinaria['subtipo']" =="planta de mezcla en frio" ?="" value="planta de mezcla en frio">&gt;Planta de mezcla en frío</option>
</select>
</div>
<?php if (!empty($maquinaria['imagen'])): ?>
<label class="form-label form-label text-warning">Imagen actual:</label>
<img class="preview" src="imagenes/&lt;?= $maquinaria['imagen'] ?&gt;"/>
<?php endif; ?>
<div class="mb-3">
<label class="form-label form-label text-warning">Nueva imagen (opcional)</label>
<input accept="image/*" class="form-control form-control mb-3" name="imagen" type="file"/>
</div>
<div class="d-grid mb-2">
<button class="btn btn-primary btn btn-warning w-100 mt-3" type="submit">Guardar Cambios</button>
</div>
</form>
<a class="btn btn-regresar w-100 text-center" href="inventario.php">← Volver al Inventario</a>
</div>
<script>
    const tipo = document.getElementById("tipo");
    const subtipoContainer = document.getElementById("subtipo-container");

    function toggleSubtipo() {
      subtipoContainer.style.display = tipo.value === "nueva" ? "block" : "none";
    }

    tipo.addEventListener("change", toggleSubtipo);
    toggleSubtipo(); // al cargar
  </script>
</div></body>
</html>
