<?php
include 'conexion.php';
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID no proporcionado.";
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campos = [];
    $valores = [];
    foreach ($_POST as $campo => $valor) {
        $campos[] = $campo;
        $valores[] = "'" . $conn->real_escape_string($valor) . "'";
    }
    $campos[] = "maquinaria_id";
    $valores[] = $id;
    $sql = "INSERT INTO recibo_unidad (" . implode(",", $campos) . ") VALUES (" . implode(",", $valores) . ")";
    if ($conn->query($sql)) {
        // Calcular condición
        function valor_numerico($v) {
            if ($v == 'bueno') return 100;
            if ($v == 'regular') return 50;
            if ($v == 'malo') return 0;
            return null;
        }
        $componentes = {"motor": ["cilindros", "pistones", "anillos", "inyectores", "block", "cabeza", "varillas", "resortes", "punterias", "cigueñal", "arbol_de_elevas", "retenes", "ligas", "sensores", "poleas", "concha", "cremallera", "clutch", "coples", "bomba_de_inyeccion", "juntas", "marcha", "tuberia", "alternador", "filtros", "bases", "soportes", "turbo", "escape", "chicotes"], "mecanico": ["transmision", "diferenciales", "cardan"], "hidraulico": ["banco_de_valvulas", "bombas_de_transito", "bombas_de_precarga", "bombas_de_accesorios", "coples_hidraulicos", "clutch_hidraulico", "gatos_de_levante", "gatos_de_direccion", "gatos_de_accesorios", "mangueras", "motores_hidraulicos", "orbitrol", "torques_huv", "valvulas_de_retencion", "reductores"], "electrico": ["alarmas", "arneses", "bobinas", "botones", "cables", "cables_de_sensores", "conectores", "electro_valvulas", "fusibles", "porta_fusibles", "indicadores", "presion_agua_temp_voltimetro", "luces", "modulos", "torreta", "relevadores", "switch_llave", "sensores_electricos"], "estetico": ["pintura", "calcomanias", "asiento", "tapiceria", "tolvas", "cristales", "accesorios", "sistema_de_riego"], "consumibles": ["puntas", "porta_puntas", "garras", "cuchillas", "cepillos", "separadores", "llantas", "rines", "bandas_orugas"]};
        $pesos = ['motor'=>15,'mecanico'=>15,'hidraulico'=>30,'electrico'=>25,'estetico'=>5,'consumibles'=>10];
        $total = 0;
        foreach ($pesos as $seccion => $peso) {
            $lista = $componentes[$seccion];
            $suma = 0;
            $validos = 0;
            foreach ($lista as $campo) {
                if (isset($_POST[$campo])) {
                    $v = valor_numerico($_POST[$campo]);
                    if (!is_null($v)) {
                        $suma += $v;
                        $validos++;
                    }
                }
            }
            if ($validos > 0) {
                $prom = $suma / $validos;
                $total += ($prom * $peso) / 100;
            }
        }
        $condicion = round($total, 2);
        $conn->query("UPDATE maquinaria SET condicion_estimada = $condicion WHERE id = $id");
        header("Location: recibo_formato_hoja.php?id=" . $id);
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recibo Unidad</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        h3 { margin-top: 30px; background: #f0f0f0; padding: 10px; }
        form { display: flex; flex-wrap: wrap; }
        .campo { width: 48%; margin: 1%; background: #fafafa; padding: 10px; border: 1px solid #ccc; border-radius: 6px; }
        .campo label { display: block; font-weight: bold; margin-bottom: 5px; }
        .opciones label { display: inline-block; margin-right: 10px; font-weight: normal; }
        .submit-btn { width: 100%; margin-top: 30px; padding: 10px; font-size: 16px; }
    </style>
</head>
<body>
<h2>Recibo de Unidad (Maquinaria ID: <?= $id ?>)</h2>
<form method="POST">
<h3>MOTOR</h3>

<div class="campo">
    <label>CILINDROS:</label>
    <div class="opciones">
        <label><input type="radio" name="cilindros" value="bueno" required> Bueno</label>
        <label><input type="radio" name="cilindros" value="regular"> Regular</label>
        <label><input type="radio" name="cilindros" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>PISTONES:</label>
    <div class="opciones">
        <label><input type="radio" name="pistones" value="bueno" required> Bueno</label>
        <label><input type="radio" name="pistones" value="regular"> Regular</label>
        <label><input type="radio" name="pistones" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>ANILLOS:</label>
    <div class="opciones">
        <label><input type="radio" name="anillos" value="bueno" required> Bueno</label>
        <label><input type="radio" name="anillos" value="regular"> Regular</label>
        <label><input type="radio" name="anillos" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>INYECTORES:</label>
    <div class="opciones">
        <label><input type="radio" name="inyectores" value="bueno" required> Bueno</label>
        <label><input type="radio" name="inyectores" value="regular"> Regular</label>
        <label><input type="radio" name="inyectores" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>BLOCK:</label>
    <div class="opciones">
        <label><input type="radio" name="block" value="bueno" required> Bueno</label>
        <label><input type="radio" name="block" value="regular"> Regular</label>
        <label><input type="radio" name="block" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CABEZA:</label>
    <div class="opciones">
        <label><input type="radio" name="cabeza" value="bueno" required> Bueno</label>
        <label><input type="radio" name="cabeza" value="regular"> Regular</label>
        <label><input type="radio" name="cabeza" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>VARILLAS:</label>
    <div class="opciones">
        <label><input type="radio" name="varillas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="varillas" value="regular"> Regular</label>
        <label><input type="radio" name="varillas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>RESORTES:</label>
    <div class="opciones">
        <label><input type="radio" name="resortes" value="bueno" required> Bueno</label>
        <label><input type="radio" name="resortes" value="regular"> Regular</label>
        <label><input type="radio" name="resortes" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>PUNTERIAS:</label>
    <div class="opciones">
        <label><input type="radio" name="punterias" value="bueno" required> Bueno</label>
        <label><input type="radio" name="punterias" value="regular"> Regular</label>
        <label><input type="radio" name="punterias" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CIGUEÑAL:</label>
    <div class="opciones">
        <label><input type="radio" name="cigueñal" value="bueno" required> Bueno</label>
        <label><input type="radio" name="cigueñal" value="regular"> Regular</label>
        <label><input type="radio" name="cigueñal" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>ARBOL DE ELEVAS:</label>
    <div class="opciones">
        <label><input type="radio" name="arbol_de_elevas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="arbol_de_elevas" value="regular"> Regular</label>
        <label><input type="radio" name="arbol_de_elevas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>RETENES:</label>
    <div class="opciones">
        <label><input type="radio" name="retenes" value="bueno" required> Bueno</label>
        <label><input type="radio" name="retenes" value="regular"> Regular</label>
        <label><input type="radio" name="retenes" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>LIGAS:</label>
    <div class="opciones">
        <label><input type="radio" name="ligas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="ligas" value="regular"> Regular</label>
        <label><input type="radio" name="ligas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>SENSORES:</label>
    <div class="opciones">
        <label><input type="radio" name="sensores" value="bueno" required> Bueno</label>
        <label><input type="radio" name="sensores" value="regular"> Regular</label>
        <label><input type="radio" name="sensores" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>POLEAS:</label>
    <div class="opciones">
        <label><input type="radio" name="poleas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="poleas" value="regular"> Regular</label>
        <label><input type="radio" name="poleas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CONCHA:</label>
    <div class="opciones">
        <label><input type="radio" name="concha" value="bueno" required> Bueno</label>
        <label><input type="radio" name="concha" value="regular"> Regular</label>
        <label><input type="radio" name="concha" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CREMALLERA:</label>
    <div class="opciones">
        <label><input type="radio" name="cremallera" value="bueno" required> Bueno</label>
        <label><input type="radio" name="cremallera" value="regular"> Regular</label>
        <label><input type="radio" name="cremallera" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CLUTCH:</label>
    <div class="opciones">
        <label><input type="radio" name="clutch" value="bueno" required> Bueno</label>
        <label><input type="radio" name="clutch" value="regular"> Regular</label>
        <label><input type="radio" name="clutch" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>COPLES:</label>
    <div class="opciones">
        <label><input type="radio" name="coples" value="bueno" required> Bueno</label>
        <label><input type="radio" name="coples" value="regular"> Regular</label>
        <label><input type="radio" name="coples" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>BOMBA DE INYECCION:</label>
    <div class="opciones">
        <label><input type="radio" name="bomba_de_inyeccion" value="bueno" required> Bueno</label>
        <label><input type="radio" name="bomba_de_inyeccion" value="regular"> Regular</label>
        <label><input type="radio" name="bomba_de_inyeccion" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>JUNTAS:</label>
    <div class="opciones">
        <label><input type="radio" name="juntas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="juntas" value="regular"> Regular</label>
        <label><input type="radio" name="juntas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>MARCHA:</label>
    <div class="opciones">
        <label><input type="radio" name="marcha" value="bueno" required> Bueno</label>
        <label><input type="radio" name="marcha" value="regular"> Regular</label>
        <label><input type="radio" name="marcha" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>TUBERIA:</label>
    <div class="opciones">
        <label><input type="radio" name="tuberia" value="bueno" required> Bueno</label>
        <label><input type="radio" name="tuberia" value="regular"> Regular</label>
        <label><input type="radio" name="tuberia" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>ALTERNADOR:</label>
    <div class="opciones">
        <label><input type="radio" name="alternador" value="bueno" required> Bueno</label>
        <label><input type="radio" name="alternador" value="regular"> Regular</label>
        <label><input type="radio" name="alternador" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>FILTROS:</label>
    <div class="opciones">
        <label><input type="radio" name="filtros" value="bueno" required> Bueno</label>
        <label><input type="radio" name="filtros" value="regular"> Regular</label>
        <label><input type="radio" name="filtros" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>BASES:</label>
    <div class="opciones">
        <label><input type="radio" name="bases" value="bueno" required> Bueno</label>
        <label><input type="radio" name="bases" value="regular"> Regular</label>
        <label><input type="radio" name="bases" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>SOPORTES:</label>
    <div class="opciones">
        <label><input type="radio" name="soportes" value="bueno" required> Bueno</label>
        <label><input type="radio" name="soportes" value="regular"> Regular</label>
        <label><input type="radio" name="soportes" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>TURBO:</label>
    <div class="opciones">
        <label><input type="radio" name="turbo" value="bueno" required> Bueno</label>
        <label><input type="radio" name="turbo" value="regular"> Regular</label>
        <label><input type="radio" name="turbo" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>ESCAPE:</label>
    <div class="opciones">
        <label><input type="radio" name="escape" value="bueno" required> Bueno</label>
        <label><input type="radio" name="escape" value="regular"> Regular</label>
        <label><input type="radio" name="escape" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CHICOTES:</label>
    <div class="opciones">
        <label><input type="radio" name="chicotes" value="bueno" required> Bueno</label>
        <label><input type="radio" name="chicotes" value="regular"> Regular</label>
        <label><input type="radio" name="chicotes" value="malo"> Malo</label>
    </div>
</div>
<h3>MECANICO</h3>

<div class="campo">
    <label>TRANSMISION:</label>
    <div class="opciones">
        <label><input type="radio" name="transmision" value="bueno" required> Bueno</label>
        <label><input type="radio" name="transmision" value="regular"> Regular</label>
        <label><input type="radio" name="transmision" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>DIFERENCIALES:</label>
    <div class="opciones">
        <label><input type="radio" name="diferenciales" value="bueno" required> Bueno</label>
        <label><input type="radio" name="diferenciales" value="regular"> Regular</label>
        <label><input type="radio" name="diferenciales" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CARDAN:</label>
    <div class="opciones">
        <label><input type="radio" name="cardan" value="bueno" required> Bueno</label>
        <label><input type="radio" name="cardan" value="regular"> Regular</label>
        <label><input type="radio" name="cardan" value="malo"> Malo</label>
    </div>
</div>
<h3>HIDRAULICO</h3>

<div class="campo">
    <label>BANCO DE VALVULAS:</label>
    <div class="opciones">
        <label><input type="radio" name="banco_de_valvulas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="banco_de_valvulas" value="regular"> Regular</label>
        <label><input type="radio" name="banco_de_valvulas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>BOMBAS DE TRANSITO:</label>
    <div class="opciones">
        <label><input type="radio" name="bombas_de_transito" value="bueno" required> Bueno</label>
        <label><input type="radio" name="bombas_de_transito" value="regular"> Regular</label>
        <label><input type="radio" name="bombas_de_transito" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>BOMBAS DE PRECARGA:</label>
    <div class="opciones">
        <label><input type="radio" name="bombas_de_precarga" value="bueno" required> Bueno</label>
        <label><input type="radio" name="bombas_de_precarga" value="regular"> Regular</label>
        <label><input type="radio" name="bombas_de_precarga" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>BOMBAS DE ACCESORIOS:</label>
    <div class="opciones">
        <label><input type="radio" name="bombas_de_accesorios" value="bueno" required> Bueno</label>
        <label><input type="radio" name="bombas_de_accesorios" value="regular"> Regular</label>
        <label><input type="radio" name="bombas_de_accesorios" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>COPLES HIDRAULICOS:</label>
    <div class="opciones">
        <label><input type="radio" name="coples_hidraulicos" value="bueno" required> Bueno</label>
        <label><input type="radio" name="coples_hidraulicos" value="regular"> Regular</label>
        <label><input type="radio" name="coples_hidraulicos" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CLUTCH HIDRAULICO:</label>
    <div class="opciones">
        <label><input type="radio" name="clutch_hidraulico" value="bueno" required> Bueno</label>
        <label><input type="radio" name="clutch_hidraulico" value="regular"> Regular</label>
        <label><input type="radio" name="clutch_hidraulico" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>GATOS DE LEVANTE:</label>
    <div class="opciones">
        <label><input type="radio" name="gatos_de_levante" value="bueno" required> Bueno</label>
        <label><input type="radio" name="gatos_de_levante" value="regular"> Regular</label>
        <label><input type="radio" name="gatos_de_levante" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>GATOS DE DIRECCION:</label>
    <div class="opciones">
        <label><input type="radio" name="gatos_de_direccion" value="bueno" required> Bueno</label>
        <label><input type="radio" name="gatos_de_direccion" value="regular"> Regular</label>
        <label><input type="radio" name="gatos_de_direccion" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>GATOS DE ACCESORIOS:</label>
    <div class="opciones">
        <label><input type="radio" name="gatos_de_accesorios" value="bueno" required> Bueno</label>
        <label><input type="radio" name="gatos_de_accesorios" value="regular"> Regular</label>
        <label><input type="radio" name="gatos_de_accesorios" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>MANGUERAS:</label>
    <div class="opciones">
        <label><input type="radio" name="mangueras" value="bueno" required> Bueno</label>
        <label><input type="radio" name="mangueras" value="regular"> Regular</label>
        <label><input type="radio" name="mangueras" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>MOTORES HIDRAULICOS:</label>
    <div class="opciones">
        <label><input type="radio" name="motores_hidraulicos" value="bueno" required> Bueno</label>
        <label><input type="radio" name="motores_hidraulicos" value="regular"> Regular</label>
        <label><input type="radio" name="motores_hidraulicos" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>ORBITROL:</label>
    <div class="opciones">
        <label><input type="radio" name="orbitrol" value="bueno" required> Bueno</label>
        <label><input type="radio" name="orbitrol" value="regular"> Regular</label>
        <label><input type="radio" name="orbitrol" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>TORQUES HUV:</label>
    <div class="opciones">
        <label><input type="radio" name="torques_huv" value="bueno" required> Bueno</label>
        <label><input type="radio" name="torques_huv" value="regular"> Regular</label>
        <label><input type="radio" name="torques_huv" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>VALVULAS DE RETENCION:</label>
    <div class="opciones">
        <label><input type="radio" name="valvulas_de_retencion" value="bueno" required> Bueno</label>
        <label><input type="radio" name="valvulas_de_retencion" value="regular"> Regular</label>
        <label><input type="radio" name="valvulas_de_retencion" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>REDUCTORES:</label>
    <div class="opciones">
        <label><input type="radio" name="reductores" value="bueno" required> Bueno</label>
        <label><input type="radio" name="reductores" value="regular"> Regular</label>
        <label><input type="radio" name="reductores" value="malo"> Malo</label>
    </div>
</div>
<h3>ELECTRICO</h3>

<div class="campo">
    <label>ALARMAS:</label>
    <div class="opciones">
        <label><input type="radio" name="alarmas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="alarmas" value="regular"> Regular</label>
        <label><input type="radio" name="alarmas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>ARNESES:</label>
    <div class="opciones">
        <label><input type="radio" name="arneses" value="bueno" required> Bueno</label>
        <label><input type="radio" name="arneses" value="regular"> Regular</label>
        <label><input type="radio" name="arneses" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>BOBINAS:</label>
    <div class="opciones">
        <label><input type="radio" name="bobinas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="bobinas" value="regular"> Regular</label>
        <label><input type="radio" name="bobinas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>BOTONES:</label>
    <div class="opciones">
        <label><input type="radio" name="botones" value="bueno" required> Bueno</label>
        <label><input type="radio" name="botones" value="regular"> Regular</label>
        <label><input type="radio" name="botones" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CABLES:</label>
    <div class="opciones">
        <label><input type="radio" name="cables" value="bueno" required> Bueno</label>
        <label><input type="radio" name="cables" value="regular"> Regular</label>
        <label><input type="radio" name="cables" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CABLES DE SENSORES:</label>
    <div class="opciones">
        <label><input type="radio" name="cables_de_sensores" value="bueno" required> Bueno</label>
        <label><input type="radio" name="cables_de_sensores" value="regular"> Regular</label>
        <label><input type="radio" name="cables_de_sensores" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CONECTORES:</label>
    <div class="opciones">
        <label><input type="radio" name="conectores" value="bueno" required> Bueno</label>
        <label><input type="radio" name="conectores" value="regular"> Regular</label>
        <label><input type="radio" name="conectores" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>ELECTRO VALVULAS:</label>
    <div class="opciones">
        <label><input type="radio" name="electro_valvulas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="electro_valvulas" value="regular"> Regular</label>
        <label><input type="radio" name="electro_valvulas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>FUSIBLES:</label>
    <div class="opciones">
        <label><input type="radio" name="fusibles" value="bueno" required> Bueno</label>
        <label><input type="radio" name="fusibles" value="regular"> Regular</label>
        <label><input type="radio" name="fusibles" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>PORTA FUSIBLES:</label>
    <div class="opciones">
        <label><input type="radio" name="porta_fusibles" value="bueno" required> Bueno</label>
        <label><input type="radio" name="porta_fusibles" value="regular"> Regular</label>
        <label><input type="radio" name="porta_fusibles" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>INDICADORES:</label>
    <div class="opciones">
        <label><input type="radio" name="indicadores" value="bueno" required> Bueno</label>
        <label><input type="radio" name="indicadores" value="regular"> Regular</label>
        <label><input type="radio" name="indicadores" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>PRESION AGUA TEMP VOLTIMETRO:</label>
    <div class="opciones">
        <label><input type="radio" name="presion_agua_temp_voltimetro" value="bueno" required> Bueno</label>
        <label><input type="radio" name="presion_agua_temp_voltimetro" value="regular"> Regular</label>
        <label><input type="radio" name="presion_agua_temp_voltimetro" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>LUCES:</label>
    <div class="opciones">
        <label><input type="radio" name="luces" value="bueno" required> Bueno</label>
        <label><input type="radio" name="luces" value="regular"> Regular</label>
        <label><input type="radio" name="luces" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>MODULOS:</label>
    <div class="opciones">
        <label><input type="radio" name="modulos" value="bueno" required> Bueno</label>
        <label><input type="radio" name="modulos" value="regular"> Regular</label>
        <label><input type="radio" name="modulos" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>TORRETA:</label>
    <div class="opciones">
        <label><input type="radio" name="torreta" value="bueno" required> Bueno</label>
        <label><input type="radio" name="torreta" value="regular"> Regular</label>
        <label><input type="radio" name="torreta" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>RELEVADORES:</label>
    <div class="opciones">
        <label><input type="radio" name="relevadores" value="bueno" required> Bueno</label>
        <label><input type="radio" name="relevadores" value="regular"> Regular</label>
        <label><input type="radio" name="relevadores" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>SWITCH LLAVE:</label>
    <div class="opciones">
        <label><input type="radio" name="switch_llave" value="bueno" required> Bueno</label>
        <label><input type="radio" name="switch_llave" value="regular"> Regular</label>
        <label><input type="radio" name="switch_llave" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>SENSORES ELECTRICOS:</label>
    <div class="opciones">
        <label><input type="radio" name="sensores_electricos" value="bueno" required> Bueno</label>
        <label><input type="radio" name="sensores_electricos" value="regular"> Regular</label>
        <label><input type="radio" name="sensores_electricos" value="malo"> Malo</label>
    </div>
</div>
<h3>ESTETICO</h3>

<div class="campo">
    <label>PINTURA:</label>
    <div class="opciones">
        <label><input type="radio" name="pintura" value="bueno" required> Bueno</label>
        <label><input type="radio" name="pintura" value="regular"> Regular</label>
        <label><input type="radio" name="pintura" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CALCOMANIAS:</label>
    <div class="opciones">
        <label><input type="radio" name="calcomanias" value="bueno" required> Bueno</label>
        <label><input type="radio" name="calcomanias" value="regular"> Regular</label>
        <label><input type="radio" name="calcomanias" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>ASIENTO:</label>
    <div class="opciones">
        <label><input type="radio" name="asiento" value="bueno" required> Bueno</label>
        <label><input type="radio" name="asiento" value="regular"> Regular</label>
        <label><input type="radio" name="asiento" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>TAPICERIA:</label>
    <div class="opciones">
        <label><input type="radio" name="tapiceria" value="bueno" required> Bueno</label>
        <label><input type="radio" name="tapiceria" value="regular"> Regular</label>
        <label><input type="radio" name="tapiceria" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>TOLVAS:</label>
    <div class="opciones">
        <label><input type="radio" name="tolvas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="tolvas" value="regular"> Regular</label>
        <label><input type="radio" name="tolvas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CRISTALES:</label>
    <div class="opciones">
        <label><input type="radio" name="cristales" value="bueno" required> Bueno</label>
        <label><input type="radio" name="cristales" value="regular"> Regular</label>
        <label><input type="radio" name="cristales" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>ACCESORIOS:</label>
    <div class="opciones">
        <label><input type="radio" name="accesorios" value="bueno" required> Bueno</label>
        <label><input type="radio" name="accesorios" value="regular"> Regular</label>
        <label><input type="radio" name="accesorios" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>SISTEMA DE RIEGO:</label>
    <div class="opciones">
        <label><input type="radio" name="sistema_de_riego" value="bueno" required> Bueno</label>
        <label><input type="radio" name="sistema_de_riego" value="regular"> Regular</label>
        <label><input type="radio" name="sistema_de_riego" value="malo"> Malo</label>
    </div>
</div>
<h3>CONSUMIBLES</h3>

<div class="campo">
    <label>PUNTAS:</label>
    <div class="opciones">
        <label><input type="radio" name="puntas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="puntas" value="regular"> Regular</label>
        <label><input type="radio" name="puntas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>PORTA PUNTAS:</label>
    <div class="opciones">
        <label><input type="radio" name="porta_puntas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="porta_puntas" value="regular"> Regular</label>
        <label><input type="radio" name="porta_puntas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>GARRAS:</label>
    <div class="opciones">
        <label><input type="radio" name="garras" value="bueno" required> Bueno</label>
        <label><input type="radio" name="garras" value="regular"> Regular</label>
        <label><input type="radio" name="garras" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CUCHILLAS:</label>
    <div class="opciones">
        <label><input type="radio" name="cuchillas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="cuchillas" value="regular"> Regular</label>
        <label><input type="radio" name="cuchillas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>CEPILLOS:</label>
    <div class="opciones">
        <label><input type="radio" name="cepillos" value="bueno" required> Bueno</label>
        <label><input type="radio" name="cepillos" value="regular"> Regular</label>
        <label><input type="radio" name="cepillos" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>SEPARADORES:</label>
    <div class="opciones">
        <label><input type="radio" name="separadores" value="bueno" required> Bueno</label>
        <label><input type="radio" name="separadores" value="regular"> Regular</label>
        <label><input type="radio" name="separadores" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>LLANTAS:</label>
    <div class="opciones">
        <label><input type="radio" name="llantas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="llantas" value="regular"> Regular</label>
        <label><input type="radio" name="llantas" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>RINES:</label>
    <div class="opciones">
        <label><input type="radio" name="rines" value="bueno" required> Bueno</label>
        <label><input type="radio" name="rines" value="regular"> Regular</label>
        <label><input type="radio" name="rines" value="malo"> Malo</label>
    </div>
</div>

<div class="campo">
    <label>BANDAS ORUGAS:</label>
    <div class="opciones">
        <label><input type="radio" name="bandas_orugas" value="bueno" required> Bueno</label>
        <label><input type="radio" name="bandas_orugas" value="regular"> Regular</label>
        <label><input type="radio" name="bandas_orugas" value="malo"> Malo</label>
    </div>
</div>

<input type="submit" value="Guardar Recibo" class="submit-btn">
</form>
</body>
</html>
