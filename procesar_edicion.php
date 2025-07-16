<?php
include 'conexion.php';
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$tipo = $_POST['tipo'];
$modelo = $_POST['modelo'];
$serie = $_POST['numero_serie'];
$ubicacion = $_POST['ubicacion'];

if (!empty($_FILES['imagen']['name'])) {
  $nombreImg = time() . '_' . basename($_FILES['imagen']['name']);
  $ruta = 'imagenes/' . $nombreImg;
  move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
  $stmt = $conn->prepare("UPDATE maquinaria SET nombre=?, tipo=?, modelo=?, numero_serie=?, ubicacion=?, imagen=? WHERE id=?");
  $stmt->bind_param("ssssssi", $nombre, $tipo, $modelo, $serie, $ubicacion, $nombreImg, $id);
} else {
  $stmt = $conn->prepare("UPDATE maquinaria SET nombre=?, tipo=?, modelo=?, numero_serie=?, ubicacion=? WHERE id=?");
  $stmt->bind_param("sssssi", $nombre, $tipo, $modelo, $serie, $ubicacion, $id);
}
$stmt->execute();
header("Location: index.php");
?>