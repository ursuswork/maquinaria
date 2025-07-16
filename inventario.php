<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}

include 'conexion.php';

$busqueda = $_GET['busqueda'] ?? '';
$sql = "SELECT * FROM maquinaria";

if (!empty($busqueda)) {
    $busqueda = $conn->real_escape_string($busqueda);
    $sql .= " WHERE nombre LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%' OR numero_serie LIKE '%$busqueda%'";
}

$sql .= " ORDER BY condicion_estimada DESC";
$resultado = $conn->query($sql);
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
            background: #f4f6f9;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .card-maquina {
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: 0.2s;
        }
        .card-maquina:hover {
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        .img-thumb {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 15px;
        }
        .condicion-bar {
            height: 15px;
            border-radius: 10px;
        }
        .search-bar input {
            border-radius: 10px;
        }
        .btn-outline-warning {
            border-color: #ffc107;
            color: #ffc107;
        }
        .btn-outline-warning:hover {
            background-color: #ffc107;
            color: white;
        }
    </style>
</head>
<body>

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
    <a class="navbar-brand" href="#">📋 Inventario</a>
    <div class="ms-auto">
        <a href="logout.php" class="btn btn-outline-light">
            🔓 Cerrar sesión
        </a>
    </div>
</nav>

<div class="container py-4">
    <h3 class="mb-4 text-center text-primary">Maquinaria Registrada</h3>

    <form method="GET" class="mb-4">
        <div class="input-group search-bar">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, modelo o serie..." value="<?= htmlspecialchars($busqueda) ?>">
            <button class="btn btn-primary" type="submit">🔍 Buscar</button>
        </div>
    </form>

    <div class="row g-4">
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <?php while ($row = $resultado->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card card-maquina p-3">
                        <?php if (!empty($row['imagen'])): ?>
                            <img src="imagenes/<?= htmlspecialchars($row['imagen']) ?>" class="img-thumb mb-3">
                        <?php else: ?>
                            <div class="text-center text-muted mb-3">📷 Sin imagen</div>
                        <?php endif; ?>

                        <h5><?= htmlspecialchars($row['nombre']) ?> <small class="text-muted">(<?= htmlspecialchars($row['tipo']) ?>)</small></h5>
                        <p class="mb-1"><strong>Modelo:</strong> <?= htmlspecialchars($row['modelo']) ?></p>
                        <p class="mb-1"><strong>Ubicación:</strong> <?= htmlspecialchars($row['ubicacion']) ?></p>
                        <p class="mb-1"><strong>Condición:</strong> <?= htmlspecialchars($row['condicion_estimada']) ?>%</p>

                        <div class="progress condicion-bar mb-3">
                            <div class="progress-bar 
                                <?php
                                    $cond = (int)$row['condicion_estimada'];
                                    if ($cond >= 80) echo 'bg-success';
                                    elseif ($cond >= 50) echo 'bg-warning';
                                    else echo 'bg-danger';
                                ?>"

                                role="progressbar" 
                                style="width: <?= $cond ?>%;" 
                                aria-valuenow="<?= $cond ?>" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between flex-wrap gap-1">
                            <a href="editar_maquinaria.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">✏️ Editar</a>
                            <a href="eliminar_maquinaria.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta maquinaria?');">🗑️ Eliminar</a>

                            <?php if ($row['tipo'] === 'usada'): ?>
                                <a href="acciones/recibo_unidad.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning">📄 Recibo</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted">
                No se encontraron resultados.
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
