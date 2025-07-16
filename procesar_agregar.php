<?php
include 'conexion.php';
$nombre = $_POST['nombre'];
$tipo = $_POST['tipo'];
$modelo = $_POST['modelo'];
$serie = $_POST['numero_serie'];
$ubicacion = $_POST['ubicacion'];
$nombreImg = time() . '_' . basename($_FILES['imagen']['name']);
$ruta = 'imagenes/' . $nombreImg;
move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
$stmt = $conn->prepare("INSERT INTO maquinaria (nombre, tipo, modelo, numero_serie, ubicacion, imagen) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $nombre, $tipo, $modelo, $serie, $ubicacion, $nombreImg);
$stmt->execute();
header("Location: index.php");
?>