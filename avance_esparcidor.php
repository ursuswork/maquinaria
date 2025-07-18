<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) {
  die("ID inválido");
}

// Verificar si existe el registro, si no, crear
$existe = $conn->query("SELECT id FROM avance_esparcidor WHERE id_maquinaria = $id_maquinaria LIMIT 1")->fetch_assoc();
if (!$existe) {
  $conn->query("INSERT INTO avance_esparcidor (id_maquinaria) VALUES ($id_maquinaria)");
}

// Manejo de actualización por botón
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actividad'])) {
  $actividad = $conn->real_escape_string($_POST['actividad']);
  $porcentaje = intval($_POST['porcentaje']);
  $conn->query("UPDATE avance_esparcidor SET `$actividad` = $porcentaje WHERE id_maquinaria = $id_maquinaria");
  echo json_encode(['ok' => true]);
  exit;
}

$secciones = [
  'ARMAR TANQUE' => [
    ["Trazar,cortar,rolar y hacer ceja a tapas", 5],
    ["Trazar,cortar,rolar cuerpo", 5],
    ["Armar cuerpo", 5],
    ["Armar chasis", 5],
    ["Armar flux", 5],
    ["Colocar chasis y flux", 5],
    ["Colocar tapas y tubulares", 5],
    ["Colocar fibra de vidrio y lamina A.I", 10],
    ["Colocar accesorios", 5]
  ],
  'ESPARCIDOR' => [
    ["Armar cajas negras y de controles", 55],
    ["Armar chasis", 60],
    ["Cortar, doblar y armar tolva", 65],
    ["Doblar, armar y colocar cabezal", 70],
    ["Doblar,armar,probar y colocar tanque de aceite", 75],
    ["Armar bomba", 80],
    ["Armar transportadores", 83],
    ["Pintar", 85],
    ["Colocar hidráulico y neumático", 89],
    ["Conectar eléctrico", 92],
    ["Colocar accesorios finales", 95],
    ["Prueba de equipo final", 100]
  ]
];

$actuales = $conn->query("SELECT * FROM avance_esparcidor WHERE id_maquinaria = $id_maquinaria")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Avance Esparcidor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .btn-etapa.active {
      background-color: #0d6efd !important;
      color: white !important;
    }
  </style>
</head>
<body class="bg-light p-4">
  <div class="container">
    <h3 class="text-center mb-4 text-primary">Avance - Esparcidor de Sello</h3>
    <?php foreach ($secciones as $titulo => $actividades): ?>
      <h5 class="mt-4">Sección: <?= htmlspecialchars($titulo) ?></h5>
      <div class="row">
        <?php foreach ($actividades as [$nombre, $porcentaje]): 
          $campo = strtolower(preg_replace('/[^a-z0-9]/i', '_', $nombre));
          $estado = intval($actuales[$campo] ?? 0);
        ?>
        <div class="col-md-6 mb-3">
          <div class="card p-3 shadow-sm">
            <strong><?= htmlspecialchars($nombre) ?></strong>
            <div class="mt-2">
              <button class="btn btn-sm btn-outline-success btn-etapa <?= $estado == $porcentaje ? 'active' : '' ?>" 
                onclick="marcarEtapa('<?= $campo ?>', <?= $porcentaje ?>, this)">
                <?= $porcentaje ?>%</button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    <a href="../inventario.php" class="btn btn-secondary mt-4">← Volver al Inventario</a>
  </div>

  <script>
    function marcarEtapa(actividad, porcentaje, btn) {
      fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `actividad=${actividad}&porcentaje=${porcentaje}`
      })
      .then(r => r.json())
      .then(d => {
        if (d.ok) {
          document.querySelectorAll('.btn-etapa').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
        }
      });
    }
  </script>
</body>
</html>
