<?php
include 'conexion.php';
$id = $_GET['id'];
$data = $conn->query("SELECT * FROM maquinaria WHERE id=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<html>
<head>
    <title>Editar Maquinaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">Editar Maquinaria</h2>
    <form class="row g-3" action="actualizar.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id ?>">
        <label>Nombre:</label><input type="text" name="nombre" value="<?= $data['nombre'] ?>" required>
        <label>Tipo:</label>
        <select name="tipo">
            <option value="nueva" <?= $data['tipo']=='nueva'?'selected':'' ?>>Nueva</option>
            <option value="usada" <?= $data['tipo']=='usada'?'selected':'' ?>>Usada</option>
        </select>
        <label>Modelo:</label><input type="text" name="modelo" value="<?= $data['modelo'] ?>">
        <label>Número de Serie:</label><input type="text" name="numero_serie" value="<?= $data['numero_serie'] ?>">
        <label>Marca:</label><input type="text" name="marca" value="<?= $data['marca'] ?>">
        <label>Año:</label><input type="number" name="anio" value="<?= $data['anio'] ?>">
        <label>Ubicación:</label><input type="text" name="ubicacion" value="<?= $data['ubicacion'] ?>">
        <label>Condición Estimada (%):</label><input type="number" name="condicion_estimada" value="<?= $data['condicion_estimada'] ?>">
        <label>Imagen nueva:</label><input type="file" name="imagen">
        <div class="col-12"><button type="submit" class="btn btn-primary">Actualizar</button> <a href="index.php" class="btn btn-secondary">Cancelar</a></div>
    </form>
</body>
</html>