<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}

include 'conexion.php';

$usuario = $_SESSION['usuario'];
$fecha_actual = date('Y-m-d');
$hora_actual = date('H:i:s');
$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$tipo_filtro = $_GET['tipo'] ?? 'todas';

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=inventario_maquinaria_$fecha_actual.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "\xEF\xBB\xBF";

echo "<table>";
echo "<tr><td colspan='8'><strong>Exportado por:</strong> $usuario</td></tr>";
echo "<tr><td colspan='8'><strong>Fecha:</strong> $fecha_actual &nbsp;&nbsp;&nbsp;&nbsp; <strong>Hora:</strong> $hora_actual</td></tr>";
echo "<tr><td colspan='8'>&nbsp;</td></tr>";
echo "</table>";

echo "<table border='1'>";
echo "<tr>
  <th>ID</th><th>Nombre</th><th>Marca</th><th>Modelo</th><th>Ubicación</th>
  <th>Tipo</th><th>Subtipo</th><th>Condición Estimada</th>
</tr>";

$sql = "SELECT m.*, r.condicion_estimada 
        FROM maquinaria m 
        LEFT JOIN recibo_unidad r ON m.id = r.id_maquinaria";

if (!empty($busqueda)) {
  $sql .= " WHERE (m.nombre LIKE '%$busqueda%' OR m.modelo LIKE '%$busqueda%' OR m.numero_serie LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'nueva') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "m.tipo_maquinaria = 'nueva'";
} elseif ($tipo_filtro === 'usada') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "m.tipo_maquinaria = 'usada'";
}

$sql .= " ORDER BY m.id DESC";
$resultado = $conn->query($sql);

$contador = 0;
while ($fila = $resultado->fetch_assoc()) {
  echo "<tr>";
  echo "<td>{$fila['id']}</td>";
  echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
  echo "<td>" . htmlspecialchars($fila['marca'] ?? '-') . "</td>";
  echo "<td>" . htmlspecialchars($fila['modelo']) . "</td>";
  echo "<td>" . htmlspecialchars($fila['ubicacion']) . "</td>";
  echo "<td>" . htmlspecialchars($fila['tipo_maquinaria']) . "</td>";
  echo "<td>" . htmlspecialchars($fila['subtipo'] ?? '-') . "</td>";
  echo "<td>" . ($fila['condicion_estimada'] !== null ? $fila['condicion_estimada'] . "%" : '-') . "</td>";
  echo "</tr>";
  $contador++;
}

echo "<tr style='background-color:#e0e0e0; font-weight:bold;'><td colspan='8'>Total de registros exportados: $contador</td></tr>";
echo "</table>";
?>
