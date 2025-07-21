<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}
include '../conexion.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("❌ ID inválido.");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id")->fetch_assoc();
$recibo = $conn->query("SELECT * FROM recibo_unidad WHERE id_maquinaria = $id")->fetch_assoc();

if (!$maquinaria || !$recibo) {
    die("❌ Datos no encontrados.");
}

// Cabeceras para Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=recibo_maquinaria_{$id}.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Columnas
echo "<table border='1'>";
echo "<tr><th colspan='2'>Datos Generales</th></tr>";
echo "<tr><td><b>Equipo</b></td><td>{$maquinaria['nombre']}</td></tr>";
echo "<tr><td><b>Marca</b></td><td>{$maquinaria['marca']}</td></tr>";
echo "<tr><td><b>Modelo</b></td><td>{$maquinaria['modelo']}</td></tr>";
echo "<tr><td><b>Empresa Origen</b></td><td>{$recibo['empresa_origen']}</td></tr>";
echo "<tr><td><b>Empresa Destino</b></td><td>{$recibo['empresa_destino']}</td></tr>";
echo "<tr><td><b>Condición Estimada</b></td><td>{$recibo['condicion_estimada']}%</td></tr>";
echo "</table><br>";

$secciones = [
  "MOTOR" => [...],
  "SISTEMA MECÁNICO" => [...],
  "SISTEMA HIDRÁULICO" => [...],
  "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => [...],
  "ESTÉTICO" => [...],
  "CONSUMIBLES" => [...]
];

// Componentes de cada sección
$secciones["MOTOR"] = ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"];
$secciones["SISTEMA MECÁNICO"] = ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"];
$secciones["SISTEMA HIDRÁULICO"] = ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "COPLES", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"];
$secciones["SISTEMA ELÉCTRICO Y ELECTRÓNICO"] = ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)", "SENSORES"];
$secciones["ESTÉTICO"] = ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"];
$secciones["CONSUMIBLES"] = ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"];

// Mostrar componentes
foreach ($secciones as $titulo => $componentes) {
    echo "<table border='1'>";
    echo "<tr><th colspan='2'>{$titulo}</th></tr>";
    foreach ($componentes as $componente) {
        $valor = $recibo[$componente] ?? '';
        echo "<tr><td>{$componente}</td><td>{$valor}</td></tr>";
    }
    echo "</table><br>";
}

// Observaciones
echo "<table border='1'>";
echo "<tr><th>Observaciones</th></tr>";
echo "<tr><td>" . nl2br(htmlspecialchars($recibo['observaciones'] ?? '')) . "</td></tr>";
echo "</table>";
