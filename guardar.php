<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

// Validar y limpiar datos del formulario
$nombre         = $_POST['nombre'] ?? '';
$tipo           = $_POST['tipo'] ?? '';
$modelo         = $_POST['modelo'] ?? '';
$numero_serie   = $_POST['numero_serie'] ?? '';
$marca          = $_POST['marca'] ?? '';
$anio           = $_POST['anio'] ?? '';
$ubicacion      = $_POST['ubicacion'] ?? '';
$condicion      = $_POST['condicion_estimada'] ?? '';
$imagen         = '';

// Procesar la imagen si se envió
if (!empty($_FILES['imagen']['name'])) {
    $directorio = 'imagenes/';
    if (!is_dir($directorio)) {
        mkdir($directorio, 0755, true);
    }

    $nombre_imagen = time() . '_' . basename($_FILES['imagen']['name']);
    $ruta_imagen = $directorio . $nombre_imagen;

    // Validar tipo MIME
    $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['imagen']['type'], $permitidos)) {
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_imagen)) {
            $imagen = $nombre_imagen;
        } else {
            die("❌ Error al subir la imagen.");
        }
    } else {
        die("❌ Solo se permiten archivos JPG, PNG o GIF.");
    }
}

// Usar prepare para insertar de forma segura
$stmt = $conn->prepare("INSERT INTO maquinaria (nombre, tipo, modelo, numero_serie, marca, anio, ubicacion, condicion_estimada, imagen)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("❌ Error en prepare(): " . $conn->error);
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
