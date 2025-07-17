<?php
session_start();
include '../conexion.php';

function convertir_valor($valor) {
    switch ($valor) {
        case 'bueno': return 100;
        case 'regular': return 70;
        case 'malo': return 40;
        default: return 0;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_maquinaria = intval($_POST['id_maquinaria']);
    $empresa_origen = $_POST['empresa_origen'];
    $empresa_destino = $_POST['empresa_destino'];
    $observaciones = $_POST['observaciones'] ?? '';
    $componentes = $_POST['componentes'] ?? [];

    $total = 0;
    $peso_total = 0;

    $pesos = [
        'MOTOR' => 15,
        'SISTEMA MECÁNICO' => 15,
        'SISTEMA HIDRÁULICO' => 30,
        'SISTEMA ELÉCTRICO Y ELECTRÓNICO' => 30,
        'ESTÉTICO' => 5,
        'CONSUMIBLES' => 10
    ];

    $secciones = [
        'MOTOR' => ["Cilindros", "Pistones", "Anillos", "Inyectores", "Block", "Cabeza", "Varillas", "Resortes", "Punterías", "Cigüeñal", "Árbol de levas", "Retenes", "Ligas", "Sensores", "Poleas", "Concha", "Cremallera", "Clutch", "Coples", "Bomba de inyección", "Juntas", "Marcha", "Tubería", "Alternador", "Filtros", "Bases", "Soportes", "Turbo", "Escape", "Chicotes"],
        'SISTEMA MECÁNICO' => ["Transmisión", "Diferenciales", "Cardán"],
        'SISTEMA HIDRÁULICO' => ["Banco de válvulas", "Bombas de tránsito", "Bombas de precarga", "Bombas de accesorios", "Coples", "Clutch hidráulico", "Gatos de levante", "Gatos de dirección", "Gatos de accesorios", "Mangueras", "Motores hidráulicos", "Orbitrol", "Torques HUV (Satélites)", "Válvulas de retención", "Reductores"],
        'SISTEMA ELÉCTRICO Y ELECTRÓNICO' => ["Alarmas", "Arneses", "Bobinas", "Botones", "Cables", "Cables de sensores", "Conectores", "Electro válvulas", "Fusibles", "Porta fusibles", "Indicadores", "Presión/Agua/Temperatura/Voltímetro", "Luces", "Módulos", "Torreta", "Relevadores", "Switch (llave)", "Sensores"],
        'ESTÉTICO' => ["Pintura", "Calcomanías", "Asiento", "Tapicería", "Tolvas", "Cristales", "Accesorios", "Sistema de riego"],
        'CONSUMIBLES' => ["Puntas", "Porta puntas", "Garras", "Cuchillas", "Cepillos", "Separadores", "Llantas", "Rines", "Bandas / Orugas"]
    ];

    foreach ($secciones as $nombre => $campos) {
        $suma = 0;
        $cuenta = 0;
        foreach ($campos as $c) {
            if (isset($componentes[$c])) {
                $suma += convertir_valor($componentes[$c]);
                $cuenta++;
            }
        }
        if ($cuenta > 0) {
            $prom = $suma / $cuenta;
            $total += $prom * ($pesos[$nombre] / 100);
            $peso_total += $pesos[$nombre];
        }
    }

    $condicion = round($total);

    $stmt = $conn->prepare("INSERT INTO recibo_unidad (id_maquinaria, fecha, observaciones, condicion_estimada) VALUES (?, NOW(), ?, ?)");
    $stmt->bind_param("isi", $id_maquinaria, $observaciones, $condicion);
    $stmt->execute();

    $conn->query("UPDATE maquinaria SET condicion_estimada = $condicion WHERE id = $id_maquinaria");

    header("Location: ../inventario.php?guardado=1");
    exit;
}
?>
