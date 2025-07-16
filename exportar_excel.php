<?php
include 'conexion.php';

// Establecer headers para descarga
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=inventario_maquinaria.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Consulta
$sql = "SELECT nombre, tipo, modelo, numero_serie, marca, anio, ubicacion, condicion_estimada FROM maquinaria";
$resultado = $conn->query($sql);

// Encabezados de tabla
echo "<table border='1'>";
echo "<tr>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>Modelo</th>
        <th>Número de Serie</th>
        <th>Marca</th>
        <th>Año</th>
        <th>Ubicación</th>
        <th>Condición (%)</th>
      </tr>";

// Filas
while ($row = $resultado->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
    echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
    echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
    echo "<td>" . htmlspecialchars($row['numero_serie']) . "</td>";
    echo "<td>" . htmlspecialchars($row['marca']) . "</td>";
    echo "<td>" . htmlspecialchars($row['anio']) . "</td>";
    echo "<td>" . htmlspecialchars($row['ubicacion']) . "</td>";
    echo "<td>" . htmlspecialchars($row['condicion_estimada']) . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
