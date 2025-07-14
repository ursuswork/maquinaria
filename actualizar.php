<?php
include 'conexion.php';
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$tipo = $_POST['tipo'];
$modelo = $_POST['modelo'];
$numero_serie = $_POST['numero_serie'];
$marca = $_POST['marca'];
$anio = $_POST['anio'];
$ubicacion = $_POST['ubicacion'];
$condicion = $_POST['condicion_estimada'];

if ($_FILES['imagen']['name']) {
    $imagen = time() . "_" . basename($_FILES['imagen']['name']);
    move_uploaded_file($_FILES['imagen']['tmp_name'], "imagenes/" . $imagen);
    $conn->query("UPDATE maquinaria SET nombre='$nombre', tipo='$tipo', modelo='$modelo', numero_serie='$numero_serie',
                  marca='$marca', anio='$anio', ubicacion='$ubicacion', condicion_estimada='$condicion', imagen='$imagen'
                  WHERE id=$id");
} else {
    $conn->query("UPDATE maquinaria SET nombre='$nombre', tipo='$tipo', modelo='$modelo', numero_serie='$numero_serie',
                  marca='$marca', anio='$anio', ubicacion='$ubicacion', condicion_estimada='$condicion' WHERE id=$id");
}
header("Location: index.php?mensaje=actualizado");
?>