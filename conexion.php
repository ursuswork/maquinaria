
<?php
// CONFIGURA ESTOS DATOS PARA CLEVER CLOUD
$servername = "mb3p6qq8wsihgfuawhkmt-mysql.services.clever-cloud.com";
$username = "u0f3xgk6ntxayxonu";
$password = "040ii4QwsywMuZIQqLHM";
$dbname = "bb3p6qq8wsihgfuawhkmt";
$puerto = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $puerto);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
