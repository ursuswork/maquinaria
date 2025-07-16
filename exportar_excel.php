<?php
include 'conexion.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=maquinaria.xls");

echo "<table border='1'>";
echo "<tr><th>Nombre</th><th>Tipo</th><th>Modelo</th><th>Número de Serie</th><th>Ubicación</th></tr>";

$resultado = $conn->query("SELECT * FROM maquinaria");
while ($row = $resultado->fetch_assoc()) {
  echo "<tr>
          <td>{$row['nombre']}</td>
          <td>{$row['tipo']}</td>
          <td>{$row['modelo']}</td>
          <td>{$row['numero_serie']}</td>
          <td>{$row['ubicacion']}</td>
        </tr>";
}
echo "</table>";
?>