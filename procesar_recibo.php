<?php
session_start();
include '../conexion.php';

function valor_componente($estado) {
    return match ($estado) {
        'bueno'   => 100,
        'regular' => 70,
        'malo'    => 40,
        default   => 0,
    };
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_maquinaria = intval($_POST['id_maquinaria']);
    $observaciones = $conn->real_escape_string($_POST['observaciones'] ?? '');
    $componentes   = $_POST['componente'] ?? [];

    // Pesos por sección
    $pesos = [
        "MOTOR"                         => 0.30,
        "SISTEMA MECÁNICO"             => 0.00, // no usado aquí
        "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => 0.25,
        "SISTEMA HIDRÁULICO"           => 0.30,
        "ESTÉTICO"                     => 0.05,
        "CONSUMIBLES"                  => 0.10,
    ];

    $total_condicion = 0;

    foreach ($componentes as $seccion => $items) {
        $subtotal = 0;
        $cantidad = count($items);

        foreach ($items as $nombre => $estado) {
            $valor = valor_componente($estado);

            // Guardar en tabla de recibo (opcional)
            $stmt = $conn->prepare("INSERT INTO recibo_unidad 
                (id_maquinaria, seccion, componente, estado) 
                VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $id_maquinaria, $seccion, $nombre, $estado);
            $stmt->execute();

            $subtotal += $valor;
        }

        if ($cantidad > 0 && isset($pesos[$seccion])) {
            $promedio = $subtotal / $cantidad;
            $total_condicion += $promedio * $pesos[$seccion];
        }
    }

    // Normaliza el resultado a porcentaje redondeado
    $condicion_final = round($total_condicion);

    // Actualizar maquinaria
    $stmt = $conn->prepare("UPDATE maquinaria 
                            SET condicion_estimada = ?, observaciones = ?
                            WHERE id = ?");
    $stmt->bind_param("isi", $condicion_final, $observaciones, $id_maquinaria);
    $stmt->execute();

    header("Location: ../inventario.php?evaluado=ok");
    exit;
}
?>
