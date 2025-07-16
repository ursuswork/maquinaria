<?php
session_start();
include 'conexion.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $conn->query("DELETE FROM maquinaria WHERE id = $id");
}

header("Location: inventario.php?eliminado=ok");
exit();
