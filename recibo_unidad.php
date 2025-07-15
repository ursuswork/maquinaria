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
        h3 { margin-top: 30px; }
        label { display: inline-block; width: 300px; margin-top: 8px; }
        select { width: 200px; margin-bottom: 10px; }
    </style>
</head>
<body>
<h2>Recibo de Unidad (Maquinaria ID: <?= $id ?>)</h2>
<form method="POST">
<h3>MOTOR</h3>
<label for="cilindros">CILINDROS:</label>
<select name="cilindros" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="pistones">PISTONES:</label>
<select name="pistones" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="anillos">ANILLOS:</label>
<select name="anillos" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="inyectores">INYECTORES:</label>
<select name="inyectores" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="block">BLOCK:</label>
<select name="block" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="cabeza">CABEZA:</label>
<select name="cabeza" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="varillas">VARILLAS:</label>
<select name="varillas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="resortes">RESORTES:</label>
<select name="resortes" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="punterias">PUNTERIAS:</label>
<select name="punterias" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="cigueñal">CIGUEÑAL:</label>
<select name="cigueñal" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="arbol_de_elevas">ARBOL DE ELEVAS:</label>
<select name="arbol_de_elevas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="retenes">RETENES:</label>
<select name="retenes" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="ligas">LIGAS:</label>
<select name="ligas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="sensores">SENSORES:</label>
<select name="sensores" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="poleas">POLEAS:</label>
<select name="poleas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="concha">CONCHA:</label>
<select name="concha" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="cremallera">CREMALLERA:</label>
<select name="cremallera" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="clutch">CLUTCH:</label>
<select name="clutch" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="coples">COPLES:</label>
<select name="coples" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="bomba_de_inyeccion">BOMBA DE INYECCION:</label>
<select name="bomba_de_inyeccion" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="juntas">JUNTAS:</label>
<select name="juntas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="marcha">MARCHA:</label>
<select name="marcha" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="tuberia">TUBERIA:</label>
<select name="tuberia" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="alternador">ALTERNADOR:</label>
<select name="alternador" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="filtros">FILTROS:</label>
<select name="filtros" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="bases">BASES:</label>
<select name="bases" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="soportes">SOPORTES:</label>
<select name="soportes" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="turbo">TURBO:</label>
<select name="turbo" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="escape">ESCAPE:</label>
<select name="escape" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="chicotes">CHICOTES:</label>
<select name="chicotes" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<h3>MECANICO</h3>
<label for="transmision">TRANSMISION:</label>
<select name="transmision" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="diferenciales">DIFERENCIALES:</label>
<select name="diferenciales" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="cardan">CARDAN:</label>
<select name="cardan" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<h3>HIDRAULICO</h3>
<label for="banco_de_valvulas">BANCO DE VALVULAS:</label>
<select name="banco_de_valvulas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="bombas_de_transito">BOMBAS DE TRANSITO:</label>
<select name="bombas_de_transito" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="bombas_de_precarga">BOMBAS DE PRECARGA:</label>
<select name="bombas_de_precarga" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="bombas_de_accesorios">BOMBAS DE ACCESORIOS:</label>
<select name="bombas_de_accesorios" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="coples_hidraulicos">COPLES HIDRAULICOS:</label>
<select name="coples_hidraulicos" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="clutch_hidraulico">CLUTCH HIDRAULICO:</label>
<select name="clutch_hidraulico" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="gatos_de_levante">GATOS DE LEVANTE:</label>
<select name="gatos_de_levante" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="gatos_de_direccion">GATOS DE DIRECCION:</label>
<select name="gatos_de_direccion" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="gatos_de_accesorios">GATOS DE ACCESORIOS:</label>
<select name="gatos_de_accesorios" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="mangueras">MANGUERAS:</label>
<select name="mangueras" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="motores_hidraulicos">MOTORES HIDRAULICOS:</label>
<select name="motores_hidraulicos" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="orbitrol">ORBITROL:</label>
<select name="orbitrol" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="torques_huv">TORQUES HUV:</label>
<select name="torques_huv" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="valvulas_de_retencion">VALVULAS DE RETENCION:</label>
<select name="valvulas_de_retencion" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="reductores">REDUCTORES:</label>
<select name="reductores" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<h3>ELECTRICO</h3>
<label for="alarmas">ALARMAS:</label>
<select name="alarmas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="arneses">ARNESES:</label>
<select name="arneses" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="bobinas">BOBINAS:</label>
<select name="bobinas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="botones">BOTONES:</label>
<select name="botones" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="cables">CABLES:</label>
<select name="cables" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="cables_de_sensores">CABLES DE SENSORES:</label>
<select name="cables_de_sensores" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="conectores">CONECTORES:</label>
<select name="conectores" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="electro_valvulas">ELECTRO VALVULAS:</label>
<select name="electro_valvulas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="fusibles">FUSIBLES:</label>
<select name="fusibles" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="porta_fusibles">PORTA FUSIBLES:</label>
<select name="porta_fusibles" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="indicadores">INDICADORES:</label>
<select name="indicadores" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="presion_agua_temp_voltimetro">PRESION AGUA TEMP VOLTIMETRO:</label>
<select name="presion_agua_temp_voltimetro" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="luces">LUCES:</label>
<select name="luces" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="modulos">MODULOS:</label>
<select name="modulos" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="torreta">TORRETA:</label>
<select name="torreta" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="relevadores">RELEVADORES:</label>
<select name="relevadores" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="switch_llave">SWITCH LLAVE:</label>
<select name="switch_llave" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="sensores_electricos">SENSORES ELECTRICOS:</label>
<select name="sensores_electricos" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<h3>ESTETICO</h3>
<label for="pintura">PINTURA:</label>
<select name="pintura" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="calcomanias">CALCOMANIAS:</label>
<select name="calcomanias" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="asiento">ASIENTO:</label>
<select name="asiento" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="tapiceria">TAPICERIA:</label>
<select name="tapiceria" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="tolvas">TOLVAS:</label>
<select name="tolvas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="cristales">CRISTALES:</label>
<select name="cristales" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="accesorios">ACCESORIOS:</label>
<select name="accesorios" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="sistema_de_riego">SISTEMA DE RIEGO:</label>
<select name="sistema_de_riego" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<h3>CONSUMIBLES</h3>
<label for="puntas">PUNTAS:</label>
<select name="puntas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="porta_puntas">PORTA PUNTAS:</label>
<select name="porta_puntas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="garras">GARRAS:</label>
<select name="garras" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="cuchillas">CUCHILLAS:</label>
<select name="cuchillas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="cepillos">CEPILLOS:</label>
<select name="cepillos" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="separadores">SEPARADORES:</label>
<select name="separadores" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="llantas">LLANTAS:</label>
<select name="llantas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="rines">RINES:</label>
<select name="rines" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>
<label for="bandas_orugas">BANDAS ORUGAS:</label>
<select name="bandas_orugas" required>
    <option value="">Selecciona</option>
    <option value="bueno">Bueno</option>
    <option value="regular">Regular</option>
    <option value="malo">Malo</option>
</select><br>

<input type="submit" value="Guardar Recibo">
</form>
</body>
</html>
