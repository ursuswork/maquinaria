<?php
// Conexión a MySQL en Clever Cloud
$servername = "b3p6qq8wsihgfuawhkmt-mysql.services.clever-cloud.com";
$username   = "u0f3xgk6ntxayxon";
$password   = "040ii4QwsywMuZIQqLHM";
$dbname     = "b3p6qq8wsihgfuawhkmt";
$puerto     = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $puerto);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
