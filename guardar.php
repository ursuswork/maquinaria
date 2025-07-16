<?php
include 'conexion.php';

$nombre         = $_POST['nombre'] ?? '';
$tipo           = $_POST['tipo'] ?? '';
$modelo         = $_POST['modelo'] ?? '';
$numero_serie   = $_POST['numero_serie'] ?? '';
$marca          = $_POST['marca'] ?? '';
$anio           = $_POST['anio'] ?? '';
$ubicacion      = $_POST['ubicacion'] ?? '';
$condicion      = $_POST['condicion_estimada'] ?? '';

$imagen = '';
if (!empty($_FILES['imagen']['name'])) {
    $nombre_archivo = time() . "_" . basename($_FILES['imagen']['name']);
    $ruta_destino = "imagenes/" . $nombre_archivo;

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
        $imagen = $nombre_archivo;
    } else {
        echo "❌ Error al subir la imagen.";
        exit;
    }
}

// Preparar la consulta SQL para mayor seguridad
$stmt = $conn->prepare("INSERT INTO maquinaria 
    (nombre, tipo, modelo, numero_serie, marca, anio, ubicacion, condicion_estimada, imagen)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo "❌ Error en prepare(): " . $conn->error;
    exit;
}

$stmt->bind_param("sssssssss", $nombre, $tipo, $modelo, $numero_serie, $marca, $anio, $ubicacion, $condicion, $imagen);

if ($stmt->execute()) {
    $ultimo_id = $stmt->insert_id;

    if ($tipo === 'usada') {
        header("Location: acciones/recibo_unidad.php?id=" . $ultimo_id);
    } else {
        header("Location: index.php?mensaje=agregado");
    }
    exit;
} else {
    echo "❌ Error al guardar: " . $stmt->error;
}
?>

