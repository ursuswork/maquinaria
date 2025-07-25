<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}

include 'conexion.php';

header("Content-Type: application/vnd.ms-excel");
$fecha = date("Y-m-d");
header("Content-Disposition: attachment; filename=inventario_maquinaria_$fecha.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>
  <th>ID</th>
  <th>Nombre</th>
  <th>Modelo</th>
  <th>Tipo</th>
  <th>Subtipo</th>
  <th>Avance</th>
</tr>";

$sql = "SELECT * FROM maquinaria ORDER BY id DESC";
$resultado = $conn->query($sql);

while ($fila = $resultado->fetch_assoc()) {
  $avance = '';
  $id = intval($fila['id']);
  $tipo = strtolower(trim($fila['tipo_maquinaria']));
  $subtipo = strtolower(trim($fila['subtipo']));

  if ($tipo === 'nueva') {
    if ($subtipo === 'bachadora') {
      $q = $conn->query("SELECT etapa FROM avance_bachadora WHERE id_maquinaria = $id");
      $completadas = $q ? $q->num_rows : 0;
      $avance = $completadas > 0 ? calcular_avance($conn, $id, 'avance_bachadora', 'bachadora') : '0%';
    } elseif ($subtipo === 'esparcidor de sello') {
      $q = $conn->query("SELECT etapa FROM avance_esparcidor WHERE id_maquinaria = $id");
      $completadas = $q ? $q->num_rows : 0;
      $avance = $completadas > 0 ? calcular_avance($conn, $id, 'avance_esparcidor', 'esparcidor') : '0%';
    } elseif ($subtipo === 'petrolizadora') {
      $q = $conn->query("SELECT etapa FROM avance_petrolizadora WHERE id_maquinaria = $id");
      $completadas = $q ? $q->num_rows : 0;
      $avance = $completadas > 0 ? calcular_avance($conn, $id, 'avance_petrolizadora', 'petrolizadora') : '0%';
    }
  }

  echo "<tr>
    <td>{$fila['id']}</td>
    <td>" . htmlspecialchars($fila['nombre']) . "</td>
    <td>" . htmlspecialchars($fila['modelo']) . "</td>
    <td>{$fila['tipo_maquinaria']}</td>
    <td>{$fila['subtipo']}</td>
    <td>{$avance}</td>
  </tr>";
}

echo "</table>";

// Función que calcula el avance dependiendo de subtipo
function calcular_avance($conn, $id, $tabla, $tipo) {
  $etapas = [];

  if ($tipo === 'bachadora') {
    $etapas = [
      "Trazar,cortar,rolar y hacer ceja a tapas" => 5,
      "Trazar,cortar,rolar cuerpo" => 5,
      "Armar cuerpo" => 5,
      "Armar chasis" => 5,
      "Armar flux" => 5,
      "Colocar chasis y flux" => 5,
      "Colocar tapas y tubulares" => 5,
      "Colocar fibra de vidrio y lamina A.I" => 10,
      "Colocar accesorios" => 5,
      "Armar ejes" => 5,
      "Armar jalon" => 5,
      "Armar barra" => 5,
      "Armar chasis de bomba y motor" => 5,
      "Armar accesorios" => 5,
      "Montar bomba y motor" => 5,
      "Montar accesorios" => 5,
      "Pintar" => 3,
      "Instalacion electrica" => 2,
      "Checar y tapar fugas" => 5,
      "Probar equipo" => 5,
    ];
  } elseif ($tipo === 'esparcidor') {
    $etapas = [
      "Trazar,cortar,rolar y hacer ceja a tapas" => 5,
      "Trazar,cortar,rolar cuerpo" => 5,
      "Armar cuerpo" => 5,
      "Armar chasis" => 5,
      "Armar flux" => 5,
      "Colocar chasis y flux" => 5,
      "Colocar tapas y tubulares" => 5,
      "Colocar fibra de vidrio y lamina A.I" => 10,
      "Colocar accesorios" => 5,
      "Armar cajas negras y de controles" => 5,
      "Armar chasis" => 5,
      "Cortar, doblar y armar tolva" => 5,
      "Doblar, armar y colocar cabezal" => 5,
      "Doblar,armar,probar y colocar tanque de aceite" => 5,
      "Armar bomba" => 5,
      "Armar transportadores" => 3,
      "Pintar" => 2,
      "Colocar hidráulico y neumático" => 4,
      "Conectar eléctrico" => 3,
      "Colocar accesorios finales" => 3,
      "Prueba de equipo final" => 5
    ];
  } elseif ($tipo === 'petrolizadora') {
    $etapas = [
      "Trazar,cortar,rolar y hacer ceja a tapas" => 5,
      "Trazar,cortar,rolar cuerpo" => 5,
      "Armar cuerpo" => 5,
      "Armar chasis" => 5,
      "Armar flux" => 5,
      "Colocar chasis y flux" => 5,
      "Colocar tapas y tubulares" => 5,
      "Colocar fibra de vidrio y lamina A.I" => 10,
      "Colocar accesorios tanque" => 5,
      "Armar y colocar barra" => 5,
      "Armar y colocar chasis p/bomba y motor" => 5,
      "Armar,alinear motor y bomba" => 5,
      "Montar alinear motor" => 5,
      "Armar tuberia interna y externa" => 5,
      "Alinear y colocar tuberias" => 5,
      "Colocar accesorios petrolizadora" => 5,
      "Pintura" => 5,
      "Intalacion electrica" => 5,
      "Probar y checar fugas" => 5
    ];
  }

  $peso_total = array_sum($etapas);
  $res = $conn->query("SELECT etapa FROM `$tabla` WHERE id_maquinaria = $id");
  $peso_actual = 0;

  while ($row = $res->fetch_assoc()) {
    $etapa = $row['etapa'];
    if (isset($etapas[$etapa])) {
      $peso_actual += $etapas[$etapa];
    }
  }

  return $peso_total > 0 ? round(($peso_actual / $peso_total) * 100) . '%' : '0%';
}
?>
