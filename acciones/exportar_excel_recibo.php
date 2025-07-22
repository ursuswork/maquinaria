<?php
require 'conexion.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=maquinaria_inventario.xls");
header("Pragma: no-cache");
header("Expires: 0");

$sql = "
  SELECT m.nombre, m.modelo, m.marca, m.numero_serie, m.tipo_maquinaria, m.subtipo, m.ubicacion, r.condicion_estimada
  FROM maquinaria m
  LEFT JOIN recibo_unidad r ON m.id = r.id_maquinaria
  ORDER BY m.tipo_maquinaria ASC, m.nombre ASC
";
$resultado = $conn->query($sql);

echo "<table border='1'>";
echo "<tr>
  <th>Nombre</th>
  <th>Modelo</th>
  <th>Marca</th>
  <th>No. Serie</th>
  <th>Tipo</th>
  <th>Subtipo</th>
  <th>Ubicación</th>
  <th>Condición Estimada (%)</th>
</tr>";

while ($row = $resultado->fetch_assoc()) {
  echo "<tr>
    <td>" . htmlspecialchars($row['nombre']) . "</td>
    <td>" . htmlspecialchars($row['modelo']) . "</td>
    <td>" . htmlspecialchars($row['marca']) . "</td>
    <td>" . htmlspecialchars($row['numero_serie']) . "</td>
    <td>" . htmlspecialchars($row['tipo_maquinaria']) . "</td>
    <td>" . htmlspecialchars($row['subtipo']) . "</td>
    <td>" . htmlspecialchars($row['ubicacion']) . "</td>
    <td>" . ($row['condicion_estimada'] ?? '-') . "</td>
  </tr>";
}

echo "</table>";
exit;
