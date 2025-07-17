<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

// Validar datos del formulario
$nombre = $_POST['nombre'] ?? '';
$modelo = $_POST['modelo'] ?? '';
$ubicacion = $_POST['ubicacion'] ?? '';
$tipo_maquinaria = $_POST['tipo_maquinaria'] ?? '';
$condicion_estimada = 100; // Por defecto

if ($nombre && $modelo && $ubicacion && in_array($tipo_maquinaria, ['nueva', 'usada'])) {

  // Procesar imagen
  $imagen_nombre = '';
  if (!empty($_FILES['imagen']['name'])) {
    $directorio = "imagenes/";
    if (!is_dir($directorio)) {
      mkdir($directorio, 0777, true);
    }

    $nombre_archivo = time() . "_" . basename($_FILES["imagen"]["name"]);
    $ruta_destino = $directorio . $nombre_archivo;

    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_destino)) {
      $imagen_nombre = $nombre_archivo;
    }
  }

  $stmt = $conn->prepare("INSERT INTO maquinaria (nombre, modelo, ubicacion, tipo_maquinaria, imagen, condicion_estimada) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssssi", $nombre, $modelo, $ubicacion, $tipo_maquinaria, $imagen_nombre, $condicion_estimada);
  $stmt->execute();

  header("Location: inventario.php");
  exit;
} else {
  echo "❌ Todos los campos son obligatorios.";
}
?>
