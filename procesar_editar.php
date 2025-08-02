<?php
session_start();
include 'conexion.php';

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) die("ID inválido.");

$nombre = trim($_POST['nombre']);
$marca = trim($_POST['marca']);
$modelo = trim($_POST['modelo']);
$ubicacion = trim($_POST['ubicacion']);
$numero_serie = trim($_POST['numero_serie']);
$anio = intval($_POST['anio']);
$tipo_maquinaria = $_POST['tipo_maquinaria'];
$subtipo = ($tipo_maquinaria == 'nueva') ? ($_POST['subtipo'] ?? '') : null;
$capacidad = isset($_POST['capacidad']) ? trim($_POST['capacidad']) : null;

// Subida de imagen (opcional)
$update_imagen = "";
$params = [
  $nombre, $marca, $modelo, $ubicacion, $numero_serie, $anio, $tipo_maquinaria, $subtipo, $capacidad
];
$param_types = "sssssssss";

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
  $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
  $nombre_imagen = time() . '_' . uniqid() . '.' . $extension;
  if (!is_dir('imagenes')) mkdir('imagenes', 0755, true);
  $ruta_destino = 'imagenes/' . $nombre_imagen;
  if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
    $update_imagen = ", imagen = ?";
    $params[] = $nombre_imagen;
    $param_types .= "s";
  }
}

$params[] = $id;
$param_types .= "i";

$sql = "
  UPDATE maquinaria SET 
    nombre=?, marca=?, modelo=?, ubicacion=?, numero_serie=?, anio=?, tipo_maquinaria=?, subtipo=?, capacidad=?
    $update_imagen
  WHERE id=?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($param_types, ...$params);

if ($stmt->execute()) {
  header("Location: inventario.php");
  exit;
} else {
  echo "❌ Error al actualizar: " . $stmt->error;
}
?>
