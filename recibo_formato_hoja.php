<?php
include 'conexion.php';
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID no proporcionado.";
    exit;
}
$maq = $conn->query("SELECT * FROM maquinaria WHERE id = $id")->fetch_assoc();
$recibo = $conn->query("SELECT * FROM recibo_unidad WHERE maquinaria_id = $id")->fetch_assoc();
if (!$maq || !$recibo) {
    echo "Datos no encontrados.";
    exit;
}
function imprimirCampo($campo) {
    global $recibo;
    return strtoupper($recibo[$campo] ?? '');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Unidad</title>
    <style>
        body { font-family: Arial; margin: 30px; }
        h4 { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { border: 1px solid #ccc; padding: 6px; }
        .btn-print {
            margin-bottom: 20px;
            padding: 8px 16px;
            background-color: green;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body>
<button class="btn-print" onclick="window.print()">🖨️ Imprimir</button>

<div style='text-align: center; margin-bottom: 20px;'>
    <img src="logo.png" alt="Logo" style="max-height: 80px;"><br>
    <h3 style='margin: 10px 0;'>RECIBO DE UNIDAD</h3>
</div>

<div><strong>Nombre:</strong> <?= $maq['nombre'] ?> - <strong>Condición:</strong> <?= $recibo['condicion_total'] ?>%</div>
<h4>MOTOR</h4>
<table><tbody>
<tr><td>CILINDROS</td><td><?= imprimirCampo('cilindros') ?></td><td>PISTONES</td><td><?= imprimirCampo('pistones') ?></td></tr>
</tbody></table><br>
<h4>SISTEMA MECÁNICO</h4>
<table><tbody>
<tr><td>TRANSMISIÓN</td><td><?= imprimirCampo('transmisión') ?></td><td>DIFERENCIALES</td><td><?= imprimirCampo('diferenciales') ?></td></tr>
<tr><td>CARDAN</td><td><?= imprimirCampo('cardan') ?></td><td colspan='2'></td></tr>
</tbody></table><br>
<h4>CONSUMIBLES</h4>
<table><tbody>
<tr><td>PUNTAS</td><td><?= imprimirCampo('puntas') ?></td><td>PORTA PUNTAS</td><td><?= imprimirCampo('porta_puntas') ?></td></tr>
</tbody></table><br>

</body>
</html>
