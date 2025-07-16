<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Maquinaria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f6fa;
        }
        .card-form {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .form-title {
            font-size: 1.6rem;
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
            color: #333;
        }
        .form-control, .form-select {
            border-radius: 10px;
        }
        .btn-success {
            border-radius: 10px;
            padding: 10px 30px;
            font-weight: bold;
        }
        label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card-form">
        <div class="form-title">➕ Agregar Nueva Maquinaria</div>

        <form action="procesar_agregar.php" method="POST" enctype="multipart/form-data" class="row g-3">

            <div class="col-md-6">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" name="nombre" id="nombre" required>
            </div>

            <div class="col-md-6">
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo" class="form-select" required>
                    <option value="">-- Selecciona --</option>
                    <option value="nueva">Nueva</option>
                    <option value="usada">Usada</option>
                </select>
            </div>

            <div class="col-md-6">
                <label for="modelo">Modelo:</label>
                <input type="text" class="form-control" name="modelo" id="modelo">
            </div>

            <div class="col-md-6">
                <label for="numero_serie">Número de Serie:</label>
                <input type="text" class="form-control" name="numero_serie" id="numero_serie">
            </div>

            <div class="col-md-6">
                <label for="marca">Marca:</label>
                <input type="text" class="form-control" name="marca" id="marca">
            </div>

            <div class="col-md-6">
                <label for="anio">Año:</label>
                <input type="number" class="form-control" name="anio" id="anio" min="1900" max="2099">
            </div>

            <div class="col-md-6">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" class="form-control" name="ubicacion" id="ubicacion">
            </div>

            <div class="col-md-6">
                <label for="condicion_estimada">Condición estimada (%):</label>
                <input type="number" class="form-control" name="condicion_estimada" id="condicion_estimada" min="0" max="100">
            </div>

            <div class="col-md-12">
                <label for="imagen">Imagen:</label>
                <input type="file" class="form-control" name="imagen" id="imagen" accept="image/*">
            </div>

            <div class="col-12 text-center">
                <button type="submit" class="btn btn-success mt-3">💾 Guardar Maquinaria</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
