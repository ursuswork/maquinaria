<?php
session_start();
include 'conexion.php';

// Validar y limpiar datos del formulario
$nombre       = trim($_POST['nombre'] ?? '');
$tipo         = trim($_POST['tipo'] ?? '');
$modelo       = trim($_POST['modelo'] ?? '');
$numero_serie = trim($_POST['numero_serie'] ?? '');
$marca        = trim($_POST['marca'] ?? '');
$anio         = trim($_POST['anio'] ?? '');
$ubicacion    = trim($_POST['ubicacion'] ?? '');
$condicion    = trim($_POST['condicion_estimada'] ?? '');
$imagen       = '';

// Verificar campos mínimos
if ($nombre === '' || $tipo === '') {
    die("❌ Campos obligatorios incompletos.");
}

// Subida de imagen
if (!empty($_FILES['imagen']['name'])) {
    $dir = 'imagenes/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $archivo = time().'_'.basename($_FILES['imagen']['name']);
    $ruta    = $dir.$archivo;

    $permitidos = ['image/jpeg','image/png','image/jpg','image/webp'];
    if (in_array($_FILES['imagen']['type'], $permitidos) &&
        move_uploaded_file($_FILES['imagen']['tmp_name'],$ruta)) {
        $imagen = $archivo;
    } else {
        die("❌ Error al subir imagen.");
    }
}

// Insertar maquinaria
$stmt = $conn->prepare(
    "INSERT INTO maquinaria
     (nombre,tipo,modelo,numero_serie,marca,anio,ubicacion,condicion_estimada,imagen)
     VALUES (?,?,?,?,?,?,?,?,?)"
);
$stmt->bind_param("sssssssss", $nombre, $tipo, $modelo, $numero_serie,
                  $marca, $anio, $ubicacion, $condicion, $imagen);

if ($stmt->execute()) {
    $ultimo_id = $stmt->insert_id;

    // Redirigir según tipo
    if ($tipo === 'usada') {
        header("Location: acciones/recibo_unidad.php?id=" . $ultimo_id);
    } else {
        header("Location: inventario.php?agregado=ok");
    }
    exit();
} else {
    echo "❌ Error al guardar maquinaria: " . $stmt->error;
}
?>
