<?php
session_start();
include 'conexion.php';

$id              = intval($_POST['id']);
$nombre          = $_POST['nombre'];
$tipo            = $_POST['tipo'];
$modelo          = $_POST['modelo'];
$numero_serie    = $_POST['numero_serie'];
$marca           = $_POST['marca'];
$anio            = $_POST['anio'];
$ubicacion       = $_POST['ubicacion'];
$condicion       = $_POST['condicion_estimada'];
$imagen          = '';

if (!empty($_FILES['imagen']['name'])) {
    $nombre_archivo = time() . '_' . basename($_FILES['imagen']['name']);
    $ruta = "imagenes/" . $nombre_archivo;
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
        $imagen = $nombre_archivo;
    }
}

$sql = "UPDATE maquinaria SET 
        nombre = ?, tipo = ?, modelo = ?, numero_serie = ?, marca = ?, anio = ?, ubicacion = ?, condicion_estimada = ?" .
        ($imagen ? ", imagen = ?" : "") . " WHERE id = ?";

if ($imagen) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssi", $nombre, $tipo, $modelo, $numero_serie, $marca, $anio, $ubicacion, $condicion, $imagen, $id);
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $nombre, $tipo, $modelo, $numero_serie, $marca, $anio, $ubicacion, $condicion, $id);
}

if ($stmt->execute()) {
    header("Location: inventario.php?editado=ok");
    exit();
} else {
    echo "❌ Error al guardar cambios: " . $stmt->error;
}
