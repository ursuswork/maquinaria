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
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario de Maquinaria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #001f3f; color: #ffffff; }
    .table thead th { background-color: #004080; color: #ffffff; border: none; }
    .table tbody tr { border-bottom: 1px solid #004f8c; }
    .table tbody tr:nth-child(even) { background-color: #003366; }
    .table tbody tr:nth-child(odd) { background-color: #002b5c; }
    .badge-nueva { background-color: #ffc107; color: #001f3f; padding: 6px 12px; border-radius: 6px; font-size: 0.9rem; font-weight: bold; }
    .progress { height: 22px; border-radius: 20px; background-color: #002b5c; overflow: hidden; }
    .progress-bar { font-weight: bold; background-color: #ffcc00 !important; color: black; border-radius: 20px; transition: width 0.4s ease; }
    .btn-flotante {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #ffc107;
      color: #001f3f;
      padding: 12px 20px;
      border: none;
      border-radius: 30px;
      font-weight: bold;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
      z-index: 1000;
      transition: all 0.3s;
    }
    .btn-flotante:hover { transform: scale(1.1); background-color: #e0a800; color: white; }
    .imagen-thumbnail { max-width: 80px; max-height: 80px; cursor: pointer; transition: transform 0.3s ease-in-out; }
    .imagen-thumbnail:hover { transform: scale(1.1); z-index: 999; }
    .modal-img { display: none; position: fixed; z-index: 9999; padding-top: 60px; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.9); }
    .modal-img-content { margin: auto; display: block; max-width: 90%; max-height: 90%; }
    .modal-img:hover { cursor: pointer; }
  </style>
</head>
<body>
<div class="container py-4">
  <h3 class="text-white mb-3">Inventario de Maquinaria</h3>
  <form class="mb-3" method="GET">
    <div class="input-group">
      <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_filtro) ?>">
      <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre o modelo" value="<?= htmlspecialchars($busqueda) ?>">
    </div>
  </form>
  <table class="table table-hover table-bordered">
    <thead>
      <tr>
        <th>Imagen</th>
        <th>Nombre</th>
        <th>Modelo</th>
        <th>Tipo</th>
        <th>Subtipo</th>
        <th>Avance / Condición</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($fila = $resultado->fetch_assoc()): ?>
      <?php
        $id = intval($fila['id']);
        $subtipo = mb_strtolower(trim($fila['subtipo']));
        $avance = 0;

        if ($fila['tipo_maquinaria'] === 'nueva') {
          $etapas = [];
          if ($subtipo === 'bachadora') {
            $etapas = [
              "Trazar,cortar,rolar y hacer ceja a tapas" => 5, "Trazar,cortar,rolar cuerpo" => 5,
              "Armar cuerpo" => 5, "Armar chasis" => 5, "Armar flux" => 5, "Colocar chasis y flux" => 5,
              "Colocar tapas y tubulares" => 5, "Colocar fibra de vidrio y lamina A.I" => 10,
              "Colocar accesorios" => 5, "Armar ejes" => 5, "Armar jalon" => 5,
              "Armar barra" => 5, "Armar chasis de bomba y motor" => 5, "Armar accesorios" => 5,
              "Montar bomba y motor" => 5, "Montar accesorios" => 5, "Pintar" => 3,
              "Instalacion electrica" => 2, "Checar y tapar fugas" => 5, "Probar equipo" => 5
            ];
          } elseif ($subtipo === 'esparcidor de sello') {
            $etapas = [
              "Trazar,cortar,rolar y hacer ceja a tapas" => 5, "Trazar,cortar,rolar cuerpo" => 5,
              "Armar cuerpo" => 5, "Armar chasis" => 5, "Colocar tapas" => 5, "Colocar fibra" => 10,
              "Armar cajas negras y de controles" => 5, "Armar chasis" => 5,
              "Cortar, doblar y armar tolva" => 5, "Doblar, armar y colocar cabezal" => 5,
              "Doblar, armar, probar y colocar tanque de aceite" => 5, "Armar bomba" => 5,
              "Armar transportadores" => 3, "Pintar" => 2, "Colocar hidráulico y neumático" => 4,
              "Conectar eléctrico" => 3, "Colocar accesorios finales" => 3, "Prueba de equipo final" => 5
            ];
          } elseif ($subtipo === 'petrolizadora') {
            $etapas = [
              "Trazar,cortar,rolar y hacer ceja a tapas" => 5,"Trazar,cortar,rolar cuerpo" => 5,"Armar cuerpo" => 5,"Armar chasis" => 5,"Armar flux" => 5,
              "Armar flux" => 5,"Colocar chasis y flux" => 5,"Colocar tapas y tubulares" => 5,"Colocar fibra de vidrio y lamina A.I" => 5,
              "Colocar accesorios tanque" => 5,"Armar y colocar barra" => 5,"Armar y colocar chasis p/bomba y motor" => 5,
              "Armar,alinear motor y bomba" => 5,"Montar alinear motor" => 5,"Armar tuberia interna y externa" => 5,
              "Alinear y colocar tuberias" => 5,"Colocar accesorios petrolizadora" => 5,"Pintura" => 5,"Intalacion electrica" => 5,
              "Probar y checar fugas" => 5
            ];
          }

          if (!empty($etapas)) {
            $peso_total = array_sum($etapas);
            $peso_completado = 0;
            $q = $conn->query("SELECT etapa FROM avance_{$subtipo} WHERE id_maquinaria = $id");
            while ($row = $q->fetch_assoc()) {
              $etapa = $row['etapa'];
              if (isset($etapas[$etapa])) {
                $peso_completado += $etapas[$etapa];
              }
            }
            $avance = round(($peso_completado / $peso_total) * 100);
          }
        }
      ?>
      <tr>
        <td>
          <?php if (!empty($fila['imagen'])): ?>
            <img src="imagenes/<?= htmlspecialchars($fila['imagen']) ?>" class="imagen-thumbnail" onclick="ampliarImagen(this)">
          <?php else: ?>
            <span class="text-muted">Sin imagen</span>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($fila['nombre']) ?></td>
        <td><?= htmlspecialchars($fila['modelo']) ?></td>
        <td><?= htmlspecialchars($fila['tipo_maquinaria']) ?></td>
        <td><?= htmlspecialchars($fila['subtipo']) ?></td>
        <td>
          <?php
            if ($fila['tipo_maquinaria'] === 'usada') {
              $cond = intval($fila['condicion_estimada']);
              echo "<div class='progress'><div class='progress-bar' style='width: {$cond}%'>{$cond}%</div></div>";
            } else {
              echo "<div class='progress'><div class='progress-bar' style='width: {$avance}%'>{$avance}%</div></div>";
            }
          ?>
        </td>
        <td>
          <a href="editar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil-square"></i></a>
          <a href="eliminar_maquinaria.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger me-1" onclick="return confirm('¿Eliminar?')"><i class="bi bi-trash"></i></a>
          <?php
            $mapa_avance = [
              'bachadora' => 'avance_bachadora.php',
              'esparcidor de sello' => 'avance_esparcidor.php',
              'petrolizadora' => 'avance_petrolizadora.php'
            ];
            if ($fila['tipo_maquinaria'] === 'nueva' && isset($mapa_avance[$subtipo])) {
              echo '<a href="'.$mapa_avance[$subtipo].'?id='.$fila['id'].'" class="btn btn-sm btn-outline-success"><i class="bi bi-bar-chart-line"></i></a>';
            }
          ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<a href="exportar_excel.php" class="btn-flotante">📥 Exportar Excel</a>
<div id="modalImg" class="modal-img" onclick="cerrarImagen()"><img class="modal-img-content" id="imagenAmpliada"></div>
<script>
function ampliarImagen(img) {
  document.getElementById("modalImg").style.display = "block";
  document.getElementById("imagenAmpliada").src = img.src;
}
function cerrarImagen() {
  document.getElementById("modalImg").style.display = "none";
}
</script>
</body>
</html>
