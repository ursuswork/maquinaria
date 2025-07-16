<?php
session_start();
include 'conexion.php';

// Búsqueda
$buscar = $_GET['buscar'] ?? '';
$condicion = $conn->real_escape_string($buscar);

$sql = "SELECT * FROM maquinaria WHERE 
        nombre LIKE '%$condicion%' OR 
        modelo LIKE '%$condicion%' OR 
        tipo LIKE '%$condicion%' OR 
        ubicacion LIKE '%$condicion%'
        ORDER BY id DESC";
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
        body { background: #f1f5f9; padding: 20px; }
        .card-maquinaria { border-radius: 15px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        .card-img { height: 160px; object-fit: cover; border-radius: 10px; }
        .barra-condicion { height: 15px; border-radius: 10px; }
        .btn-flotante {
            position: fixed; bottom: 30px; right: 30px;
            background: #28a745; color: white;
            padding: 15px 25px; border-radius: 50px;
            font-size: 18px; text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4 text-center">📦 Inventario de Maquinaria</h2>

    <form method="GET" class="mb-4 text-center">
        <input type="text" name="buscar" class="form-control w-50 d-inline" placeholder="Buscar maquinaria..." value="<?= htmlspecialchars($buscar) ?>">
        <button class="btn btn-primary ms-2">Buscar</button>
    </form>

    <div class="row g-4">
        <?php while ($row = $resultado->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card card-maquinaria p-3">
                    <?php if ($row['imagen']): ?>
                        <img src="imagenes/<?= $row['imagen'] ?>" class="card-img mb-2" alt="Imagen">
                    <?php else: ?>
                        <div class="bg-secondary text-white text-center py-5 rounded">Sin imagen</div>
                    <?php endif; ?>

                    <h5><?= htmlspecialchars($row['nombre']) ?> (<?= htmlspecialchars($row['tipo']) ?>)</h5>
                    <p class="mb-1"><strong>Modelo:</strong> <?= htmlspecialchars($row['modelo']) ?></p>
                    <p class="mb-1"><strong>Ubicación:</strong> <?= htmlspecialchars($row['ubicacion']) ?></p>
                    <div class="mb-2">
                        <small>Condición:</small>
                        <div class="progress barra-condicion">
                            <div class="progress-bar bg-success" style="width: <?= intval($row['condicion_estimada']) ?>%">
                                <?= intval($row['condicion_estimada']) ?>%
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="editar_maquinaria.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">✏️ Editar</a>
                        <a href="eliminar_maquinaria.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar esta maquinaria?')" class="btn btn-outline-danger btn-sm">🗑 Eliminar</a>
                        <?php if ($row['tipo'] == 'usada'): ?>
                            <a href="acciones/recibo_unidad.php?id=<?= $row['id'] ?>" class="btn btn-outline-dark btn-sm">📄 Recibo</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<a href="agregar_maquinaria.php" class="btn-flotante">➕</a>

</body>
</html>
