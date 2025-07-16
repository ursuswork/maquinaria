<?php
include '../conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID no proporcionado.");
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
        function valor_numerico($v) {
            if ($v == 'bueno') return 100;
            if ($v == 'regular') return 50;
            if ($v == 'malo') return 0;
            return null;
        }

        $componentes = [
            "motor" => ["cilindros", "pistones", "anillos", "inyectores", "block", "cabeza", "varillas", "resortes", "punterias", "cigueñal", "arbol_de_elevas", "retenes", "ligas", "sensores", "poleas", "concha", "cremallera", "clutch", "coples", "bomba_de_inyeccion", "juntas", "marcha", "tuberia", "alternador", "filtros", "bases", "soportes", "turbo", "escape", "chicotes"],
            "mecanico" => ["transmision", "diferenciales", "cardan"],
            "hidraulico" => ["banco_de_valvulas", "bombas_de_transito", "bombas_de_precarga", "bombas_de_accesorios", "coples_hidraulicos", "clutch_hidraulico", "gatos_de_levante", "gatos_de_direccion", "gatos_de_accesorios", "mangueras", "motores_hidraulicos", "orbitrol", "torques_huv", "valvulas_de_retencion", "reductores"],
            "electrico" => ["alarmas", "arneses", "bobinas", "botones", "cables", "cables_de_sensores", "conectores", "electro_valvulas", "fusibles", "porta_fusibles", "indicadores", "presion_agua_temp_voltimetro", "luces", "modulos", "torreta", "relevadores", "switch_llave", "sensores_electricos"],
            "estetico" => ["pintura", "calcomanias", "asiento", "tapiceria", "tolvas", "cristales", "accesorios", "sistema_de_riego"],
            "consumibles" => ["puntas", "porta_puntas", "garras", "cuchillas", "cepillos", "separadores", "llantas", "rines", "bandas_orugas"]
        ];
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

        header("Location: ../recibo_formato_hoja.php?id=$id");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>