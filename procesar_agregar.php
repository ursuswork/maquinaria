<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}

include 'conexion.php';

// Recoger datos
$id = intval($_POST['id']);
$nombre = trim($_POST['nombre']);
$marca = trim($_POST['marca']);
$modelo = trim($_POST['modelo']);
$ubicacion = trim($_POST['ubicacion']);
$tipo_maquinaria = $_POST['tipo_maquinaria'];
$subtipo = ($tipo_maquinaria == 'nueva') ? ($_POST['subtipo'] ?? '') : null;

// Manejo de imagen
$nombre_imagen = $_POST['imagen_actual'] ?? null;

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
  // Crear carpeta si no existe
  if (!is_dir('imagenes')) {
    mkdir('imagenes', 0775, true);
  }

  $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
  $nombre_imagen = time() . '_' . uniqid() . '.' . $extension;

  if (!is_dir('imagenes')) {
  mkdir('imagenes', 0775, true);
}
  if (!move_uploaded_file($_FILES['imagen']['tmp_name'], 'imagenes/' . $nombre_imagen)) {
    echo "❌ Error al subir la imagen.";
    exit;
  }
}

// Actualizar en base de datos
$stmt = $conn->prepare("UPDATE maquinaria SET nombre=?, marca=?, modelo=?, ubicacion=?, tipo_maquinaria=?, subtipo=?, imagen=? WHERE id=?");
$stmt->bind_param("sssssssi", $nombre, $marca, $modelo, $ubicacion, $tipo_maquinaria, $subtipo, $nombre_imagen, $id);

if ($stmt->execute()) {
  header("Location: inventario.php");
  exit;
} else {
  echo "❌ Error al actualizar: " . $stmt->error;
}
?>
