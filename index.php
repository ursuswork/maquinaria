<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Maquinaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .acciones i {
            cursor: pointer;
            margin: 0 5px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">Inventario de Maquinaria</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Modelo</th>
                <th>Ubicación</th>
                <th>Condición</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM maquinaria";
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="imagenes/<?= $row['imagen'] ?>" width="100"></td>
                    <td><?= $row['nombre'] ?></td>
                    <td><?= ucfirst($row['tipo']) ?></td>
                    <td><?= $row['modelo'] ?></td>
                    <td><?= $row['ubicacion'] ?></td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $row['condicion_estimada'] ?>%;">
                                <?= $row['condicion_estimada'] ?>%
                            </div>
                        </div>
                    </td>
                    <td class="acciones">
                        <a href="editar.php?id=<?= $row['id'] ?>"><i class="bi bi-pencil-square text-primary"></i></a>
                        <a href="eliminar.php?id=<?= $row['id'] ?>"><i class="bi bi-trash text-danger"></i></a>
                        <a href="recibo_formato_hoja.php?id=<?= $row['id'] ?>"><i class="bi bi-receipt-cutoff text-success"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="agregar.php" class="btn btn-success mt-3">Agregar Maquinaria</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
</body>
</html>

