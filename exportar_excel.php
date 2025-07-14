<?php
include 'conexion.php';
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=maquinaria.xls");

$result = $conn->query("SELECT * FROM maquinaria");

echo "Nombre	Tipo	Modelo	Serie	Marca	Año	Ubicación	Condición\n";
while($row = $result->fetch_assoc()) {
    echo "{$row['nombre']}	{$row['tipo']}	{$row['modelo']}	{$row['numero_serie']}	{$row['marca']}	{$row['anio']}	{$row['ubicacion']}	{$row['condicion_estimada']}%
";
}
?>