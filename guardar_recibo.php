<?php
session_start();
include '../conexion.php';

function convertir_valor($valor) {
    return match (strtolower($valor)) {
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
        'MOTOR' => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "ARBOL DE LEVAS", "BALANCINES", "BIELAS", "BLOCK", "CULATA", "VALVULAS", "TURBO", "MULTIPLE DE ESCAPE", "RADIADOR", "TERMOSTATO", "BOMBA DE AGUA", "BOMBA DE ACEITE", "CARTER", "FILTRO DE ACEITE", "SENSOR DE OXIGENO", "COMPUTADORA", "CHICOTES", "ARRANCADOR", "ALTERNADOR", "FAJAS", "POLEAS", "TAPA DE PUNTERIAS", "VENTILADOR", "SOPORTES DE MOTOR", "DEPOSITO DE REFRIGERANTE", "SENSOR DE TEMPERATURA"],
        'SISTEMA MECÁNICO' => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
        'SISTEMA HIDRÁULICO' => ["BOMBAS HIDRÁULICAS", "CILINDROS", "VÁLVULAS", "MANGUERAS"],
        'SISTEMA ELÉCTRICO Y ELECTRÓNICO' => ["LUCES", "TABLERO", "SENSORES", "FUSIBLES"],
        'ESTÉTICO' => ["PINTURA", "CABINA", "CRISTALES", "ASIENTOS"],
        'CONSUMIBLES' => ["ACEITE MOTOR", "FILTRO DE AIRE", "FILTRO COMBUSTIBLE", "FILTRO HIDRÁULICO"]
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

    // Asegurar que las columnas existan en la tabla
    $result = $conn->query("SHOW COLUMNS FROM recibo_unidad LIKE 'empresa_origen'");
    if ($result->num_rows === 0) {
        $conn->query("ALTER TABLE recibo_unidad ADD COLUMN empresa_origen VARCHAR(255) DEFAULT ''");
    }
    $result = $conn->query("SHOW COLUMNS FROM recibo_unidad LIKE 'empresa_destino'");
    if ($result->num_rows === 0) {
        $conn->query("ALTER TABLE recibo_unidad ADD COLUMN empresa_destino VARCHAR(255) DEFAULT ''");
    }
    $result = $conn->query("SHOW COLUMNS FROM recibo_unidad LIKE 'condicion_estimada'");
    if ($result->num_rows === 0) {
        $conn->query("ALTER TABLE recibo_unidad ADD COLUMN condicion_estimada INT DEFAULT 0");
    }

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
