<?php
session_start();
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_maquinaria = intval($_POST['id_maquinaria'] ?? 0);
    $etapa = $_POST['etapa'] ?? '';
    $porcentaje = intval($_POST['porcentaje'] ?? 0);

    if ($id_maquinaria <= 0 || $etapa === '' || $porcentaje <= 0) {
        http_response_code(400);
        echo "❌ Datos incompletos.";
        exit;
    }

    // Asegura que la tabla avance_esparcidor exista
    $conn->query("
        CREATE TABLE IF NOT EXISTS avance_esparcidor (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_maquinaria INT NOT NULL,
            etapa VARCHAR(100),
            porcentaje INT,
            fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_etapa (id_maquinaria, etapa)
        )
    ");

    // Insertar o actualizar avance
    $stmt = $conn->prepare("
        INSERT INTO avance_esparcidor (id_maquinaria, etapa, porcentaje) 
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE porcentaje = VALUES(porcentaje), fecha = NOW()
    ");
    $stmt->bind_param("isi", $id_maquinaria, $etapa, $porcentaje);

    if ($stmt->execute()) {
        echo "✅ Avance guardado.";
    } else {
        http_response_code(500);
        echo "❌ Error al guardar: " . $conn->error;
    }
}
?>
