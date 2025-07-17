
<?php
// CONFIGURA ESTOS DATOS SEGÚN TU INSTANCIA EN CLEVER CLOUD
$host = 'b3p6qq8wsihgfuawhkmt-mysql.services.clever-cloud.com';
$usuario = 'u0f3xgk6ntxayxon';
$contrasena = '040ii4QwsywMuZIQqLHM';
$basedatos = 'b3p6qq8wsihgfuawhkmt';
$puerto = 3306;

$conn = new mysqli($host, $usuario, $contrasena, $basedatos, $puerto);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
