<?php
include 'conexion.php';
$id = $_GET['id'] ?? null;
if (!$id) { echo "ID no proporcionado."; exit; }
$sql = "SELECT * FROM recibo_unidad WHERE maquinaria_id = $id ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
function mostrar_valor($valor) {
    return ucfirst($valor ?? '-');
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Formato Recibo Unidad</title>
<style>
    body { font-family: Arial; margin: 40px; }
    .logo { text-align: center; margin-bottom: 20px; }
    .logo img { height: 80px; }
    h2 { text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 5px; text-align: left; font-size: 14px; }
    th { background-color: #f0f0f0; }
    .section { background-color: #ccc; font-weight: bold; text-align: center; }
    .print-btn { margin: 20px 0; }
</style>
</head>
<body>
<div class="logo"><img src="logo.png" alt="Logo"></div>
<h2>FORMATO DE RECIBO DE UNIDAD</h2>
<button class="print-btn" onclick="window.print()">🖨️ Imprimir</button>
<table>
<tr><td class='section' colspan='2'>MOTOR</td></tr>
<tr><td>CILINDROS: <?= mostrar_valor($data['cilindros']) ?></td><td>PISTONES: <?= mostrar_valor($data['pistones']) ?></td></tr>
<tr><td>ANILLOS: <?= mostrar_valor($data['anillos']) ?></td><td>INYECTORES: <?= mostrar_valor($data['inyectores']) ?></td></tr>
<tr><td>BLOCK: <?= mostrar_valor($data['block']) ?></td><td>CABEZA: <?= mostrar_valor($data['cabeza']) ?></td></tr>
<tr><td>VARILLAS: <?= mostrar_valor($data['varillas']) ?></td><td>RESORTES: <?= mostrar_valor($data['resortes']) ?></td></tr>
<tr><td>PUNTERIAS: <?= mostrar_valor($data['punterias']) ?></td><td>CIGUEÑAL: <?= mostrar_valor($data['cigueñal']) ?></td></tr>
<tr><td>ARBOL DE ELEVAS: <?= mostrar_valor($data['arbol_de_elevas']) ?></td><td>RETENES: <?= mostrar_valor($data['retenes']) ?></td></tr>
<tr><td>LIGAS: <?= mostrar_valor($data['ligas']) ?></td><td>SENSORES: <?= mostrar_valor($data['sensores']) ?></td></tr>
<tr><td>POLEAS: <?= mostrar_valor($data['poleas']) ?></td><td>CONCHA: <?= mostrar_valor($data['concha']) ?></td></tr>
<tr><td>CREMALLERA: <?= mostrar_valor($data['cremallera']) ?></td><td>CLUTCH: <?= mostrar_valor($data['clutch']) ?></td></tr>
<tr><td>COPLES: <?= mostrar_valor($data['coples']) ?></td><td>BOMBA DE INYECCION: <?= mostrar_valor($data['bomba_de_inyeccion']) ?></td></tr>
<tr><td>JUNTAS: <?= mostrar_valor($data['juntas']) ?></td><td>MARCHA: <?= mostrar_valor($data['marcha']) ?></td></tr>
<tr><td>TUBERIA: <?= mostrar_valor($data['tuberia']) ?></td><td>ALTERNADOR: <?= mostrar_valor($data['alternador']) ?></td></tr>
<tr><td>FILTROS: <?= mostrar_valor($data['filtros']) ?></td><td>BASES: <?= mostrar_valor($data['bases']) ?></td></tr>
<tr><td>SOPORTES: <?= mostrar_valor($data['soportes']) ?></td><td>TURBO: <?= mostrar_valor($data['turbo']) ?></td></tr>
<tr><td>ESCAPE: <?= mostrar_valor($data['escape']) ?></td><td>CHICOTES: <?= mostrar_valor($data['chicotes']) ?></td></tr>
<tr><td class='section' colspan='2'>MOTOR</td></tr>
<tr><td>CILINDROS: <?= mostrar_valor($data['cilindros']) ?></td><td>PISTONES: <?= mostrar_valor($data['pistones']) ?></td></tr>
<tr><td>ANILLOS: <?= mostrar_valor($data['anillos']) ?></td><td>INYECTORES: <?= mostrar_valor($data['inyectores']) ?></td></tr>
<tr><td>BLOCK: <?= mostrar_valor($data['block']) ?></td><td>CABEZA: <?= mostrar_valor($data['cabeza']) ?></td></tr>
<tr><td>VARILLAS: <?= mostrar_valor($data['varillas']) ?></td><td>RESORTES: <?= mostrar_valor($data['resortes']) ?></td></tr>
<tr><td>PUNTERIAS: <?= mostrar_valor($data['punterias']) ?></td><td>CIGUEÑAL: <?= mostrar_valor($data['cigueñal']) ?></td></tr>
<tr><td>ARBOL DE ELEVAS: <?= mostrar_valor($data['arbol_de_elevas']) ?></td><td>RETENES: <?= mostrar_valor($data['retenes']) ?></td></tr>
<tr><td>LIGAS: <?= mostrar_valor($data['ligas']) ?></td><td>SENSORES: <?= mostrar_valor($data['sensores']) ?></td></tr>
<tr><td>POLEAS: <?= mostrar_valor($data['poleas']) ?></td><td>CONCHA: <?= mostrar_valor($data['concha']) ?></td></tr>
<tr><td>CREMALLERA: <?= mostrar_valor($data['cremallera']) ?></td><td>CLUTCH: <?= mostrar_valor($data['clutch']) ?></td></tr>
<tr><td>COPLES: <?= mostrar_valor($data['coples']) ?></td><td>BOMBA DE INYECCION: <?= mostrar_valor($data['bomba_de_inyeccion']) ?></td></tr>
<tr><td>JUNTAS: <?= mostrar_valor($data['juntas']) ?></td><td>MARCHA: <?= mostrar_valor($data['marcha']) ?></td></tr>
<tr><td>TUBERIA: <?= mostrar_valor($data['tuberia']) ?></td><td>ALTERNADOR: <?= mostrar_valor($data['alternador']) ?></td></tr>
<tr><td>FILTROS: <?= mostrar_valor($data['filtros']) ?></td><td>BASES: <?= mostrar_valor($data['bases']) ?></td></tr>
<tr><td>SOPORTES: <?= mostrar_valor($data['soportes']) ?></td><td>TURBO: <?= mostrar_valor($data['turbo']) ?></td></tr>
<tr><td>ESCAPE: <?= mostrar_valor($data['escape']) ?></td><td>CHICOTES: <?= mostrar_valor($data['chicotes']) ?></td></tr>
<tr><td class='section' colspan='2'>MECANICO</td></tr>
<tr><td>TRANSMISION: <?= mostrar_valor($data['transmision']) ?></td><td>DIFERENCIALES: <?= mostrar_valor($data['diferenciales']) ?></td></tr>
<tr><td>CARDAN: <?= mostrar_valor($data['cardan']) ?></td><td>: </td></tr>
<tr><td class='section' colspan='2'>HIDRAULICO</td></tr>
<tr><td>BANCO DE VALVULAS: <?= mostrar_valor($data['banco_de_valvulas']) ?></td><td>BOMBAS DE TRANSITO: <?= mostrar_valor($data['bombas_de_transito']) ?></td></tr>
<tr><td>BOMBAS DE PRECARGA: <?= mostrar_valor($data['bombas_de_precarga']) ?></td><td>BOMBAS DE ACCESORIOS: <?= mostrar_valor($data['bombas_de_accesorios']) ?></td></tr>
<tr><td>COPLES HIDRAULICOS: <?= mostrar_valor($data['coples_hidraulicos']) ?></td><td>CLUTCH HIDRAULICO: <?= mostrar_valor($data['clutch_hidraulico']) ?></td></tr>
<tr><td>GATOS DE LEVANTE: <?= mostrar_valor($data['gatos_de_levante']) ?></td><td>GATOS DE DIRECCION: <?= mostrar_valor($data['gatos_de_direccion']) ?></td></tr>
<tr><td>GATOS DE ACCESORIOS: <?= mostrar_valor($data['gatos_de_accesorios']) ?></td><td>MANGUERAS: <?= mostrar_valor($data['mangueras']) ?></td></tr>
<tr><td>MOTORES HIDRAULICOS: <?= mostrar_valor($data['motores_hidraulicos']) ?></td><td>ORBITROL: <?= mostrar_valor($data['orbitrol']) ?></td></tr>
<tr><td>TORQUES HUV: <?= mostrar_valor($data['torques_huv']) ?></td><td>VALVULAS DE RETENCION: <?= mostrar_valor($data['valvulas_de_retencion']) ?></td></tr>
<tr><td>REDUCTORES: <?= mostrar_valor($data['reductores']) ?></td><td>: </td></tr>
<tr><td class='section' colspan='2'>ELECTRICO</td></tr>
<tr><td>ALARMAS: <?= mostrar_valor($data['alarmas']) ?></td><td>ARNESES: <?= mostrar_valor($data['arneses']) ?></td></tr>
<tr><td>BOBINAS: <?= mostrar_valor($data['bobinas']) ?></td><td>BOTONES: <?= mostrar_valor($data['botones']) ?></td></tr>
<tr><td>CABLES: <?= mostrar_valor($data['cables']) ?></td><td>CABLES DE SENSORES: <?= mostrar_valor($data['cables_de_sensores']) ?></td></tr>
<tr><td>CONECTORES: <?= mostrar_valor($data['conectores']) ?></td><td>ELECTRO VALVULAS: <?= mostrar_valor($data['electro_valvulas']) ?></td></tr>
<tr><td>FUSIBLES: <?= mostrar_valor($data['fusibles']) ?></td><td>PORTA FUSIBLES: <?= mostrar_valor($data['porta_fusibles']) ?></td></tr>
<tr><td>INDICADORES: <?= mostrar_valor($data['indicadores']) ?></td><td>PRESION AGUA TEMP VOLTIMETRO: <?= mostrar_valor($data['presion_agua_temp_voltimetro']) ?></td></tr>
<tr><td>LUCES: <?= mostrar_valor($data['luces']) ?></td><td>MODULOS: <?= mostrar_valor($data['modulos']) ?></td></tr>
<tr><td>TORRETA: <?= mostrar_valor($data['torreta']) ?></td><td>RELEVADORES: <?= mostrar_valor($data['relevadores']) ?></td></tr>
<tr><td>SWITCH LLAVE: <?= mostrar_valor($data['switch_llave']) ?></td><td>SENSORES ELECTRICOS: <?= mostrar_valor($data['sensores_electricos']) ?></td></tr>
<tr><td class='section' colspan='2'>ESTETICO</td></tr>
<tr><td>PINTURA: <?= mostrar_valor($data['pintura']) ?></td><td>CALCOMANIAS: <?= mostrar_valor($data['calcomanias']) ?></td></tr>
<tr><td>ASIENTO: <?= mostrar_valor($data['asiento']) ?></td><td>TAPICERIA: <?= mostrar_valor($data['tapiceria']) ?></td></tr>
<tr><td>TOLVAS: <?= mostrar_valor($data['tolvas']) ?></td><td>CRISTALES: <?= mostrar_valor($data['cristales']) ?></td></tr>
<tr><td>ACCESORIOS: <?= mostrar_valor($data['accesorios']) ?></td><td>SISTEMA DE RIEGO: <?= mostrar_valor($data['sistema_de_riego']) ?></td></tr>
<tr><td class='section' colspan='2'>CONSUMIBLES</td></tr>
<tr><td>PUNTAS: <?= mostrar_valor($data['puntas']) ?></td><td>PORTA PUNTAS: <?= mostrar_valor($data['porta_puntas']) ?></td></tr>
<tr><td>GARRAS: <?= mostrar_valor($data['garras']) ?></td><td>CUCHILLAS: <?= mostrar_valor($data['cuchillas']) ?></td></tr>
<tr><td>CEPILLOS: <?= mostrar_valor($data['cepillos']) ?></td><td>SEPARADORES: <?= mostrar_valor($data['separadores']) ?></td></tr>
<tr><td>LLANTAS: <?= mostrar_valor($data['llantas']) ?></td><td>RINES: <?= mostrar_valor($data['rines']) ?></td></tr>
<tr><td>BANDAS ORUGAS: <?= mostrar_valor($data['bandas_orugas']) ?></td><td>: </td></tr>
</table>
</body>
</html>