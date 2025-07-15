<?php
include 'conexion.php';

$nombre = $_POST['nombre'];
$tipo = $_POST['tipo'];
$modelo = $_POST['modelo'];
$numero_serie = $_POST['numero_serie'];
$marca = $_POST['marca'];
$anio = $_POST['anio'];
$ubicacion = $_POST['ubicacion'];
$condicion = $_POST['condicion_estimada'];

$imagen = '';
if ($_FILES['imagen']['name']) {
    $imagen = time() . "_" . basename($_FILES['imagen']['name']);
    move_uploaded_file($_FILES['imagen']['tmp_name'], "imagenes/" . $imagen);
}

$sql = "INSERT INTO maquinaria (nombre, tipo, modelo, numero_serie, marca, anio, ubicacion, condicion_estimada, imagen)
        VALUES ('$nombre', '$tipo', '$modelo', '$numero_serie', '$marca', '$anio', '$ubicacion', '$condicion', '$imagen')";

if ($conn->query($sql)) {
    $ultimo_id = $conn->insert_id;

    if ($tipo === 'usada') {
        header("Location: acciones/recibo_unidad.php?id=" . $ultimo_id);
    } else {
        header("Location: index.php?mensaje=agregado");
    }
    exit;
} else {
    echo "Error al guardar: " . $conn->error;
}
?>
