<?php
$host = "b3p6qq8wsihgfuawhkmt-mysql.services.clever-cloud.com";
$user = "u0f3xgk6ntxayxon";
$pass = "040ii4QwsywMuZIQqLHM";
$db   = "b3p6qq8wsihgfuawhkmt";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
