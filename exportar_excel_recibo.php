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
$tipo_filtro = strtolower(trim($_GET['tipo'] ?? 'todas'));

// Encabezados para exportar como Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=inventario_maquinaria_$fecha_actual.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "\xEF\xBB\xBF"; // UTF-8 BOM

echo "<table>";
echo "<tr><td colspan='10'><strong>Exportado por:</strong> $usuario</td></tr>";
echo "<tr><td colspan='10'><strong>Fecha:</strong> $fecha_actual &nbsp;&nbsp;&nbsp;&nbsp; <strong>Hora:</strong> $hora_actual</td></tr>";
echo "<tr><td colspan='10'>&nbsp;</td></tr>";
echo "</table>";

// Tabla principal
echo "<table border='1'>";
echo "<tr style='background:#175266;color:#fff;'>
  <th>ID</th>
  <th>Nombre</th>
  <th>Marca</th>
  <th>Modelo</th>
  <th>Número de Serie</th>
  <th>Ubicación</th>
  <th>Tipo</th>
  <th>Subtipo</th>
  <th>Condición Estimada / Avance (%)</th>
  <th>Fecha de Actualización</th>
</tr>";

// La consulta se ajusta a tu inventario completo con avances
$sql = "
SELECT 
  m.*, 
  r.condicion_estimada, r.fecha AS fecha_recibo,
  ab.avance AS avance_bachadora, ab.fecha_actualizacion AS fecha_bachadora,
  ae.avance AS avance_esparcidor, ae.fecha_actualizacion AS fecha_esparcidor,
  ap.avance AS avance_petrolizadora, ap.fecha_actualizacion AS fecha_petrolizadora
FROM maquinaria m
LEFT JOIN recibo_unidad r ON m.id = r.id_maquinaria
LEFT JOIN avance_bachadora ab ON m.id = ab.id_maquinaria AND ab.etapa IS NULL
LEFT JOIN avance_esparcidor ae ON m.id = ae.id_maquinaria AND ae.etapa IS NULL
LEFT JOIN avance_petrolizadora ap ON m.id = ap.id_maquinaria AND ap.etapa IS NULL
";

$where = [];
if (!empty($busqueda)) {
  $where[] = "(m.nombre LIKE '%$busqueda%' OR m.modelo LIKE '%$busqueda%' OR m.numero_serie LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'nueva') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'nueva'";
}
if ($tipo_filtro === 'usada') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'usada'";
}
if ($tipo_filtro === 'camion') {
  $where[] = "LOWER(TRIM(m.tipo_maquinaria)) = 'camion'";
}
if (count($where)) {
  $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY m.id DESC";
$resultado = $conn->query($sql);

$contador = 0;
while ($fila = $resultado->fetch_assoc()) {
  // Decidir avance/condición/fecha
  $avance = "-";
  $fecha_avance = "-";
  $tipo = strtolower($fila['tipo_maquinaria']);
  $subtipo = strtolower($fila['subtipo']);

  if ($tipo === 'usada') {
    if (!is_null($fila['condicion_estimada'])) {
      $avance = $fila['condicion_estimada'] . "%";
      $fecha_avance = $fila['fecha_recibo'] ? date('d/m/Y', strtotime($fila['fecha_recibo'])) : "-";
    }
  } elseif ($tipo === 'nueva') {
    if ($subtipo === 'bachadora' && !is_null($fila['avance_bachadora'])) {
      $avance = $fila['avance_bachadora'] . "%";
      $fecha_avance = $fila['fecha_bachadora'] ? date('d/m/Y', strtotime($fila['fecha_bachadora'])) : "-";
    } elseif ($subtipo === 'esparcidor de sello' && !is_null($fila['avance_esparcidor'])) {
      $avance = $fila['avance_esparcidor'] . "%";
      $fecha_avance = $fila['fecha_esparcidor'] ? date('d/m/Y', strtotime($fila['fecha_esparcidor'])) : "-";
    } elseif ($subtipo === 'petrolizadora' && !is_null($fila['avance_petrolizadora'])) {
      $avance = $fila['avance_petrolizadora'] . "%";
      $fecha_avance = $fila['fecha_petrolizadora'] ? date('d/m/Y', strtotime($fila['fecha_petrolizadora'])) : "-";
    }
  }

  echo "<tr>";
  echo "<td>{$fila['id']}</td>";
  echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
  echo "<td>" . htmlspecialchars($fila['marca'] ?? '-') . "</td>";
  echo "<td>" . htmlspecialchars($fila['modelo']) . "</td>";
  echo "<td>" . htmlspecialchars($fila['numero_serie'] ?? '-') . "</td>";
  echo "<td>" . htmlspecialchars($fila['ubicacion']) . "</td>";
  echo "<td>" . htmlspecialchars($fila['tipo_maquinaria']) . "</td>";
  echo "<td>" . htmlspecialchars($fila['subtipo'] ?? '-') . "</td>";
  echo "<td>$avance</td>";
  echo "<td>$fecha_avance</td>";
  echo "</tr>";
  $contador++;
}
echo "<tr style='background-color:#e0e0e0; font-weight:bold;'><td colspan='10'>Total de registros exportados: $contador</td></tr>";
echo "</table>";
?>
