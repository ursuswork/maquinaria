<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=inventario_maquinaria.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>
  <th>ID</th>
  <th>Nombre</th>
  <th>Modelo</th>
  <th>Ubicación</th>
  <th>Tipo</th>
  <th>Subtipo</th>
  <th>Condición Estimada</th>
</tr>";

$sql = "
  SELECT m.*, r.condicion_estimada 
  FROM maquinaria m
  LEFT JOIN recibo_unidad r ON m.id = r.id_maquinaria
  ORDER BY m.id DESC
";
$resultado = $conn->query($sql);
while ($fila = $resultado->fetch_assoc()) {
  echo "<tr>";
  echo "<td>{$fila['id']}</td>";
  echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
  echo "<td>" . htmlspecialchars($fila['modelo']) . "</td>";
  echo "<td>" . htmlspecialchars($fila['ubicacion']) . "</td>";
  echo "<td>" . htmlspecialchars($fila['tipo_maquinaria']) . "</td>";
  echo "<td>" . htmlspecialchars($fila['subtipo']) . "</td>";
  echo "<td>" . ($fila['condicion_estimada'] ?? '-') . "%</td>";
  echo "</tr>";
}
echo "</table>";
?>
