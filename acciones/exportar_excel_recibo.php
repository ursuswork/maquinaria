<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}

include 'conexion.php';

// Obtener filtros desde URL
$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$tipo_filtro = $_GET['tipo'] ?? 'todas';

// Encabezados para exportar Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=inventario_maquinaria.xls");
header("Pragma: no-cache");
header("Expires: 0");

// BOM para Excel (para evitar errores con acentos)
echo "\xEF\xBB\xBF";

// Encabezado de la tabla
echo "<table border='1'>";
echo "<tr>
  <th>ID</th>
  <th>Nombre</th>
  <th>Marca</th>
  <th>Modelo</th>
  <th>Ubicación</th>
  <th>Tipo</th>
  <th>Subtipo</th>
  <th>Condición Estimada</th>
</tr>";

// Construir consulta con filtros
$sql = "
  SELECT m.*, r.condicion_estimada 
  FROM maquinaria m
  LEFT JOIN recibo_unidad r ON m.id = r.id_maquinaria
";

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

// Cuerpo de la tabla
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
}

echo "</table>";
?>
