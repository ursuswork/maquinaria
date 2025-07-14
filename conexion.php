<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "inventario_maquinaria";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>