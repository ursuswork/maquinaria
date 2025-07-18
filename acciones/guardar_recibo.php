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

function normalizar_campo($cadena) {
    $sin_acentos = iconv('UTF-8', 'ASCII//TRANSLIT', $cadena);
    return strtolower(preg_replace('/[^a-z0-9_]/', '_', $sin_acentos));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_maquinaria = intval($_POST['id_maquinaria']);
    $empresa_origen = $_POST['empresa_origen'] ?? '';
    $empresa_destino = $_POST['empresa_destino'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';
    $componentes = $_POST['componentes'] ?? [];

    $pesos = [
        'MOTOR' => 15,
        'SISTEMA MECÁNICO' => 15,
        'SISTEMA HIDRÁULICO' => 30,
        'SISTEMA ELÉCTRICO Y ELECTRÓNICO' => 25,
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

    $total = 0;
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
        }
    }
    $condicion = round($total);

    $nuevos_componentes = [];
    foreach ($componentes as $campo => $valor) {
        $col = $conn->real_escape_string(normalizar_campo($campo));
        $nuevos_componentes[$col] = $valor;

        $existe = $conn->query("SHOW COLUMNS FROM recibo_unidad LIKE '$col'");
        if ($existe->num_rows === 0) {
            $conn->query("ALTER TABLE recibo_unidad ADD COLUMN `$col` ENUM('bueno','regular','malo') DEFAULT NULL");
        }
    }

    $cols = "";
    $vals = "";
    $datos = [];
    foreach ($nuevos_componentes as $campo => $valor) {
        $cols .= ", `$campo`";
        $vals .= ", ?";
        $datos[] = $valor;
    }

    $sql = "INSERT INTO recibo_unidad (id_maquinaria, empresa_origen, empresa_destino, fecha, observaciones, condicion_estimada $cols) VALUES (?, ?, ?, NOW(), ?, ? $vals)";
    $stmt = $conn->prepare($sql);
    $tipos = "isssi" . str_repeat("s", count($datos));
    $stmt->bind_param($tipos, ...array_merge([$id_maquinaria, $empresa_origen, $empresa_destino, $observaciones, $condicion], $datos));
    $stmt->execute();

    $conn->query("UPDATE maquinaria SET condicion_estimada = $condicion WHERE id = $id_maquinaria");
    header("Location: ../inventario.php?guardado=1");
    exit;
}
?>