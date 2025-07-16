<?php
include 'conexion.php';

$maquinaria_id = intval($_POST['maquinaria_id'] ?? 0);
$observaciones = trim($_POST['observaciones'] ?? '');
$componentes = $_POST['componentes'] ?? [];

if ($maquinaria_id <= 0 || empty($componentes)) {
    die("❌ Datos incompletos.");
}

// Ponderaciones por secciones
$pesos = [
    "MOTOR" => 15,
    "SISTEMA MECÁNICO" => 15,
    "SISTEMA HIDRÁULICO" => 30,
    "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => 25,
    "ESTÉTICO" => 5,
    "CONSUMIBLES" => 10
];

// Obtener componentes por sección desde estructura
$sql = "SELECT nombre, seccion FROM estructura_recibo_unidad";
$res = $conn->query($sql);
$estructura = [];
while ($row = $res->fetch_assoc()) {
    $estructura[$row['nombre']] = $row['seccion'];
}

// Agrupar calificaciones por sección
$puntajes = [];
$totales = [];

foreach ($componentes as $nombre => $valor) {
    $nombre = trim($nombre);
    $seccion = $estructura[$nombre] ?? 'OTROS';

    // Puntaje por valor
    $p = match ($valor) {
        "bueno" => 100,
        "regular" => 50,
        "malo" => 0,
        default => 0,
    };

    $puntajes[$seccion][] = $p;
}

// Calcular promedio ponderado
$condicion = 0;
foreach ($pesos as $seccion => $peso) {
    if (!isset($puntajes[$seccion])) continue;
    $promedio = array_sum($puntajes[$seccion]) / count($puntajes[$seccion]);
    $condicion += ($promedio * $peso) / 100;
}

$condicion_final = round($condicion);

// Guardar en tabla recibo_unidad
$stmt = $conn->prepare("INSERT INTO recibo_unidad (maquinaria_id, observaciones, condicion_calculada, fecha) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("isi", $maquinaria_id, $observaciones, $condicion_final);
$stmt->execute();

// Actualizar en tabla maquinaria
$conn->query("UPDATE maquinaria SET condicion_estimada = $condicion_final WHERE id = $maquinaria_id");

// Redirigir
header("Location: ../inventario.php?recibo=ok");
exit;
?>
