<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

// Validar y limpiar datos del formulario
$nombre         = trim($_POST['nombre'] ?? '');
$tipo           = trim($_POST['tipo'] ?? '');
$modelo         = trim($_POST['modelo'] ?? '');
$numero_serie   = trim($_POST['numero_serie'] ?? '');
$marca          = trim($_POST['marca'] ?? '');
$anio           = trim($_POST['anio'] ?? '');
$ubicacion      = trim($_POST['ubicacion'] ?? '');
$condicion      = trim($_POST['condicion_estimada'] ?? '');
$imagen         = '';

// Verificar campos obligatorios
if ($nombre === '' || $tipo === '') {
    die("❌ Nombre y tipo de maquinaria son obligatorios.");
}

// Procesar la imagen si se envió
if (!empty($_FILES['imagen']['name'])) {
    $directorio = 'imagenes/';
    if (!is_dir($directorio)) {
        mkdir($directorio, 0755, true);
    }

    $nombre_imagen = time() . '_' . basename($_FILES['imagen']['name']);
    $ruta_imagen = $directorio . $nombre_imagen;

    // Validar tipo MIME
    $permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (in_array($_FILES['imagen']['type'], $permitidos)) {
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_imagen)) {
            $imagen = $nombre_imagen;
        } else {
            die("❌ Error al subir la imagen.");
        }
    } else {
        die("❌ Solo se permiten archivos JPG, PNG, GIF o WEBP.");
    }
}

// Insertar registro
$stmt = $conn->prepare("INSERT INTO maquinaria (nombre, tipo, modelo, numero_serie, marca, anio, ubicacion, condicion_estimada, imagen)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("❌ Error en prepare(): " . $conn->error);
}

$stmt->bind_param("sssssssss", $nombre, $tipo, $modelo, $numero_serie, $marca, $anio, $ubicacion, $condicion, $imagen);

if ($stmt->execute()) {
    $ultimo_id = $stmt->insert_id;

    // Redirigir según el tipo
    if ($tipo === 'usada') {
        header("Location: acciones/recibo_unidad.php?id=" . $ultimo_id);
    } else {
        header("Location: inventario.php?agregado=ok");
    }
    exit;
} else {
    echo "❌ Error al guardar: " . $stmt->error;
}
?>
