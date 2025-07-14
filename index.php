<?php
include 'conexion.php';

$total_nueva = $conn->query("SELECT COUNT(*) AS total FROM maquinaria WHERE tipo = 'nueva'")->fetch_assoc()['total'];
$total_usada = $conn->query("SELECT COUNT(*) AS total FROM maquinaria WHERE tipo = 'usada'")->fetch_assoc()['total'];
$result = $conn->query("SELECT * FROM maquinaria ORDER BY fecha_registro DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Maquinaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .imagen-mini {
            width: 60px;
            height: auto;
        }
        .card-resumen {
            color: #fff;
        }
        .barra-condicion {
            width: 100%;
            height: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    
    <?php if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
            if ($_GET['mensaje'] == 'agregado') echo '✅ Maquinaria agregada con éxito.';
            elseif ($_GET['mensaje'] == 'actualizado') echo '✅ Maquinaria actualizada correctamente.';
            elseif ($_GET['mensaje'] == 'eliminado') echo '🗑️ Registro eliminado.';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php endif; ?>
    
<div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Inventario de Maquinaria</h2>
        <button id="btn-instalar" class="btn btn-success d-none" onclick="instalarApp()">Instalar App</button>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card bg-primary card-resumen">
                <div class="card-body">
                    <h5 class="card-title">Maquinaria Nueva</h5>
                    <h2><?= $total_nueva ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success card-resumen">
                <div class="card-body">
                    <h5 class="card-title">Maquinaria Usada</h5>
                    <h2><?= $total_usada ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between mb-3">
        <a href="agregar.php" class="btn btn-success">Agregar Maquinaria</a>
        <a href="exportar_excel.php" class="btn btn-outline-secondary">Exportar a Excel</a>
        <form method="get" class="d-flex" role="search">
            <input class="form-control me-2" type="search" name="buscar" placeholder="Buscar..." value="<?= $_GET['buscar'] ?? '' ?>">
            <button class="btn btn-outline-primary" type="submit">Buscar</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark text-center">
                <tr>
                    <th>Imagen</th><th>Nombre</th><th>Tipo</th><th>Modelo</th><th>Ubicación</th><th>Condición</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <?php
                if (!empty($_GET['buscar']) && stripos($row['nombre'].$row['modelo'].$row['numero_serie'], $_GET['buscar']) === false) continue;
                $condicion = (int)$row['condicion_estimada'];
                $color = $condicion >= 80 ? 'bg-success' : ($condicion >= 50 ? 'bg-warning' : 'bg-danger');
                ?>
                <tr class="text-center align-middle">
                    <td><img src="imagenes/<?= $row['imagen'] ?>" class="imagen-mini"></td>
                    <td><?= $row['nombre'] ?></td>
                    <td><?= ucfirst($row['tipo']) ?></td>
                    <td><?= $row['modelo'] ?></td>
                    <td><?= $row['ubicacion'] ?></td>
                    <td>
                        <div class="progress">
                          <div class="progress-bar <?= $color ?>" role="progressbar" style="width: <?= $condicion ?>%;" aria-valuenow="<?= $condicion ?>" aria-valuemin="0" aria-valuemax="100">
                            <?= $condicion ?>%
                          </div>
                        </div>
                    </td>
                    <td>
                        <a href="editar.php?id=<?= $row['id'] ?>" class="text-primary me-2">✏️</a>
                        <a href="eliminar.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar este registro?')" class="text-danger">🗑️</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'register_sw.html'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
