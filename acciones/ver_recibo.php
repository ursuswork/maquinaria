<?php
session_start();
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) {
    die("ID inválido");
}

$recibo = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1")->fetch_assoc();
if (!$recibo) {
    die("Recibo no encontrado.");
}

echo "<h2>📄 Recibo de Unidad para Maquinaria #$id_maquinaria</h2>";
echo "<p><strong>Empresa Origen:</strong> " . htmlspecialchars($recibo['empresa_origen']) . "</p>";
echo "<p><strong>Empresa Destino:</strong> " . htmlspecialchars($recibo['empresa_destino']) . "</p>";
echo "<p><strong>Observaciones:</strong> " . nl2br(htmlspecialchars($recibo['observaciones'])) . "</p>";
echo "<p><strong>Condición Estimada:</strong> " . intval($recibo['condicion_estimada']) . "%</p>";
echo "<hr><h4>🛠️ Componentes evaluados:</h4>";

$omitidos = ['id', 'id_maquinaria', 'empresa_origen', 'empresa_destino', 'fecha', 'observaciones', 'condicion_estimada'];
echo "<ul>";
foreach ($recibo as $clave => $valor) {
    if (!in_array($clave, $omitidos) && $valor !== '') {
        echo "<li><strong>" . htmlspecialchars($clave) . ":</strong> " . htmlspecialchars($valor) . "</li>";
    }
}
echo "</ul>";
echo "<a href='../inventario.php' style='display:inline-block;margin-top:20px;' class='btn btn-primary'>⬅️ Volver al inventario</a>";
?>