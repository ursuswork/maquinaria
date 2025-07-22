<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}

include 'conexion.php';

$id = intval($_POST['id']);
$nombre = trim($_POST['nombre']);
$marca = trim($_POST['marca']);
$modelo = trim($_POST['modelo']);
$ubicacion = trim($_POST['ubicacion']);
$tipo_maquinaria = $_POST['tipo_maquinaria'];
$subtipo = ($tipo_maquinaria === 'nueva') ? ($_POST['subtipo'] ?? '') : null;

// Obtener imagen anterior por si no se cambia
$imagen_actual = null;
$res = $conn->query("SELECT imagen FROM maquinaria WHERE id = $id");
if ($fila = $res->fetch_assoc()) {
  $imagen_actual = $fila['imagen'];
}

// Subida de imagen (opcional)
$nombre_imagen = $imagen_actual;
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

// Actualizar base de datos
$stmt = $conn->prepare("UPDATE maquinaria SET nombre = ?, marca = ?, modelo = ?, ubicacion = ?, tipo_maquinaria = ?, subtipo = ?, imagen = ? WHERE id = ?");
$stmt->bind_param("sssssssi", $nombre, $marca, $modelo, $ubicacion, $tipo_maquinaria, $subtipo, $nombre_imagen, $id);

if ($stmt->execute()) {
  header("Location: inventario.php");
  exit;
} else {
  echo "❌ Error al actualizar: " . $stmt->error;
}
?>
