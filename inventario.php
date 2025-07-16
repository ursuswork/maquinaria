<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: index.php");
    exit();
}
include 'conexion.php';
$result = $conn->query("SELECT * FROM maquinaria");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Maquinaria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
            padding: 20px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .table img {
            max-width: 100px;
            height: auto;
        }
        .barra-condicion {
            height: 20px;
            border-radius: 10px;
            background-color: #e0e0e0;
            overflow: hidden;
        }
        .barra-condicion-inner {
            height: 100%;
            text-align: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
            line-height: 20px;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <a href="agregar_maquinaria.php" class="btn btn-success">➕ Agregar Maquinaria</a>
    <a href="exportar_excel.php" class="btn btn-secondary">📥 Exportar a Excel</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
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
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td>
                    <?php if (!empty($row['imagen'])): ?>
                        <img src="<?= htmlspecialchars($row['imagen']) ?>" alt="Imagen">
                    <?php else: ?>
                        Sin imagen
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['tipo']) ?></td>
                <td><?= htmlspecialchars($row['modelo']) ?></td>
                <td><?= htmlspecialchars($row['ubicacion']) ?></td>
                <td>
                    <div class="barra-condicion">
                        <div class="barra-condicion-inner bg-success" style="width: <?= intval($row['condicion_estimada']) ?>%;">
                            <?= intval($row['condicion_estimada']) ?>%
                        </div>
                    </div>
                </td>
                <td>
                    <a href="editar_maquinaria.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">✏️</a>
                    <a href="eliminar_maquinaria.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar esta maquinaria?')">🗑️</a>
                    <?php if ($row['tipo'] === 'usada') : ?>
                        <a href="acciones/recibo_unidad.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-dark">📄 Recibo</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
