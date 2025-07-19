<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_maquinaria = intval($_POST['id_maquinaria'] ?? 0);
    $etapa = $_POST['etapa'] ?? '';
    $progreso = intval($_POST['progreso'] ?? 0);

    if ($id_maquinaria > 0 && $etapa && $progreso > 0) {
        // Crear tabla si no existe
        $conn->query("CREATE TABLE IF NOT EXISTS avance_esparcidor (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_maquinaria INT,
            etapa VARCHAR(255),
            progreso INT,
            fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Insertar avance
        $stmt = $conn->prepare("INSERT INTO avance_esparcidor (id_maquinaria, etapa, progreso) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $id_maquinaria, $etapa, $progreso);
        $stmt->execute();

        // Actualizar avance actual en maquinaria (opcional)
        $conn->query("UPDATE maquinaria SET avance = $progreso WHERE id = $id_maquinaria");

        echo "✅ Avance guardado correctamente.";
    } else {
        echo "❌ Datos incompletos.";
    }
} else {
    echo "❌ Método inválido.";
}
?>
