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

    // Crear todas las columnas si no existen
    foreach ($secciones as $lista) {
        foreach ($lista as $componente) {
            $col = $conn->real_escape_string($componente);
            $exists = $conn->query("SHOW COLUMNS FROM recibo_unidad LIKE '$col'");
            if ($exists->num_rows === 0) {
                $conn->query("ALTER TABLE recibo_unidad ADD COLUMN `$col` VARCHAR(20) DEFAULT ''");
            }
        }
    }

    // Asegurar que la columna condicion_estimada exista
    $chk = $conn->query("SHOW COLUMNS FROM recibo_unidad LIKE 'condicion_estimada'");
    if ($chk->num_rows === 0) {
        $conn->query("ALTER TABLE recibo_unidad ADD COLUMN condicion_estimada INT DEFAULT 0");
    }

    // Calcular condición estimada
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

    // Preparar campos dinámicos de componentes
    $campos_extra = "";
    $marcadores = "";
    $valores = [];
    foreach ($componentes as $clave => $valor) {
        $campo = $conn->real_escape_string($clave);
        $campos_extra .= ", `$campo`";
        $marcadores .= ", ?";
        $valores[] = $valor;
    }

    // Verificar si ya existe un recibo
    $check = $conn->query("SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1");
    if ($check->num_rows > 0) {
        // UPDATE dinámico
        $sets = "empresa_origen=?, empresa_destino=?, fecha=NOW(), observaciones=?, condicion_estimada=?";
        foreach ($componentes as $clave => $valor) {
            $sets .= ", `" . $conn->real_escape_string($clave) . "` = ?";
        }
        $sql = "UPDATE recibo_unidad SET $sets WHERE id_maquinaria=?";
        $stmt = $conn->prepare($sql);

        $tipos = str_repeat("s", count($valores) + 3) . "ii";
        $stmt->bind_param($tipos, ...array_merge([$empresa_origen, $empresa_destino, $observaciones, $condicion], $valores, [$id_maquinaria]));
        $stmt->execute();
    } else {
        // INSERT dinámico
        $sql = "INSERT INTO recibo_unidad (id_maquinaria, empresa_origen, empresa_destino, fecha, observaciones, condicion_estimada$campos_extra) VALUES (?, ?, ?, NOW(), ?, ?$marcadores)";
        $stmt = $conn->prepare($sql);

        $tipos = "isssi" . str_repeat("s", count($valores));
        $stmt->bind_param($tipos, ...array_merge([$id_maquinaria, $empresa_origen, $empresa_destino, $observaciones, $condicion], $valores));
        $stmt->execute();
    }

    // Actualizar en maquinaria
    $conn->query("UPDATE maquinaria SET condicion_estimada = $condicion WHERE id = $id_maquinaria");

    // Redirigir a recibo_unidad con confirmación
    header("Location: recibo_unidad.php?id=$id_maquinaria&guardado=1");
    exit;
}
?>
