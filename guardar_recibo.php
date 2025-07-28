<?php
session_start();
include '../conexion.php';

function convertir_valor($valor) {
    return match (strtolower($valor)) {
        'bueno' => 100,
        'regular' => 70,
        'malo' => 40,
        default => 0
    };
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_maquinaria   = intval($_POST['id_maquinaria']);
    $empresa_origen  = $_POST['empresa_origen'] ?? '';
    $empresa_destino = $_POST['empresa_destino'] ?? '';
    $observaciones   = $_POST['observaciones'] ?? '';
    $componentes     = $_POST['componentes'] ?? [];

    // Pesos de cada sección
    $pesos = [
        'MOTOR' => 15,
        'SISTEMA MECÁNICO' => 15,
        'SISTEMA HIDRÁULICO' => 30,
        'SISTEMA ELÉCTRICO Y ELECTRÓNICO' => 25,
        'ESTÉTICO' => 5,
        'CONSUMIBLES' => 10
    ];

    // Componentes unificados (igual que en el formulario)
    $secciones = [
        "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGUEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
        "SISTEMA MECÁNICO" => ["TRANSMISION", "DIFERENCIALES", "CARDAN"],
        "SISTEMA HIDRÁULICO" => ["BANCO DE VALVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "COPLES", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCION", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATELITES)", "VALVULAS DE RETENCION", "REDUCTORES"],
        "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VALVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESION/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MODULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)", "SENSORES"],
        "ESTÉTICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
        "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
    ];

    // Calcular porcentaje de condición
    $total = 0;
    foreach ($secciones as $seccion => $lista) {
        $sum = 0;
        $count = 0;
        foreach ($lista as $componente) {
            if (isset($componentes[$componente])) {
                $sum += convertir_valor($componentes[$componente]);
                $count++;
            }
        }
        if ($count > 0) {
            $promedio = $sum / $count;
            $total += $promedio * ($pesos[$seccion] / 100);
        }
    }
    $condicion = round($total);

    $conn->query("UPDATE maquinaria SET condicion_estimada = $condicion WHERE id = $id_maquinaria");

    header("Location: recibo_unidad.php?id=$id_maquinaria&guardado=1");
    exit;
}
