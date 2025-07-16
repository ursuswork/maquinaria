<?php
session_start();
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../inventario.php");
    exit();
}

function convertir_valor($estado) {
    return match ($estado) {
        'bueno'   => 100,
        'regular' => 70,
        'malo'    => 40,
        default   => 0
    };
}

// Pesos por sección
$pesos = [
    "MOTOR" => 30,
    "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => 25,
    "SISTEMA HIDRÁULICO" => 30,
    "ESTÉTICO" => 5,
    "CONSUMIBLES" => 10
];

$id_maquinaria = intval($_POST['id_maquinaria'] ?? 0);
$componentes = $_POST['componente'] ?? [];
$observaciones = $_POST['observaciones'] ?? '';

if ($id_maquinaria <= 0 || empty($componentes)) {
    die("❌ Datos inválidos.");
}

$condicion_total = 0;

foreach ($pesos as $seccion => $peso) {
    if (!isset($componentes[$seccion])) continue;

    $items = $componentes[$seccion];
    $suma = 0;
    $total = count($items);

    foreach ($items as $estado) {
        $suma += convertir_valor($estado);
    }

    $promedio_seccion = $suma / $total;
    $condicion_total += $promedio_seccion * ($peso / 100);
}

// Redondear condición final
$condicion_final = round($condicion_total);

// ✅ GUARDAR EN BASE DE DATOS

// 1. Guardar en tabla recibo_unidad (opcional)
foreach ($componentes as $seccion => $items) {
    foreach ($items as $componente => $estado) {
        $stmt = $conn->prepare("INSERT INTO recibo_unidad (id_maquinaria, seccion, componente, estado, observaciones) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $id_maquinaria, $seccion, $componente, $estado, $observaciones);
        $stmt->execute();
    }
}

// 2. Actualizar condición en tabla maquinaria
$stmt = $conn->prepare("UPDATE maquinaria SET condicion_estimada = ? WHERE id = ?");
$stmt->bind_param("ii", $condicion_final, $id_maquinaria);
$stmt->execute();

// Redirigir
header("Location: ../inventario.php?editado=ok");
exit();
?>
