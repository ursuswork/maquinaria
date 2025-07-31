<?php
session_start();
include '../conexion.php';

$id_maquinaria = intval($_GET['id'] ?? 0);
if ($id_maquinaria <= 0) die("ID inválido");

$componentes = $_POST['componentes'] ?? [];
$observaciones = $conn->real_escape_string($_POST['observaciones'] ?? '');
$empresa_origen = $conn->real_escape_string($_POST['empresa_origen'] ?? '');
$empresa_destino = $conn->real_escape_string($_POST['empresa_destino'] ?? '');

// Definir pesos y secciones igual que en el formulario
$secciones = [
  "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
  "SISTEMA MECANICO" => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
  "SISTEMA HIDRAULICO" => ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"],
  "SISTEMA ELECTRICO Y ELECTRONICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)"],
  "ESTETICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
  "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
];
$pesos = ["MOTOR"=>15,"SISTEMA MECANICO"=>15,"SISTEMA HIDRAULICO"=>30,"SISTEMA ELECTRICO Y ELECTRONICO"=>25,"ESTETICO"=>5,"CONSUMIBLES"=>10];

// Calcula el avance
$total_avance = 0;
foreach ($secciones as $seccion => $items) {
    $buenos = 0;
    foreach ($items as $item) {
        if (isset($componentes[$item]) && $componentes[$item] === 'bueno') {
            $buenos++;
        }
    }
    $peso = $pesos[$seccion];
    $porcentaje = ($buenos / count($items)) * $peso;
    $total_avance += $porcentaje;
}
$condicion_estimada = round($total_avance);

// Prepara query de campos y valores
$set = [];
foreach ($secciones as $seccion => $items) {
    foreach ($items as $item) {
        $k = $conn->real_escape_string($item);
        $v = isset($componentes[$item]) ? $conn->real_escape_string($componentes[$item]) : '';
        $set[] = "`$k` = '$v'";
    }
}
$set[] = "condicion_estimada = $condicion_estimada";
$set[] = "observaciones = '$observaciones'";
$set[] = "empresa_origen = '$empresa_origen'";
$set[] = "empresa_destino = '$empresa_destino'";
$set[] = "fecha = NOW()";

// ¿Ya existe?
$existe = $conn->query("SELECT 1 FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria")->num_rows > 0;
if ($existe) {
    $sql = "UPDATE recibo_unidad SET " . implode(",", $set) . " WHERE id_maquinaria = $id_maquinaria";
} else {
    $sql = "INSERT INTO recibo_unidad SET id_maquinaria = $id_maquinaria, " . implode(",", $set);
}

if ($conn->query($sql)) {
   header("Location: ../inventario.php");
   exit;
} else {
    echo "Error al guardar: " . $conn->error;
}
?>
