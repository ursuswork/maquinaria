<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$tipo_filtro = $_GET['tipo'] ?? 'todas';

$sql = "
  SELECT m.*, r.condicion_estimada, r.observaciones 
  FROM maquinaria m
  LEFT JOIN recibo_unidad r ON m.id = r.id_maquinaria
";
if (!empty($busqueda)) {
  $sql .= " WHERE (m.nombre LIKE '%$busqueda%' OR m.modelo LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'produccion nueva') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "m.tipo_maquinaria = 'nueva'";
} elseif ($tipo_filtro === 'usada') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "m.tipo_maquinaria = 'usada'";
}
$sql .= " ORDER BY m.tipo_maquinaria ASC, m.nombre ASC";
$resultado = $conn->query($sql);
?>
<!-- HTML HEAD OMITIDO POR BREVEDAD -->

<?php while ($fila = $resultado->fetch_assoc()): ?>
<?php
  $subtipo = mb_strtolower(trim($fila['subtipo']));
  $avance = 0;
  if ($fila['tipo_maquinaria'] === 'nueva') {
    if ($subtipo === 'bachadora') {
      $q = $conn->query("SELECT avance FROM avance_total_bachadora WHERE id_maquinaria = " . intval($fila['id']));
      if ($q && $r = $q->fetch_assoc()) $avance = intval($r['avance']);
    }
  }
?>
<!-- CONTENIDO OMITIDO -->
<?php endwhile; ?>
