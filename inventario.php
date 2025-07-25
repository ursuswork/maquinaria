<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
include 'conexion.php';

// Parámetros
$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$tipo_filtro = strtolower(trim($_GET['tipo'] ?? 'todas'));

// Consulta principal
$sql = "
  SELECT m.*, r.condicion_estimada, r.observaciones 
  FROM maquinaria m
  LEFT JOIN recibo_unidad r ON m.id = r.id_maquinaria
";
if ($busqueda !== '') {
  $sql .= " WHERE (m.nombre LIKE '%$busqueda%' OR m.modelo LIKE '%$busqueda%' OR m.numero_serie LIKE '%$busqueda%')";
}
if ($tipo_filtro === 'produccion nueva' || $tipo_filtro === 'nueva') {
  $sql .= (strpos($sql, 'WHERE') !== false ? ' AND ' : ' WHERE ') . "LOWER(TRIM(m.tipo_maquinaria)) = 'nueva'";
} elseif ($tipo_filtro === 'usada') {
  $sql .= (strpos($sql, 'WHERE') !== false ? ' AND ' : ' WHERE ') . "LOWER(TRIM(m.tipo_maquinaria)) = 'usada'";
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
    .table thead th { background-color: #004080; color: #ffffff; }
    .table tbody tr:nth-child(even) { background-color: #003366; }
    .table tbody tr:nth-child(odd) { background-color: #002b5c; }
    .badge-nueva { background-color: #ffc107; color: #001f3f; padding: 6px 12px; border-radius: 6px; }
    .progress { height: 22px; border-radius: 20px; background-color: #002b5c; overflow: hidden; }
    .progress-bar { font-weight: bold; background-color: #ffcc00 !important; color: black; }
    .btn-flotante {
      position: fixed; bottom: 20px; right: 20px;
      background-color: #ffc107; color: #001f3f;
      padding: 12px 20px; border: none; border-radius: 30px;
      font-weight: bold; box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
    .imagen-thumbnail { max-width:80px; max-height:80px; cursor:pointer; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h3>Inventario de Maquinaria</h3>
    <div>
      <a href="agregar_maquinaria.php" class="btn btn-primary">+ Agregar Maquinaria</a>
      <a href="logout.php" class="btn btn-secondary">Cerrar sesión</a>
    </div>
  </div>

  <!-- Pestañas -->
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link <?= $tipo_filtro==='todas'?'active':''?>" href="?tipo=todas">Todas</a></li>
    <li class="nav-item"><a class="nav-link <?= ($tipo_filtro==='produccion nueva'||$tipo_filtro==='nueva')?'active':''?>" href="?tipo=produccion nueva">Producción Nueva</a></li>
    <li class="nav-item"><a class="nav-link <?= $tipo_filtro==='usada'?'active':''?>" href="?tipo=usada">Usada</a></li>
  </ul>

  <!-- Búsqueda -->
  <form class="mb-3" method="GET">
    <div class="input-group">
      <input type="hidden" name="tipo" value="<?=htmlspecialchars($tipo_filtro)?>">
      <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, modelo o número de serie" value="<?=htmlspecialchars($busqueda)?>">
      <button class="btn btn-warning" type="submit">Buscar</button>
    </div>
  </form>

  <!-- Tabla -->
  <table class="table table-hover table-bordered text-white">
    <thead>
      <tr>
        <th>Imagen</th><th>Nombre</th><th>Modelo</th><th>Ubicación</th>
        <th>Tipo</th><th>Subtipo</th><th>Avance / Condición</th><th>Observaciones</th><th>Acciones</th>
      </tr>
    </thead>
    <tbody>
<?php while($fila=$resultado->fetch_assoc()):
  $id = intval($fila['id']);
  $tipo_maq = strtolower(trim($fila['tipo_maquinaria']));
  $subtipo = strtolower(trim($fila['subtipo']));
  $avance = 0;

  if ($tipo_maq === 'nueva') {
    if ($subtipo === 'bachadora') {
      $q = $conn->query("SELECT SUM(
        CASE 
          WHEN etapa='Trazar,cortar,rolar y hacer ceja a tapas' THEN 5
          WHEN etapa='Trazar,cortar,rolar cuerpo' THEN 5
          WHEN etapa='Armar cuerpo' THEN 5
          WHEN etapa='Armar chasis' THEN 5
          WHEN etapa='Armar flux' THEN 5
          WHEN etapa='Colocar chasis y flux' THEN 5
          WHEN etapa='Colocar tapas y tubulares' THEN 5
          WHEN etapa='Colocar fibra de vidrio y lamina A.I' THEN 10
          WHEN etapa='Colocar accesorios' THEN 5
          WHEN etapa='Armar ejes' THEN 5
          WHEN etapa='Armar jalon' THEN 5
          WHEN etapa='Armar barra' THEN 5
          WHEN etapa='Armar chasis de bomba y motor' THEN 5
          WHEN etapa='Montar bomba y motor' THEN 5
          WHEN etapa='Montar accesorios' THEN 5
          WHEN etapa='Pintar' THEN 3
          WHEN etapa='Instalacion electrica' THEN 2
          WHEN etapa='Checar y tapar fugas' THEN 5
          WHEN etapa='Probar equipo' THEN 5
          ELSE 0 END
      ) AS avance FROM avance_bachadora WHERE id_maquinaria=$id");
      $avance = $q && ($r = $q->fetch_assoc()) ? intval($r['avance']) : 0;
    } elseif ($subtipo === 'esparcidor de sello') {
      $q = $conn->query("SELECT SUM(
        CASE 
          WHEN etapa='Trazar,cortar,rolar y hacer ceja a tapas' THEN 5
          WHEN etapa='Trazar,cortar,rolar cuerpo' THEN 5
          WHEN etapa='Armar cuerpo' THEN 5
          WHEN etapa='Armar chasis' THEN 5
          WHEN etapa='Armar flux' THEN 5
          WHEN etapa='Colocar chasis y flux' THEN 5
          WHEN etapa='Colocar tapas y tubulares' THEN 5
          WHEN etapa='Colocar fibra de vidrio y lamina A.I' THEN 10
          WHEN etapa='Colocar accesorios' THEN 5
          WHEN etapa='Armar cajas negras y de controles' THEN 5
          WHEN etapa='Cortar, doblar y armar tolva' THEN 5
          WHEN etapa='Doblar, armar y colocar cabezal' THEN 5
          WHEN etapa='Doblar,armar,probar y colocar tanque de aceite' THEN 5
          WHEN etapa='Armar bomba' THEN 5
          WHEN etapa='Armar transportadores' THEN 3
          WHEN etapa='Pintar' THEN 2
          WHEN etapa='Colocar hidráulico y neumático' THEN 4
          WHEN etapa='Conectar eléctrico' THEN 3
          WHEN etapa='Colocar accesorios finales' THEN 2
          WHEN etapa='Prueba de equipo final' THEN 5
          ELSE 0 END
      ) AS avance FROM avance_esparcidor WHERE id_maquinaria=$id");
      $avance = $q && ($r = $q->fetch_assoc()) ? intval($r['avance']) : 0;
    } elseif ($subtipo === 'petrolizadora') {
      $q = $conn->query("SELECT SUM(
        CASE 
          WHEN etapa='Trazar,cortar,rolar y hacer ceja a tapas' THEN 5
          WHEN etapa='Trazar,cortar,rolar cuerpo' THEN 5
          WHEN etapa='Armar cuerpo' THEN 5
          WHEN etapa='Armar chasis' THEN 5
          WHEN etapa='Armar flux' THEN 5
          WHEN etapa='Colocar chasis y flux' THEN 5
          WHEN etapa='Colocar tapas y tubulares' THEN 5
          WHEN etapa='Colocar fibra de vidrio y lamina A.I' THEN 10
          WHEN etapa='Colocar accesorios tanque' THEN 5
          WHEN etapa='Armar y colocar barra' THEN 5
          WHEN etapa='Armar y colocar chasis p/bomba y motor' THEN 5
          WHEN etapa='Armar,alinear motor y bomba' THEN 5
          WHEN etapa='Montar alinear motor' THEN 5
          WHEN etapa='Armar tuberia interna y externa' THEN 5
          WHEN etapa='Alinear y colocar tuberias' THEN 5
          WHEN etapa='Colocar accesorios petrolizadora' THEN 5
          WHEN etapa='Pintura' THEN 5
          WHEN etapa='Intalacion electrica' THEN 5
          WHEN etapa='Probar y checar fugas' THEN 5
          ELSE 0 END
      ) AS avance FROM avance_petrolizadora WHERE id_maquinaria=$id");
      $avance = $q && ($r = $q->fetch_assoc()) ? intval($r['avance']) : 0;
    }
  }
?>
<tr>
  <td>
    <?php if (!empty($fila['imagen'])): ?>
      <img src="imagenes/<?=htmlspecialchars($fila['imagen'])?>" class="imagen-thumbnail">
    <?php else: ?>Sin imagen<?php endif; ?>
  </td>
  <td><?=htmlspecialchars($fila['nombre'])?></td>
  <td><?=htmlspecialchars($fila['modelo'])?></td>
  <td><?=htmlspecialchars($fila['ubicacion'])?></td>
  <td><?= $tipo_maq === 'nueva' ? '<span class="badge-nueva">Nueva</span>' : 'Usada' ?></td>
  <td><?=htmlspecialchars($fila['subtipo'])?></td>
  <td>
    <?php if($tipo_maq==='usada'): ?>
      <div class="progress"><div class="progress-bar" style="width:<?=intval($fila['condicion_estimada'])?>%"><?=intval($fila['condicion_estimada'])?>%</div></div>
    <?php else: ?>
      <div class="progress"><div class="progress-bar" style="width:<?=$avance?>%"><?=$avance?>%</div></div>
    <?php endif; ?>
  </td>
  <td><?= nl2br(htmlspecialchars($fila['observaciones'] ?? '')) ?></td>
  <td>
    <a href="editar_maquinaria.php?id=<?=$id?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
    <a href="eliminar_maquinaria.php?id=<?=$id?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')"><i class="bi bi-trash"></i></a>
    <?php if($tipo_maq==='usada'): ?>
      <a href="acciones/recibo_unidad.php?id=<?=$id?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-file-earmark-text"></i></a>
    <?php elseif($tipo_maq==='nueva' && in_array($subtipo,['bachadora','esparcidor de sello','petrolizadora'])): ?>
      <?php 
        $map = [
          'bachadora'=>'avance_bachadora.php',
          'esparcidor de sello'=>'avance_esparcidor.php',
          'petrolizadora'=>'avance_petrolizadora.php'
        ];
      ?>
      <a href="<?=$map[$subtipo]?>?id=<?=$id?>" class="btn btn-sm btn-outline-success"><i class="bi bi-bar-chart-line"></i></a>
    <?php endif;?>
  </td>
</tr>
<?php endwhile; ?>
    </tbody>
  </table>
</div>
<a href="exportar_excel.php" class="btn-flotante">📥 Exportar a Excel</a>
</body>
</html>
