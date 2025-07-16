<?php
$host = "b3p6qq8wsihgfuawhkmt-mysql.services.clever-cloud.com";
$user = "u0f3xgk6ntxayxon";
$pass = "040ii4QwsywMuZIQqLHM";
$db   = "b3p6qq8wsihgfuawhkmt";

// Reportar errores como excepciones
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("❌ Conexión fallida: " . $conn->connect_error);
}
?>
