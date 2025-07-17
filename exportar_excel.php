
<?php
include 'conexion.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=maquinaria_" . date("Ymd") . ".xls");

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'nueva';
$sql = "SELECT * FROM maquinaria WHERE tipo_maquinaria = '$tipo'";
$resultado = $conn->query($sql);

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Tipo</th><th>Modelo</th><th>Ubicación</th><th>Condición</th></tr>";
while ($row = $resultado->fetch_assoc()) {
  echo "<tr>
    <td>{$row['id']}</td>
    <td>{$row['nombre']}</td>
    <td>{$row['tipo_maquinaria']}</td>
    <td>{$row['modelo']}</td>
    <td>{$row['ubicacion']}</td>
    <td>{$row['condicion_estimada']}%</td>
  </tr>";
}
echo "</table>";
?>
