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
            max-width: 700px;
            margin: auto;
            margin-top: 40px;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .btn-primary {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="card-form">
    <h4 class="mb-4 text-center text-primary">✏️ Editar Maquinaria</h4>

    <form action="procesar_editar.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $maquinaria['id'] ?>">

        <div class="row g-3">
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

            <div class="col-md-12">
                <label>Cambiar Imagen:</label>
                <input type="file" name="imagen" class="form-control">
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
            <a href="inventario.php" class="btn btn-secondary ms-2">← Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>
