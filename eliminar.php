<?php
include 'conexion.php';
$id = $_GET['id'];
$conn->query("DELETE FROM maquinaria WHERE id=$id");
header("Location: index.php?mensaje=eliminado");
?>