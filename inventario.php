<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$busqueda = isset($_GET['busqueda']) ? trim($conn->real_escape_string($_GET['busqueda'])) : '';
$tipo_filtro = $_GET['tipo'] ?? 'todas';

$sql = "SELECT * FROM maquinaria";
if (!empty($busqueda)) {
  $sql .= " WHERE (nombre LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%' OR numero_serie LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'nueva') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "tipo_maquinaria = 'nueva'";
} elseif ($tipo_filtro === 'usada') {
  $sql .= (str_contains($sql, "WHERE") ? " AND " : " WHERE ") . "tipo_maquinaria = 'usada'";
}
$sql .= " ORDER BY tipo_maquinaria ASC, nombre ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario de Maquinaria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<div class="container py-4">
  <h3>Inventario de Maquinaria</h3>
  <div class="row">
    <?php while ($fila = $resultado->fetch_assoc()): ?>
      <?php
      $porc_avance = 0;
      $etapas_realizadas = [];
      if (strtolower(trim($fila['tipo_maquinaria'])) == 'nueva') {
        $subtipo = strtolower(trim($fila['subtipo']));
        if ($subtipo == 'esparcidor de sello') {
          $avance_result = $conn->query("SELECT etapa FROM avance_esparcidor WHERE id_maquinaria = {$fila['id']}");
          $etapas = [
            "Trazar, cortar, rolar y hacer ceja a tapas" => 5,
            "Trazar, cortar, rolar cuerpo" => 5,
            "Armar cuerpo" => 5,
            "Armar chasis" => 5,
            "Armar flux" => 5,
            "Colocar chasis y flux" => 5,
            "Colocar tapas y tubulares" => 5,
            "Colocar fibra de vidrio y lámina A.I" => 10,
            "Colocar accesorios" => 5,
            "Armar cajas negras y de controles" => 5,
            "Armar chasis" => 5,
            "Cortar, doblar y armar tolva" => 5,
            "Doblar, armar y colocar cabezal" => 5,
            "Doblar, armar, probar y colocar tanque de aceite" => 5,
            "Armar bomba" => 5,
            "Armar transportadores" => 3,
            "Pintar" => 2,
            "Colocar hidráulico y neumático" => 4,
            "Conectar eléctrico" => 3,
            "Colocar accesorios finales" => 2,
            "Prueba de equipo final" => 5
          ];
        } elseif ($subtipo == 'petrolizadora') {
          $avance_result = $conn->query("SELECT etapa FROM avance_petrolizadora WHERE id_maquinaria = {$fila['id']}");
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
        } elseif ($subtipo == 'bachadora') {
          $avance_result = $conn->query("SELECT etapa FROM avance_bachadora WHERE id_maquinaria = {$fila['id']}");
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
            "Probar equipo" => 5
          ];
        }
        if ($avance_result && $avance_result->num_rows > 0) {
          while ($row = $avance_result->fetch_assoc()) {
            $etapas_realizadas[] = $row['etapa'];
          }
        }
        $peso_total = array_sum($etapas);
        $peso_completado = 0;
        foreach ($etapas as $nombre => $peso) {
          if (in_array($nombre, $etapas_realizadas)) $peso_completado += $peso;
        }
        $porc_avance = round(($peso_completado / $peso_total) * 100);
      }
      ?>
      <div class="col-md-4 mb-4">
        <div class="card bg-secondary p-3 text-light">
          <h5><?= htmlspecialchars($fila['nombre']) ?></h5>
          <p><strong>Modelo:</strong> <?= htmlspecialchars($fila['modelo']) ?></p>
          <p><strong>Tipo:</strong> <?= htmlspecialchars($fila['tipo_maquinaria']) ?></p>
          <p><strong>Subtipo:</strong> <?= htmlspecialchars($fila['subtipo']) ?></p>
          <?php if ($porc_avance > 0): ?>
            <div class="progress mb-2">
              <div class="progress-bar bg-info" style="width: <?= $porc_avance ?>%;">
                <?= $porc_avance ?>%
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>
</body>
</html>
