<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Maquinaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">Agregar Maquinaria</h2>
    <form action="guardar.php" method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre:</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Tipo:</label>
            <select name="tipo" class="form-select">
                <option value="nueva">Nueva</option>
                <option value="usada">Usada</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Modelo:</label>
            <input type="text" name="modelo" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Número de Serie:</label>
            <input type="text" name="numero_serie" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Marca:</label>
            <input type="text" name="marca" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Año:</label>
            <input type="number" name="anio" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Ubicación:</label>
            <input type="text" name="ubicacion" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Condición Estimada (%):</label>
            <input type="number" name="condicion_estimada" max="100" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Imagen:</label>
            <input type="file" name="imagen" class="form-control">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
