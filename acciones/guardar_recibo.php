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
    die("❌ ID inválido.");
  }

  $componentes = $_POST['componentes'] ?? [];
  $observaciones = $conn->real_escape_string($_POST['observaciones'] ?? '');
  $condicion_total = 0;

  // Definir pesos por sección
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

  // Calcular peso por componente
  $porcentajes = [];
  foreach ($secciones as $seccion => $items) {
    $peso_unitario = $pesos[$seccion] / count($items);
    foreach ($items as $item) {
      $porcentajes[$item] = $peso_unitario;
    }
  }

  // Ver si ya existe un recibo
  $check = $conn->query("SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1");
  $existe = $check->fetch_assoc();

  if ($existe) {
    // Actualizar recibo existente
    $updates = [];
    foreach ($componentes as $campo => $valor) {
      $campo_sql = $conn->real_escape_string($campo);
      $valor_sql = $conn->real_escape_string($valor);
      $updates[] = "`$campo_sql` = '$valor_sql'";
      if ($valor === 'bueno') {
        $condicion_total += $porcentajes[$campo] ?? 0;
      }
    }
    $updates[] = "`observaciones` = '$observaciones'";
    $updates[] = "`fecha` = CURRENT_DATE()";
    $conn->query("UPDATE recibo_unidad SET " . implode(',', $updates) . " WHERE id_maquinaria = $id_maquinaria");
  } else {
    // Insertar nuevo recibo
    $campos = [];
    $valores = [];
    foreach ($componentes as $campo => $valor) {
      $campo_sql = $conn->real_escape_string($campo);
      $valor_sql = $conn->real_escape_string($valor);
      $campos[] = "`$campo_sql`";
      $valores[] = "'$valor_sql'";
      if ($valor === 'bueno') {
        $condicion_total += $porcentajes[$campo] ?? 0;
      }
    }
    $campos[] = "id_maquinaria";
    $valores[] = $id_maquinaria;
    $campos[] = "observaciones";
    $valores[] = "'$observaciones'";
    $campos[] = "fecha";
    $valores[] = "CURRENT_DATE()";
    $conn->query("INSERT INTO recibo_unidad (" . implode(',', $campos) . ") VALUES (" . implode(',', $valores) . ")");
  }

  // Actualizar campo condicion_estimada en tabla maquinaria
  $condicion_total = round($condicion_total, 2);
  $conn->query("UPDATE maquinaria SET condicion_estimada = $condicion_total WHERE id = $id_maquinaria");

  // Redirigir
  header("Location: ../inventario.php");
  exit;
}
?>
