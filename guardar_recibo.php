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

    $pesos = [ /* igual que antes */ ];
    $secciones = [ /* igual que antes */ ];

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

    $campos_extra = "";
    $marcadores = "";
    $valores = [];
    foreach ($componentes as $clave => $valor) {
        $campo = $conn->real_escape_string($clave);
        $campos_extra .= ", `$campo`";
        $marcadores .= ", ?";
        $valores[] = $valor;
    }

    $check = $conn->query("SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1");

    if ($check && $check->num_rows > 0) {
        $sets = "empresa_origen=?, empresa_destino=?, fecha=NOW(), observaciones=?, condicion_estimada=?";
        foreach ($componentes as $clave => $valor) {
            $sets .= ", `" . $conn->real_escape_string($clave) . "` = ?";
        }
        $sql = "UPDATE recibo_unidad SET $sets WHERE id_maquinaria=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("<p style='color:red;'>❌ Error en prepare UPDATE: " . $conn->error . "</p>");
        }

        $tipos = str_repeat("s", count($valores) + 3) . "ii";
        $bind_result = $stmt->bind_param($tipos, ...array_merge([$empresa_origen, $empresa_destino, $observaciones, $condicion], $valores, [$id_maquinaria]));
    } else {
        $sql = "INSERT INTO recibo_unidad (id_maquinaria, empresa_origen, empresa_destino, fecha, observaciones, condicion_estimada$campos_extra) VALUES (?, ?, ?, NOW(), ?, ?$marcadores)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("<p style='color:red;'>❌ Error en prepare INSERT: " . $conn->error . "</p>");
        }

        $tipos = "isssi" . str_repeat("s", count($valores));
        $bind_result = $stmt->bind_param($tipos, ...array_merge([$id_maquinaria, $empresa_origen, $empresa_destino, $observaciones, $condicion], $valores));
    }

    if (!$bind_result) {
        die("<p style='color:red;'>❌ Error al vincular parámetros: " . $stmt->error . "</p>");
    }

    if (!$stmt->execute()) {
        die("<p style='color:red;'>❌ Error al ejecutar: " . $stmt->error . "</p>");
    }

    // ✅ Guardado exitoso
    if (!$conn->query("UPDATE maquinaria SET condicion_estimada = $condicion WHERE id = $id_maquinaria")) {
        die("<p style='color:red;'>❌ Error al actualizar maquinaria: " . $conn->error . "</p>");
    }

    header("Location: ../inventario.php?actualizado=1");
    exit;
}
