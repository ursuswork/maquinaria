<?php
include 'conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID no proporcionado.";
    exit;
}

// Obtener datos de maquinaria
$maq = $conn->query("SELECT * FROM maquinaria WHERE id = $id AND tipo = 'usada'")->fetch_assoc();
if (!$maq) {
    echo "Maquinaria no encontrada o no es usada.";
    exit;
}

// Verificar si ya existe recibo
$recibo = $conn->query("SELECT * FROM recibo_unidad WHERE maquinaria_id = $id")->fetch_assoc();
$editando = $recibo ? true : false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Unidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h3 class="mb-4">Recibo de Unidad para: <?= $maq['nombre'] ?> (<?= $maq['modelo'] ?>)</h3>
    <form method="post" action="guardar_recibo.php" class="row g-3">
        <input type="hidden" name="maquinaria_id" value="<?= $id ?>">
        <?php
        $campos = ['cilindros','pistones','anillos','inyectores','block','cabeza',
                   'transmision','diferenciales','cardan','alarmas','arneses',
                   'sistema_hidraulico','estetico','consumibles'];
        foreach ($campos as $campo):
            $valor = $recibo[$campo] ?? '';
        ?>
        <div class="col-md-4">
            <label class="form-label"><?= ucfirst(str_replace('_',' ', $campo)) ?>:</label>
            <select name="<?= $campo ?>" class="form-select" required>
                <option value="">Seleccione</option>
                <option value="bueno" <?= $valor=='bueno'?'selected':'' ?>>Bueno</option>
                <option value="regular" <?= $valor=='regular'?'selected':'' ?>>Regular</option>
                <option value="malo" <?= $valor=='malo'?'selected':'' ?>>Malo</option>
            </select>
        </div>
        <?php endforeach; ?>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Guardar Recibo</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>