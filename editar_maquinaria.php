<?php
session_start();
include 'conexion.php';

if (!isset($_GET['id'])) {
    header("Location: inventario.php");
    exit();
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM maquinaria WHERE id = $id");

if ($result->num_rows !== 1) {
    echo "Maquinaria no encontrada.";
    exit();
}

$maquinaria = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Maquinaria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f2f6f9; }
        .card-form {
            max-width: 850px;
            margin: auto;
            margin-top: 40px;
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .form-title {
            font-size: 1.6rem;
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
        }
        .preview-img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            margin-top: 10px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="card-form">
    <div class="form-title text-primary">✏️ Editar Maquinaria</div>

    <form action="procesar_editar.php" method="POST" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="id" value="<?= $maquinaria['id'] ?>">

        <div class="col-md-6">
            <label>Nombre:</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($maquinaria['nombre']) ?>" required>
        </div>

        <div class="col-md-6">
            <label>Tipo:</label>
            <select name="tipo" class="form-select" required>
                <option value="nueva" <?= $maquinaria['tipo'] == 'nueva' ? 'selected' : '' ?>>Nueva</option>
                <option value="usada" <?= $maquinaria['tipo'] == 'usada' ? 'selected' : '' ?>>Usada</option>
            </select>
        </div>

        <div class="col-md-6">
            <label>Modelo:</label>
            <input type="text" name="modelo" class="form-control" value="<?= htmlspecialchars($maquinaria['modelo']) ?>">
        </div>

        <div class="col-md-6">
            <label>Número de Serie:</label>
            <input type="text" name="numero_serie" class="form-control" value="<?= htmlspecialchars($maquinaria['numero_serie']) ?>">
        </div>

        <div class="col-md-6">
            <label>Marca:</label>
            <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($maquinaria['marca']) ?>">
        </div>

        <div class="col-md-6">
            <label>Año:</label>
            <input type="number" name="anio" class="form-control" value="<?= htmlspecialchars($maquinaria['anio']) ?>">
        </div>

        <div class="col-md-6">
            <label>Ubicación:</label>
            <input type="text" name="ubicacion" class="form-control" value="<?= htmlspecialchars($maquinaria['ubicacion']) ?>">
        </div>

        <div class="col-md-6">
            <label>Condición estimada (%):</label>
            <input type="number" name="condicion_estimada" class="form-control" value="<?= htmlspecialchars($maquinaria['condicion_estimada']) ?>" min="0" max="100">
        </div>

        <div class="col-md-6">
            <label>Imagen actual:</label><br>
            <?php if (!empty($maquinaria['imagen'])): ?>
                <img src="imagenes/<?= htmlspecialchars($maquinaria['imagen']) ?>" class="preview-img">
            <?php else: ?>
                <p class="text-muted">No hay imagen</p>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <label>Nueva imagen (opcional):</label>
            <input type="file" name="imagen" class="form-control" accept="image/*" onchange="previewNueva(event)">
            <img id="preview" class="preview-img" style="display:none;">
        </div>

        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-primary px-5">Guardar Cambios</button>
        </div>
    </form>
</div>

<script>
    function previewNueva(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('preview');
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = "block";
        }
    }
</script>

</body>
</html>
