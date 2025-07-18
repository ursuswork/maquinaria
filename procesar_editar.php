<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $id = intval($_POST['id'] ?? 0);
  $nombre = $_POST['nombre'] ?? '';
  $modelo = $_POST['modelo'] ?? '';
  $ubicacion = $_POST['ubicacion'] ?? '';
  $tipo_maquinaria = $_POST['tipo_maquinaria'] ?? '';
  $subtipo = $_POST['subtipo'] ?? null;

  if ($id <= 0 || empty($nombre) || empty($modelo) || empty($ubicacion) || empty($tipo_maquinaria)) {
    die("❌ Datos incompletos.");
  }

  $imagen = null;
  if (!empty($_FILES['imagen']['name'])) {
    $nombre_imagen = basename($_FILES['imagen']['name']);
    $ruta = "imagenes/" . time() . "_" . $nombre_imagen;
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
      $imagen = basename($ruta);
    }
  }

  // Preparamos la sentencia SQL
  $sql = "UPDATE maquinaria SET nombre = ?, modelo = ?, ubicacion = ?, tipo_maquinaria = ?, subtipo = ?" . ($imagen ? ", imagen = ?" : "") . " WHERE id = ?";
  $stmt = $conn->prepare($sql);

  if ($imagen) {
    $stmt->bind_param("ssssssi", $nombre, $modelo, $ubicacion, $tipo_maquinaria, $subtipo, $imagen, $id);
  } else {
    $stmt->bind_param("sssssi", $nombre, $modelo, $ubicacion, $tipo_maquinaria, $subtipo, $id);
  }

  if ($stmt->execute()) {
    header("Location: inventario.php?editado=1");
    exit;
  } else {
    echo "❌ Error al actualizar: " . $stmt->error;
  }
}
?>
