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
        "MOTOR" => ["CILINDROS", "PISTONES", "ANILLOS", "INYECTORES", "BLOCK", "CABEZA", "VARILLAS", "RESORTES", "PUNTERIAS", "CIGÜEÑAL", "ARBOL DE ELEVAS", "RETENES", "LIGAS", "SENSORES", "POLEAS", "CONCHA", "CREMAYERA", "CLUTCH", "COPLES", "BOMBA DE INYECCION", "JUNTAS", "MARCHA", "TUBERIA", "ALTERNADOR", "FILTROS", "BASES", "SOPORTES", "TURBO", "ESCAPE", "CHICOTES"],
        "SISTEMA MECÁNICO" => ["TRANSMISIÓN", "DIFERENCIALES", "CARDÁN"],
        "SISTEMA HIDRÁULICO" => ["BANCO DE VÁLVULAS", "BOMBAS DE TRANSITO", "BOMBAS DE PRECARGA", "BOMBAS DE ACCESORIOS", "COPLES", "CLUTCH HIDRÁULICO", "GATOS DE LEVANTE", "GATOS DE DIRECCIÓN", "GATOS DE ACCESORIOS", "MANGUERAS", "MOTORES HIDRÁULICOS", "ORBITROL", "TORQUES HUV (SATÉLITES)", "VÁLVULAS DE RETENCIÓN", "REDUCTORES"],
        "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => ["ALARMAS", "ARNESES", "BOBINAS", "BOTONES", "CABLES", "CABLES DE SENSORES", "CONECTORES", "ELECTRO VÁLVULAS", "FUSIBLES", "PORTA FUSIBLES", "INDICADORES", "PRESIÓN/AGUA/TEMPERATURA/VOLTIMETRO", "LUCES", "MÓDULOS", "TORRETA", "RELEVADORES", "SWITCH (LLAVE)", "SENSORES"],
        "ESTÉTICO" => ["PINTURA", "CALCOMANIAS", "ASIENTO", "TAPICERIA", "TOLVAS", "CRISTALES", "ACCESORIOS", "SISTEMA DE RIEGO"],
        "CONSUMIBLES" => ["PUNTAS", "PORTA PUNTAS", "GARRAS", "CUCHILLAS", "CEPILLOS", "SEPARADORES", "LLANTAS", "RINES", "BANDAS / ORUGAS"]
    ];

    // Asegurar columnas dinámicamente en la tabla recibo_unidad
    foreach ($secciones as $lista) {
        foreach ($lista as $componente) {
            $col = $conn->real_escape_string($componente);
            $exists = $conn->query("SHOW COLUMNS FROM recibo_unidad LIKE '$col'");
            if ($exists->num_rows === 0) {
                $conn->query("ALTER TABLE recibo_unidad ADD COLUMN `$col` VARCHAR(20) DEFAULT ''");
            }
        }
    }

    // Asegurar columna condicion_estimada en recibo_unidad y maquinaria
    $conn->query("ALTER TABLE recibo_unidad ADD COLUMN IF NOT EXISTS condicion_estimada INT DEFAULT 0");
    $conn->query("ALTER TABLE maquinaria ADD COLUMN IF NOT EXISTS condicion_estimada INT DEFAULT 0");

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

    // Armar columnas dinámicas para insertar/update
    $campos_extra = "";
    $marcadores = "";
    $valores = [];
    foreach ($componentes as $clave => $valor) {
        $campo = $conn->real_escape_string($clave);
        $campos_extra .= ", `$campo`";
        $marcadores .= ", ?";
        $valores[] = $valor;
    }

    // Verificar si ya existe recibo
    $check = $conn->query("SELECT id FROM recibo_unidad WHERE id_maquinaria = $id_maquinaria LIMIT 1");
    if ($check->num_rows > 0) {
        // UPDATE
        $sets = "empresa_origen=?, empresa_destino=?, fecha=NOW(), observaciones=?, condicion_estimada=?";
        foreach ($componentes as $clave => $valor) {
            $sets .= ", `" . $conn->real_escape_string($clave) . "` = ?";
        }
        $sql = "UPDATE recibo_unidad SET $sets WHERE id_maquinaria=?";
        $stmt = $conn->prepare($sql);
        $tipos = str_repeat("s", count($valores) + 3) . "ii";
        $stmt->bind_param($tipos, ...array_merge([$empresa_origen, $empresa_destino, $observaciones, $condicion], $valores, [$id_maquinaria]));
    } else {
        // INSERT
        $sql = "INSERT INTO recibo_unidad (id_maquinaria, empresa_origen, empresa_destino, fecha, observaciones, condicion_estimada$campos_extra) VALUES (?, ?, ?, NOW(), ?, ?$marcadores)";
        $stmt = $conn->prepare($sql);
        $tipos = "isssi" . str_repeat("s", count($valores));
        $stmt->bind_param($tipos, ...array_merge([$id_maquinaria, $empresa_origen, $empresa_destino, $observaciones, $condicion], $valores));
    }

    $stmt->execute();

    // ✅ Actualizar condición en maquinaria
    $conn->query("UPDATE maquinaria SET condicion_estimada = $condicion WHERE id = $id_maquinaria");

    // ✅ Redirigir con confirmación
    header("Location: recibo_unidad.php?id=$id_maquinaria&guardado=1");
    exit;
}
?>
