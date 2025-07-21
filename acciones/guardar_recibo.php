
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_maquinaria = intval($_POST['id_maquinaria']);
  $empresa_origen = $conn->real_escape_string($_POST['empresa_origen'] ?? '');
  $empresa_destino = $conn->real_escape_string($_POST['empresa_destino'] ?? '');
  $observaciones = $conn->real_escape_string($_POST['observaciones'] ?? '');
  $componentes = $_POST['componentes'] ?? [];

  // Definir secciones y pesos
  $secciones = [
    "MOTOR" => 15,
    "SISTEMA MECÁNICO" => 15,
    "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => 25,
    "SISTEMA HIDRÁULICO" => 30,
    "ESTÉTICO" => 5,
    "CONSUMIBLES" => 10
  ];

  // Componentes por sección
  $lista_componentes = [
    "MOTOR" => ["CILINDROS","PISTONES","ANILLOS","INYECTORES","BLOCK","CABEZA","VARILLAS","RESORTES","PUNTERIAS","CIGÜEÑAL","ARBOL DE ELEVAS","RETENES","LIGAS","SENSORES","POLEAS","CONCHA","CREMAYERA","CLUTCH","COPLES","BOMBA DE INYECCION","JUNTAS","MARCHA","TUBERIA","ALTERNADOR","FILTROS","BASES","SOPORTES","TURBO","ESCAPE","CHICOTES"],
    "SISTEMA MECÁNICO" => ["TRANSMISIÓN","DIFERENCIALES","CARDÁN"],
    "SISTEMA HIDRÁULICO" => ["BANCO DE VÁLVULAS","BOMBAS DE TRANSITO","BOMBAS DE PRECARGA","BOMBAS DE ACCESORIOS","COPLES","CLUTCH HIDRÁULICO","GATOS DE LEVANTE","GATOS DE DIRECCIÓN","GATOS DE ACCESORIOS","MANGUERAS","MOTORES HIDRÁULICOS","ORBITROL","TORQUES HUV (SATÉLITES)","VÁLVULAS DE RETENCIÓN","REDUCTORES"],
    "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => ["ALARMAS","ARNESES","BOBINAS","BOTONES","CABLES","CABLES DE SENSORES","CONECTORES","ELECTRO VÁLVULAS","FUSIBLES","PORTA FUSIBLES","INDICADORES","PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO","LUCES","MÓDULOS","TORRETA","RELEVADORES","SWITCH (LLAVE)","SENSORES"],
    "ESTÉTICO" => ["PINTURA","CALCOMANIAS","ASIENTO","TAPICERIA","TOLVAS","CRISTALES","ACCESORIOS","SISTEMA DE RIEGO"],
    "CONSUMIBLES" => ["PUNTAS","PORTA PUNTAS","GARRAS","CUCHILLAS","CEPILLOS","SEPARADORES","LLANTAS","RINES","BANDAS / ORUGAS"]
  ];

  // Calcular condición
  $total_porcentaje = 0;
  foreach ($secciones as $seccion => $peso) {
    $componentes_seccion = $lista_componentes[$seccion];
    $total = count($componentes_seccion);
    $acumulado = 0;
    foreach ($componentes_seccion as $nombre) {
      $estado = $componentes[$nombre] ?? 'regular';
      $valor = ($estado === 'bueno') ? 1 : (($estado === 'regular') ? 0.5 : 0);
      $acumulado += $valor;
    }
    $porcentaje_seccion = ($acumulado / $total) * $peso;
    $total_porcentaje += $porcentaje_seccion;
  }

  // Verificar si ya existe
  $existe = $conn->query("SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria")->fetch_assoc();
  $query = $existe ?
    "UPDATE recibo_unidad SET empresa_origen='$empresa_origen', empresa_destino='$empresa_destino', observaciones='$observaciones', condicion_estimada=$total_porcentaje WHERE id_maquinaria=$id_maquinaria"
    :
    "INSERT INTO recibo_unidad (id_maquinaria, empresa_origen, empresa_destino, observaciones, condicion_estimada) VALUES ($id_maquinaria, '$empresa_origen', '$empresa_destino', '$observaciones', $total_porcentaje)";
  $conn->query($query);

  foreach ($componentes as $nombre => $valor) {
    $conn->query("UPDATE recibo_unidad SET `$nombre` = '{$conn->real_escape_string($valor)}' WHERE id_maquinaria = $id_maquinaria");
  }

  $conn->query("UPDATE maquinaria SET condicion_estimada = $total_porcentaje WHERE id = $id_maquinaria");

  header("Location: recibo_unidad.php?id=$id_maquinaria");
  exit;
}
?>
