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
            background: #e6f0ff; /* Azul claro */
        }
        .navbar {
            background-color: #001f3f; /* Azul marino */
        }
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        .card-maquina {
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }
        .card-maquina:hover {
            transform: scale(1.02);
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
        .header-actions a {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav class="navbar navbar-expand-lg px-3">
    <a class="navbar-brand" href="#">📋 Inventario</a>
    <div class="ms-auto">
        <a href="logout.php" class="btn btn-outline-light">🔓 Cerrar sesión</a>
    </div>
</nav>

<!-- CONTENIDO -->
<div class="container py-4">

    <?php if (isset($_GET['editado']) && $_GET['editado'] == 'ok'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ Maquinaria editada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['eliminado']) && $_GET['eliminado'] == 'ok'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            🗑️ Maquinaria eliminada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-column flex-md-row text-center text-md-start">
        <h3 class="mb-3 mb-md-0 text-primary">Maquinaria Registrada</h3>
        <div class="header-actions d-flex gap-2">
            <a href="agregar_maquinaria.php" class="btn btn-primary">➕ Agregar Maquinaria</a>
            <a href="exportar_excel.php" class="btn btn-warning text-dark">📤 Exportar a Excel</a>
        </div>
    </div>

    <form method="GET" class="mb-4">
        <div class="input-group search-bar shadow-sm">
            <input type="text" name="busqueda" class="form-control" placeholder="🔎 Buscar por nombre, modelo o serie..." value="<?= htmlspecialchars($busqueda) ?>">
            <button class="btn btn-primary" type="submit">Buscar</button>
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

                        <h5 class="text-primary"><?= htmlspecialchars($row['nombre']) ?>
                            <small class="text-muted">(<?= htmlspecialchars($row['tipo']) ?>)</small>
                        </h5>
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

                        <div class="d-flex justify-content-between">
                            <a href="editar_maquinaria.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                                ✏️ Editar
                            </a>
                            <a href="eliminar_maquinaria.php?id=<?= $row['id'] ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('¿Estás seguro de eliminar esta maquinaria?');">
                                🗑️ Eliminar
                            </a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
