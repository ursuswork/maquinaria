<?php
session_start();
include '../conexion.php';

function convertir_valor($valor) {
    return match ($valor) {
        'bueno' => 100,
        'regular' => 70,
        'malo' => 40,
        default => 0
    };
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_maquinaria   = intval($_POST['id_maquinaria']);
    $empresa_origen  = $_POST['empresa_origen'] ?? '';
    $empresa_destino = $_POST['empresa_destino'] ?? '';
    $observaciones   = $_POST['observaciones'] ?? '';
    $componentes     = $_POST['componentes'] ?? [];

    // Secciones y pesos
    $pesos = [
        'MOTOR' => 15,
        'SISTEMA MECÁNICO' => 15,
        'SISTEMA HIDRÁULICO' => 30,
        'SISTEMA ELÉCTRICO Y ELECTRÓNICO' => 25,
        'ESTÉTICO' => 5,
        'CONSUMIBLES' => 10
    ];

    $secciones = [
        'MOTOR' => ["Cilindros", "Pistones", "Anillos", "Inyectores", "Árbol de levas", "Balancines", "Bielas", "Block", "Culata", "Válvulas", "Turbo", "Múltiple de escape", "Radiador", "Termostato", "Bomba de agua", "Bomba de aceite", "Cárter", "Filtro de aceite", "Sensor de oxígeno", "Computadora", "Chicotes", "Arrancador", "Alternador", "Fajas", "Poleas", "Tapa de punterías", "Ventilador", "Soportes de motor", "Depósito de refrigerante", "Sensor de temperatura"],
        'SISTEMA MECÁNICO' => ["Transmisión", "Diferenciales", "Cardán"],
        'SISTEMA HIDRÁULICO' => ["Bombas hidráulicas", "Cilindros", "Válvulas", "Mangueras"],
        'SISTEMA ELÉCTRICO Y ELECTRÓNICO' => ["Luces", "Tablero", "Sensores", "Fusibles"],
        'ESTÉTICO' => ["Pintura", "Cabina", "Cristales", "Asientos"],
        'CONSUMIBLES' => ["Aceite motor", "Filtro de aire", "Filtro combustible", "Filtro hidráulico"]
    ];

    $total = 0;

    foreach ($secciones as $seccion => $lista) {
        $sum = 0;
        $count = 0;
        foreach ($lista as $componente) {
            if (isset($componentes[$componente])) {
                $sum += convertir_valor($componentes[$componente]);
                $count++;
            }
        }
        if ($count > 0) {
            $promedio = $sum / $count;
            $total += $promedio * ($pesos[$seccion] / 100);
        }
    }

    $condicion = round($total);

    // Validar si ya existe
    $check = $conn->query("SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1");
    if ($check->num_rows > 0) {
        // UPDATE si existe
        $update = $conn->prepare("UPDATE recibo_unidad SET empresa_origen=?, empresa_destino=?, fecha=NOW(), observaciones=?, condicion_estimada=? WHERE id_maquinaria=?");
        $update->bind_param("sssii", $empresa_origen, $empresa_destino, $observaciones, $condicion, $id_maquinaria);
        $update->execute();
    } else {
        // INSERT si no existe
        $insert = $conn->prepare("INSERT INTO recibo_unidad (id_maquinaria, empresa_origen, empresa_destino, fecha, observaciones, condicion_estimada) VALUES (?, ?, ?, NOW(), ?, ?)");
        $insert->bind_param("isssi", $id_maquinaria, $empresa_origen, $empresa_destino, $observaciones, $condicion);
        $insert->execute();
    }

    // Actualizar en tabla maquinaria
    $conn->query("UPDATE maquinaria SET condicion_estimada = $condicion WHERE id = $id_maquinaria");

    header("Location: ../inventario.php?guardado=1");
    exit;
}
?>
