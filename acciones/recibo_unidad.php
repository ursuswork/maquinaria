<?php
include '../conexion.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    die("ID inválido.");
}

$consulta = $conn->query("SELECT * FROM maquinaria WHERE id = $id");
$maquinaria = $consulta->fetch_assoc();

if (!$maquinaria) {
    die("Maquinaria no encontrada.");
}

$estructura = $conn->query("SELECT * FROM estructura_recibo_unidad ORDER BY seccion, id");
$componentes = [];
while ($row = $estructura->fetch_assoc()) {
    $componentes[$row['seccion']][] = $row['nombre'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Unidad</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f3f4f6; padding: 40px; }
        .hoja {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .titulo { font-size: 1.6rem; margin-bottom: 25px; font-weight: bold; }
        .seccion { margin-top: 25px; margin-bottom: 10px; font-weight: bold; color: #1f2937; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        select.form-select { min-width: 120px; }
        .form-observaciones { margin-top: 25px; }
    </style>
</head>
<body>

<div class="hoja">
    <div class="titulo text-center">📄 Recibo de Unidad - <?= htmlspecialchars($maquinaria['nombre']) ?></div>

    <form action="../guardar_recibo.php" method="POST">
        <input type="hidden" name="maquinaria_id" value="<?= $maquinaria['id'] ?>">

        <?php foreach ($componentes as $seccion => $items): ?>
            <div class="seccion"><?= htmlspecialchars($seccion) ?></div>
            <div class="row">
                <?php foreach ($items as $nombre): ?>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?= htmlspecialchars($nombre) ?></label>
                        <select class="form-select" name="componentes[<?= htmlspecialchars($nombre) ?>]" required>
                            <option value="bueno">Bueno</option>
                            <option value="regular">Regular</option>
                            <option value="malo">Malo</option>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div class="form-observaciones">
            <label class="form-label">Observaciones:</label>
            <textarea name="observaciones" class="form-control" rows="4"></textarea>
        </div>

        <div class="text-center mt-4">
            <button class="btn btn-primary px-4">Guardar Recibo</button>
        </div>
    </form>
</div>

</body>
</html>
