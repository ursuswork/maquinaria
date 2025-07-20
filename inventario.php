
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
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
  <style>
    body { background-color: #121212; color: #ffffff; }
    .card-maquinaria {
      background-color: #1e1e1e;
      border: 1px solid #333;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }
    .progress { background-color: #333; }
    .progress-bar { font-weight: bold; }
  </style>
</head>
<body>
<div class="container py-4">
  <h3 class="text-light mb-4">Inventario de Maquinaria</h3>
  <div class="row">
    <?php while ($fila = $resultado->fetch_assoc()): ?>
      <div class="col-md-4 mb-4">
        <div class="card card-maquinaria p-3 text-light">
          <?php if (!empty($fila['imagen'])): ?>
            <img src="imagenes/<?= $fila['imagen'] ?>" class="img-fluid rounded mb-2" style="max-height:200px; object-fit:contain;">
          <?php endif; ?>
          <h5><?= htmlspecialchars($fila['nombre']) ?></h5>
          <p class="mb-1"><strong>Modelo:</strong> <?= htmlspecialchars($fila['modelo']) ?></p>
          <p class="mb-1"><strong>Ubicación:</strong> <?= htmlspecialchars($fila['ubicacion']) ?></p>
          <p class="mb-1"><strong>Tipo:</strong> <?= htmlspecialchars($fila['tipo_maquinaria']) ?></p>
          <p class="mb-1"><strong>Subtipo:</strong> <?= htmlspecialchars($fila['subtipo']) ?></p>

          <?php
          $porc_avance = 0;
          if (
            strtolower(trim($fila['tipo_maquinaria'])) == 'nueva' &&
            strtolower(trim($fila['subtipo'])) == 'esparcidor de sello'
          ) {
            $avance_result = $conn->query("SELECT etapa FROM avance_esparcidor WHERE id_maquinaria = { $fila['id'] }");
            $etapas = [];
            while ($row = $avance_result->fetch_assoc()) {
              $etapas[] = $row['etapa'];
            }
            $etapas = [
  "Trazar, cortar, rolar y hacer ceja a tapas" => 5,
  "Trazar, cortar, rolar cuerpo" => 5,
  "Armar cuerpo" => 5,
  "Armar chasis" => 60,
  "Armar flux" => 5,
  "Colocar chasis y flux" => 5,
  "Colocar tapas y tubulares" => 5,
  "Colocar fibra de vidrio y lámina A.I" => 10,
  "Colocar accesorios" => 5,
  "Armar cajas negras y de controles" => 55,
  "Cortar, doblar y armar tolva" => 65,
  "Doblar, armar y colocar cabezal" => 70,
  "Doblar, armar, probar y colocar tanque de aceite" => 75,
  "Armar bomba" => 80,
  "Armar transportadores" => 83,
  "Pintar" => 85,
  "Colocar hidráulico y neumático" => 89,
  "Conectar eléctrico" => 92,
  "Colocar accesorios finales" => 95,
  "Prueba de equipo final" => 100,
];
            $peso_total = array_sum($etapas);
            $peso_completado = 0;
            foreach ($etapas as $nombre => $peso) {
              if (in_array($nombre, $etapas)) $peso_completado += $peso;
            }
            $porc_avance = $peso_total > 0 ? round(($peso_completado / $peso_total) * 100) : 0;
          }
          ?>
          <?php if ($porc_avance > 0): ?>
            <div class="progress mb-2" style="height: 25px;">
              <div class="progress-bar bg-info" style="width: <?= $porc_avance ?>%;">
                <?= $porc_avance ?>%
              </div>
            </div>
          <?php endif; ?>
          <a href="avance_esparcidor.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-info mt-2 w-100">🛠️ Ver Avance</a>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>
</body>
</html>
