<?php
session_start();
include 'conexion.php';

$nombre       = $_POST['nombre'] ?? '';
$tipo         = $_POST['tipo'] ?? '';
$modelo       = $_POST['modelo'] ?? '';
$numero_serie = $_POST['numero_serie'] ?? '';
$marca        = $_POST['marca'] ?? '';
$anio         = $_POST['anio'] ?? '';
$ubicacion    = $_POST['ubicacion'] ?? '';
$condicion    = $_POST['condicion_estimada'] ?? '';
$imagen       = '';

// ── Subida de imagen ────────────────────────────────────────────────
if (!empty($_FILES['imagen']['name'])) {
    $dir = 'imagenes/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $archivo = time().'_'.basename($_FILES['imagen']['name']);
    $ruta    = $dir.$archivo;

    $okTipos = ['image/jpeg','image/png','image/jpg','image/webp'];
    if (in_array($_FILES['imagen']['type'], $okTipos) &&
        move_uploaded_file($_FILES['imagen']['tmp_name'],$ruta)) {
        $imagen = $archivo;
    }
}

// ── Insertar registro ───────────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO maquinaria
     (nombre,tipo,modelo,numero_serie,marca,anio,ubicacion,condicion_estimada,imagen)
     VALUES (?,?,?,?,?,?,?,?,?)"
);
$stmt->bind_param("sssssssss",$nombre,$tipo,$modelo,$numero_serie,
                  $marca,$anio,$ubicacion,$condicion,$imagen);
$stmt->execute();

// siempre vuelve al inventario
header("Location: inventario.php?agregado=ok");
exit();
?>
