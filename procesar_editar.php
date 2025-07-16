<?php
session_start();
include 'conexion.php';

$id              = intval($_POST['id']);
$nombre          = trim($_POST['nombre'] ?? '');
$tipo            = trim($_POST['tipo'] ?? '');
$modelo          = trim($_POST['modelo'] ?? '');
$numero_serie    = trim($_POST['numero_serie'] ?? '');
$marca           = trim($_POST['marca'] ?? '');
$anio            = trim($_POST['anio'] ?? '');
$ubicacion       = trim($_POST['ubicacion'] ?? '');
$condicion       = trim($_POST['condicion_estimada'] ?? '');
$imagen          = '';

if ($nombre === '' || $tipo === '') {
    die("❌ Nombre y tipo son obligatorios.");
}

if (!empty($_FILES['imagen']['name'])) {
    $permitidos = ['image/jpeg','image/png','image/webp','image/jpg'];
    if (in_array($_FILES['imagen']['type'], $permitidos)) {
        $nombre_archivo = time() . '_' . basename($_FILES['imagen']['name']);
        $ruta = "imagenes/" . $nombre_archivo;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
            $imagen = $nombre_archivo;
        } else {
            die("❌ No se pudo guardar la nueva imagen.");
        }
    } else {
        die("❌ Tipo de archivo no permitido.");
    }
}

$sql = "UPDATE maquinaria SET 
        nombre = ?, tipo = ?, modelo = ?, numero_serie = ?, 
        marca = ?, anio = ?, ubicacion = ?, condicion_estimada = ?" .
        ($imagen ? ", imagen = ?" : "") . " WHERE id = ?";

$stmt = $conn->prepare($sql);
if ($imagen) {
    $stmt->bind_param("sssssssssi", $nombre, $tipo, $modelo, $numero_serie,
                      $marca, $anio, $ubicacion, $condicion, $imagen, $id);
} else {
    $stmt->bind_param("ssssssssi", $nombre, $tipo, $modelo, $numero_serie,
                      $marca, $anio, $ubicacion, $condicion, $id);
}

if ($stmt->execute()) {
    header("Location: inventario.php?editado=ok");
    exit();
} else {
    echo "❌ Error al actualizar: " . $stmt->error;
}
?>