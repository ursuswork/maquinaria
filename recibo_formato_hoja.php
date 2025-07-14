<?php
include 'conexion.php';
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID no proporcionado.";
    exit;
}

// Obtener datos de maquinaria y recibo
$maq = $conn->query("SELECT * FROM maquinaria WHERE id = $id")->fetch_assoc();
$recibo = $conn->query("SELECT * FROM recibo_unidad WHERE maquinaria_id = $id")->fetch_assoc();

if (!$maq || !$recibo) {
    echo "Datos no encontrados.";
    exit;
}
function imprimirCampo($campo) {
    global $recibo;
    $v = $recibo[$campo];
    return strtoupper($v);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Unidad - Formato</title>
    <style>
        body {{
            font-family: Arial, sans-serif;
            padding: 30px;
        }}
        .titulo {{
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
        }}
        .info {{
            margin-bottom: 20px;
        }}
        .info label {{
            font-weight: bold;
        }}
        table {{
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }}
        th, td {{
            border: 1px solid #444;
            padding: 8px;
            text-align: center;
        }}
        th {{
            background-color: #f0f0f0;
        }}
        .btn-print {{
            display: inline-block;
            padding: 8px 16px;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }}
        @media print {{
            .btn-print {{
                display: none;
            }}
        }}
    </style>
</head>
<body>

<div class="titulo">RECIBO DE UNIDAD</div>

<div class="info">
    <p><label>Nombre:</label> <?= $maq['nombre'] ?></p>
    <p><label>Modelo:</label> <?= $maq['modelo'] ?> | <label>Número de Serie:</label> <?= $maq['numero_serie'] ?></p>
    <p><label>Ubicación:</label> <?= $maq['ubicacion'] ?> | <label>Condición estimada:</label> <?= $maq['condicion_estimada'] ?>%</p>
</div>

<button class="btn-print" onclick="window.print()">🖨️ Imprimir</button>

<table>
    <tr><th colspan="4">MOTOR</th></tr>
    <tr>
        <td>Cilindros</td><td><?= imprimirCampo('cilindros') ?></td>
        <td>Pistones</td><td><?= imprimirCampo('pistones') ?></td>
    </tr>
    <tr>
        <td>Anillos</td><td><?= imprimirCampo('anillos') ?></td>
        <td>Inyectores</td><td><?= imprimirCampo('inyectores') ?></td>
    </tr>
    <tr>
        <td>Block</td><td><?= imprimirCampo('block') ?></td>
        <td>Cabeza</td><td><?= imprimirCampo('cabeza') ?></td>
    </tr>

    <tr><th colspan="4">SISTEMA MECÁNICO</th></tr>
    <tr>
        <td>Transmisión</td><td><?= imprimirCampo('transmision') ?></td>
        <td>Diferenciales</td><td><?= imprimirCampo('diferenciales') ?></td>
    </tr>
    <tr>
        <td>Cardán</td><td colspan="3"><?= imprimirCampo('cardan') ?></td>
    </tr>

    <tr><th colspan="4">SISTEMA ELÉCTRICO Y ELECTRÓNICO</th></tr>
    <tr>
        <td>Alarmas</td><td><?= imprimirCampo('alarmas') ?></td>
        <td>Arneses</td><td><?= imprimirCampo('arneses') ?></td>
    </tr>

    <tr><th colspan="4">SISTEMA HIDRÁULICO</th></tr>
    <tr>
        <td colspan="4"><?= imprimirCampo('sistema_hidraulico') ?></td>
    </tr>

    <tr><th colspan="4">ESTÉTICO</th></tr>
    <tr>
        <td colspan="4"><?= imprimirCampo('estetico') ?></td>
    </tr>

    <tr><th colspan="4">CONSUMIBLES</th></tr>
    <tr>
        <td colspan="4"><?= imprimirCampo('consumibles') ?></td>
    </tr>
</table>

<div><strong>Condición total estimada: <?= $recibo['condicion_total'] ?>%</strong></div>

</body>
</html>