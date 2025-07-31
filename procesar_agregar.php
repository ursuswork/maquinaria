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
$numero_serie = trim($_POST['numero_serie']);
$anio = intval($_POST['anio']);
$tipo_maquinaria = $_POST['tipo_maquinaria'];
$subtipo = ($tipo_maquinaria == 'nueva') ? ($_POST['subtipo'] ?? '') : null;

// Subida de imagen
$nombre_imagen = null;
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
  $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
  $nombre_imagen = time() . '_' . uniqid() . '.' . $extension;
  if (!is_dir('imagenes')) mkdir('imagenes', 0755, true);
  $ruta_destino = 'imagenes/' . $nombre_imagen;
  if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
    echo "❌ Error al mover la imagen a '$ruta_destino'. Verifica permisos.";
    exit;
  }
}

// Actualiza tu consulta para incluir numero_serie y anio
$stmt = $conn->prepare("INSERT INTO maquinaria (nombre, marca, modelo, ubicacion, numero_serie, anio, tipo_maquinaria, subtipo, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $nombre, $marca, $modelo, $ubicacion, $numero_serie, $anio, $tipo_maquinaria, $subtipo, $nombre_imagen);

if ($stmt->execute()) {
  header("Location: inventario.php");
  exit;
} else {
  echo "❌ Error al guardar: " . $stmt->error;
}
?>
