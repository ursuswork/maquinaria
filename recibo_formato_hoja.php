<?php
include 'conexion.php';
$id = $_GET['id'] ?? null;
if (!$id) { die("ID no proporcionado."); }
$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id")->fetch_assoc();
$recibo = $conn->query("SELECT * FROM recibo_unidad WHERE maquinaria_id = $id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Unidad</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #fff; color: #333; }
        h2 { text-align: center; }
        .datos { margin-bottom: 20px; }
        .datos label { font-weight: bold; width: 150px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #999; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
        .seccion { background: #ccc; font-weight: bold; text-align: center; }
        .btn-imprimir { margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
<h2>Recibo de Unidad</h2>

<div class="datos">
    <p><label>Nombre:</label> <?= $maquinaria["nombre"] ?></p>
    <p><label>Modelo:</label> <?= $maquinaria["modelo"] ?></p>
    <p><label>Año:</label> <?= $maquinaria["anio"] ?></p>
    <p><label>Ubicación:</label> <?= $maquinaria["ubicacion"] ?></p>
</div>

<table>
    <tr><th colspan="2" class="seccion">MOTOR</th></tr>
<?php
$componentes_motor = ["cilindros", "pistones", "anillos", "inyectores", "block", "cabeza", "varillas", "resortes", "punterias", "cigueñal", "arbol_de_elevas", "retenes", "ligas", "sensores", "poleas", "concha", "cremallera", "clutch", "coples", "bomba_de_inyeccion", "juntas", "marcha", "tuberia", "alternador", "filtros", "bases", "soportes", "turbo", "escape", "chicotes"];
foreach ($componentes_motor as $comp) {
    echo "<tr><td>".strtoupper(str_replace("_", " ", $comp))."</td><td>{$recibo[$comp]}</td></tr>";
}
?>
<tr><th colspan="2" class="seccion">MECÁNICO</th></tr>
<?php
$mecanico = ["transmision", "diferenciales", "cardan"];
foreach ($mecanico as $comp) {
    echo "<tr><td>".strtoupper($comp)."</td><td>{$recibo[$comp]}</td></tr>";
}
?>
<tr><th colspan="2" class="seccion">HIDRÁULICO</th></tr>
<?php
$hidraulico = ["banco_de_valvulas", "bombas_de_transito", "bombas_de_precarga", "bombas_de_accesorios", "coples_hidraulicos", "clutch_hidraulico", "gatos_de_levante", "gatos_de_direccion", "gatos_de_accesorios", "mangueras", "motores_hidraulicos", "orbitrol", "torques_huv", "valvulas_de_retencion", "reductores"];
foreach ($hidraulico as $comp) {
    echo "<tr><td>".strtoupper(str_replace("_", " ", $comp))."</td><td>{$recibo[$comp]}</td></tr>";
}
?>
<tr><th colspan="2" class="seccion">ELÉCTRICO Y ELECTRÓNICO</th></tr>
<?php
$electrico = ["alarmas", "arneses", "bobinas", "botones", "cables", "cables_de_sensores", "conectores", "electro_valvulas", "fusibles", "porta_fusibles", "indicadores", "presion_agua_temp_voltimetro", "luces", "modulos", "torreta", "relevadores", "switch_llave", "sensores_electricos"];
foreach ($electrico as $comp) {
    echo "<tr><td>".strtoupper(str_replace("_", " ", $comp))."</td><td>{$recibo[$comp]}</td></tr>";
}
?>
<tr><th colspan="2" class="seccion">ESTÉTICO</th></tr>
<?php
$estetico = ["pintura", "calcomanias", "asiento", "tapiceria", "tolvas", "cristales", "accesorios", "sistema_de_riego"];
foreach ($estetico as $comp) {
    echo "<tr><td>".strtoupper(str_replace("_", " ", $comp))."</td><td>{$recibo[$comp]}</td></tr>";
}
?>
<tr><th colspan="2" class="seccion">CONSUMIBLES</th></tr>
<?php
$consumibles = ["puntas", "porta_puntas", "garras", "cuchillas", "cepillos", "separadores", "llantas", "rines", "bandas_orugas"];
foreach ($consumibles as $comp) {
    echo "<tr><td>".strtoupper(str_replace("_", " ", $comp))."</td><td>{$recibo[$comp]}</td></tr>";
}
?>
</table>

<button onclick="window.print()" class="btn-imprimir">Imprimir</button>
</body>
</html>
