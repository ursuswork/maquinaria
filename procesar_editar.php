<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}

include 'conexion.php';

$id = intval($_POST['id']);
$nombre = $_POST['nombre'];
$modelo = $_POST['modelo'];
$ubicacion = $_POST['ubicacion'];
$tipo_maquinaria = $_POST['tipo_maquinaria'];
$nueva_imagen = null;

// Obtener imagen anterior
$query = $conn->query("SELECT imagen FROM maquinaria WHERE id = $id");
$registro = $query->fetch_assoc();
$imagen_actual = $registro['imagen'] ?? null;

// Si se sube una nueva imagen
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $nombreArchivo = time() . "_" . basename($_FILES["imagen"]["name"]);
    $rutaDestino = "imagenes/" . $nombreArchivo;

    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
        $nueva_imagen = $nombreArchivo;
    } else {
        die("❌ Error al subir la nueva imagen.");
    }
}

// Construir la consulta SQL
if ($nueva_imagen) {
    $sql = "UPDATE maquinaria 
            SET nombre = ?, modelo = ?, ubicacion = ?, tipo_maquinaria = ?, imagen = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $modelo, $ubicacion, $tipo_maquinaria, $nueva_imagen, $id);
} else {
    $sql = "UPDATE maquinaria 
            SET nombre = ?, modelo = ?, ubicacion = ?, tipo_maquinaria = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $modelo, $ubicacion, $tipo_maquinaria, $id);
}

// Ejecutar y verificar
if ($stmt->execute()) {
    header("Location: inventario.php");
    exit;
} else {
    die("❌ Error al actualizar: " . $stmt->error);
}
