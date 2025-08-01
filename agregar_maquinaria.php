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
} elseif ($rol === 'camiones') {
  $tipos_permitidos = ['camion'];
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
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<style>
    body {
        background-color: #002b5c;
        color: #ffffff;
        font-family: 'Segoe UI', sans-serif;
        min-height: 100vh;
    }
    .container-ficha {
        background-color: #012a5c;
        padding: 2.5rem 2rem 2rem 2rem;
        border-radius: 1.4rem;
        box-shadow: 0 0 20px #0005;
        max-width: 950px;
        margin: 32px auto 0 auto;
        border-left: 6px solid #ffc107;
        color: #fff;
    }
    h3, h4, h5 {
        color: #ffc107;
        border-bottom: 2px solid #ffc10710;
        padding-bottom: .5rem;
        margin-bottom: 1.5rem;
        font-weight: bold;
        text-align: center;
    }
    .form-label { color: #ffc107; font-weight: 600; }
    .form-control, .form-select {
        margin-bottom: 1rem;
        background-color: #003366;
        color: white;
        border: 1px solid #0059b3;
        border-radius: .5rem;
    }
    .form-control:focus, .form-select:focus {
        background-color: #02436a;
        color: #fff;
        border: 1.5px solid #ffc107;
    }
    .btn-warning, .btn-success, .btn-primary {
        font-weight: bold;
        border: none;
        border-radius: .6rem;
    }
    .btn-outline-info {
        border-radius: .6rem;
        font-weight: bold;
    }
    .d-grid .btn {
        font-size: 1.1rem;
    }
    @media (max-width: 600px) {
        .container-ficha { padding: 1.3rem 0.5rem 1.2rem 0.5rem; }
    }
</style>
</head>
<body>
<div class="container-ficha">
<div class="contenedor-formulario">
<h4 class="text-center mb-4 text-warning">Agregar Maquinaria</h4>
<form action="procesar_agregar.php" enctype="multipart/form-data" method="POST" id="formMaquinaria">
<div class="row">
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label text-warning">Nombre</label>
      <input class="form-control" name="nombre" required="" type="text"/>
    </div>
    <div class="mb-3">
      <label class="form-label text-warning">Marca</label>
      <input class="form-control" name="marca" required="" type="text"/>
    </div>
    <div class="mb-3">
      <label class="form-label text-warning">Modelo</label>
      <input class="form-control" name="modelo" required="" type="text"/>
    </div>
    <div class="mb-3">
      <label class="form-label text-warning">Año</label>
      <input class="form-control" name="anio" type="number" min="1950" max="<?= date('Y')+1 ?>" placeholder="Ejemplo: 2024"/>
    </div>
  </div>
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label text-warning">Número de serie</label>
      <input class="form-control" name="numero_serie" required="" type="text"/>
    </div>
    <div class="mb-3">
      <label class="form-label text-warning">Ubicación</label>
      <input class="form-control" name="ubicacion" required="" type="text"/>
    </div>
    <div class="mb-3">
      <label class="form-label text-warning">Imagen</label>
      <input accept="image/*" class="form-control" name="imagen" type="file"/>
    </div>
  </div>
</div>
<div class="mb-3">
  <label class="form-label text-warning">Tipo</label>
  <select class="form-select" id="tipo_maquinaria" name="tipo_maquinaria" onchange="mostrarOpcionesPorTipo()" required>
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
  <select class="form-select" name="subtipo" id="subtipo_select" onchange="mostrarCapacidad()">
    <option value="">Seleccionar</option>
    <option value="Petrolizadora">Petrolizadora</option>
    <option value="Esparcidor de sello">Esparcidor de sello</option>
    <option value="Bachadora">Bachadora</option>
    <option value="Tanque de almacén">Tanque de almacén</option>
    <option value="Planta de mezcla en frío">Planta de mezcla en frío</option>
  </select>
</div>
<div class="mb-3" id="capacidad_contenedor" style="display:none;">
  <label class="form-label text-warning">Capacidad</label>
  <select class="form-select" name="capacidad" id="capacidad_select"></select>
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
function mostrarOpcionesPorTipo() {
    const tipo = document.getElementById('tipo_maquinaria').value;
    const subtipoCont = document.getElementById('subtipo_contenedor');
    const subtipoSel = document.getElementById('subtipo_select');
    const capacidadCont = document.getElementById('capacidad_contenedor');
    subtipoCont.style.display = (tipo === 'nueva') ? 'block' : 'none';
    capacidadCont.style.display = 'none';
    subtipoSel.value = "";
    if(tipo !== 'nueva') {
        document.getElementById('capacidad_select').innerHTML = "";
    }
}

function mostrarCapacidad() {
    const subtipo = document.getElementById('subtipo_select').value.toLowerCase();
    const capacidadCont = document.getElementById('capacidad_contenedor');
    const capacidadSel = document.getElementById('capacidad_select');
    let options = "";

    if(subtipo === "petrolizadora") {
        options += "<option value='6000 Lts'>6,000 Lts</option>";
        options += "<option value='8000 Lts'>8,000 Lts</option>";
        options += "<option value='10000 Lts'>10,000 Lts</option>";
        options += "<option value='12000 Lts'>12,000 Lts</option>";
        options += "<option value='15000 Lts'>15,000 Lts</option>";
        options += "<option value='18000 Lts'>18,000 Lts</option>";
        options += "<option value='20000 Lts'>20,000 Lts</option>";
        capacidadCont.style.display = "block";
    } else if(subtipo === "bachadora") {
        options += "<option value='1000 Lts'>1,000 Lts</option>";
        options += "<option value='2000 Lts'>2,000 Lts</option>";
        capacidadCont.style.display = "block";
    } else if(subtipo === "tanque de almacén") {
        options += "<option value='40,000 Lts'>40,000 Lts</option>";
        options += "<option value='60,000 Lts'>60,000 Lts</option>";
        options += "<option value='80,000 Lts'>80,000 Lts</option>";
        capacidadCont.style.display = "block";
    } else if(subtipo === "planta de mezcla en frío") {
        options += "<option value='70 Ton'>70 toneladas</option>";
        options += "<option value='150 Ton'>150 toneladas</option>";
        capacidadCont.style.display = "block";
    } else {
        capacidadCont.style.display = "none";
        options = "";
    }
    capacidadSel.innerHTML = options;
}
</script>
</div>
</body>
</html>
