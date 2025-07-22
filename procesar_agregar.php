<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}

include 'conexion.php';

// Recoger datos
$nombre = trim($_POST['nombre']);
$marca = trim($_POST['marca']);
$modelo = trim($_POST['modelo']);
$ubicacion = trim($_POST['ubicacion']);
$tipo_maquinaria = $_POST['tipo_maquinaria'];
$subtipo = ($tipo_maquinaria == 'nueva') ? ($_POST['subtipo'] ?? '') : null;

// Subida de imagen
$nombre_imagen = null;
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
  $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
  $nombre_imagen = time() . '_' . uniqid() . '.' . $extension;

  // Crear carpeta si no existe
  if (!is_dir('imagenes')) {
    mkdir('imagenes', 0755, true);
  }

  $ruta_destino = 'imagenes/' . $nombre_imagen;
  if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
    echo "❌ Error al mover la imagen a '$ruta_destino'. Verifica permisos.";
    exit;
  }
}

// Insertar en base de datos
$stmt = $conn->prepare("INSERT INTO maquinaria (nombre, marca, modelo, ubicacion, tipo_maquinaria, subtipo, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $nombre, $marca, $modelo, $ubicacion, $tipo_maquinaria, $subtipo, $nombre_imagen);

if ($stmt->execute()) {
  header("Location: inventario.php");
  exit;
} else {
  echo "❌ Error al guardar: " . $stmt->error;
}
?>
