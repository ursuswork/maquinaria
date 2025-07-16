<?php
session_start();
include '../conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener componentes agrupados por sección
$componentes = [];
$query = "SELECT seccion, componente FROM estructura_recibo_unidad ORDER BY seccion, componente";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $componentes[$row['seccion']][] = $row['componente'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Unidad</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: sans-serif; background: #f9f9f9; margin: 20px; }
        .formulario { background: white; padding: 30px; border-radius: 10px; max-width: 900px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 30px; }
        .seccion { margin-bottom: 25px; }
        .seccion h4 { margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        .componente { display: flex; justify-content: space-between; margin-bottom: 10px; }
        select { width: 150px; }
        .btn { background: #007bff; color: white; padding: 10px 25px; border: none; border-radius: 5px; }
        textarea { width: 100%; height: 80px; }
        .print { margin-top: 20px; text-align: center; }
    </style>
</head>
<body>

<div class="formulario">
    <h2>🛠️ Formato de Recibo de Unidad</h2>
    <form method="POST" action="../procesar_recibo.php">
        <input type="hidden" name="id_maquinaria" value="<?= \$id ?>">

        <?php foreach ($componentes as $seccion => $lista): ?>
            <div class="seccion">
                <h4><?= htmlspecialchars(\$seccion) ?></h4>
                <?php foreach ($lista as $comp): ?>
                    <div class="componente">
                        <label><?= htmlspecialchars(\$comp) ?></label>
                        <select name="componente[<?= htmlspecialchars(\$seccion) ?>][<?= htmlspecialchars(\$comp) ?>]" required>
                            <option value="">-- Selecciona --</option>
                            <option value="bueno">Bueno</option>
                            <option value="regular">Regular</option>
                            <option value="malo">Malo</option>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div class="seccion">
            <h4>Observaciones</h4>
            <textarea name="observaciones" placeholder="Escribe cualquier nota técnica..."></textarea>
        </div>

        <div class="text-center">
            <button type="submit" class="btn">Guardar Evaluación</button>
        </div>
    </form>

    <div class="print">
        <button onclick="window.print()" class="btn" style="background:#28a745">🖨 Imprimir</button>
    </div>
</div>

</body>
</html>
