<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $id_maquinaria = intval($_POST['id_maquinaria'] ?? 0);
  if ($id_maquinaria <= 0) {
    die("Error: ID inválido.");
  }

  $componentes = $_POST['componentes'] ?? [];
  $observaciones = $conn->real_escape_string($_POST['observaciones'] ?? '');
  $avance_total = 0;

  // Pesos por sección
  $pesos = [
    "MOTOR" => 15,
    "SISTEMA MECANICO" => 15,
    "SISTEMA HIDRAULICO" => 30,
    "SISTEMA ELECTRICO Y ELECTRONICO" => 25,
    "ESTETICO" => 5,
    "CONSUMIBLES" => 10
  ];

  // Componentes por sección
  $secciones = [
    "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGUEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
    "SISTEMA MECANICO" => ["TRANSMISION", "DIFERENCIALES", "CARDAN"],
    "SISTEMA HIDRAULICO" => ["BANCO DE VALVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRAULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCION", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRAULICOS", "ORBITROL", "TORQUES HUV (SATELITES)", "VALVULAS DE RETENCION", "REDUCTORES"],
    "SISTEMA ELECTRICO Y ELECTRONICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VALVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESION/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MODULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)"],
    "ESTETICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
    "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
  ];

  // Calcular porcentaje por componente
  $porcentajes = [];
  foreach ($secciones as $seccion => $items) {
    $peso_unitario = $pesos[$seccion] / count($items);
    foreach ($items as $item) {
      $porcentajes[$item] = $peso_unitario;
    }
  }

  // Verificar si ya existe registro
  $sql_check = $conn->query("SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria");
  $existe = $sql_check->fetch_assoc();

  if ($existe) {
    $updates = [];
    foreach ($componentes as $campo => $valor) {
      $campo_sql = $conn->real_escape_string($campo);
      $valor_sql = $conn->real_escape_string($valor);
      $updates[] = "`$campo_sql` = '$valor_sql'";
      if ($valor === 'bueno') {
        $avance_total += $porcentajes[$campo] ?? 0;
      }
    }
    $updates[] = "`observaciones` = '$observaciones'";
    $conn->query("UPDATE recibo_unidad SET " . implode(', ', $updates) . " WHERE id_maquinaria = $id_maquinaria");
  } else {
    $campos = [];
    $valores = [];
    foreach ($componentes as $campo => $valor) {
      $campo_sql = $conn->real_escape_string($campo);
      $valor_sql = $conn->real_escape_string($valor);
      $campos[] = "`$campo_sql`";
      $valores[] = "'$valor_sql'";
      if ($valor === 'bueno') {
        $avance_total += $porcentajes[$campo] ?? 0;
      }
    }
    $campos[] = "`id_maquinaria`";
    $valores[] = $id_maquinaria;
    $campos[] = "`observaciones`";
    $valores[] = "'$observaciones'";
    $conn->query("INSERT INTO recibo_unidad (" . implode(',', $campos) . ") VALUES (" . implode(',', $valores) . ")");
  }

  $avance_total = round($avance_total, 2);
  $conn->query("UPDATE maquinaria SET condicion_estimada = $avance_total WHERE id = $id_maquinaria");

  header("Location: ../inventario.php");
  exit;
}
?>