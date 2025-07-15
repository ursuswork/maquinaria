<?php
include 'conexion.php';
$id = $_GET['id'] ?? null;
if (!$id) { die("ID no proporcionado."); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Formulario Recibo Unidad</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f4f6f9; font-family: Arial, sans-serif; }
.container { background: white; padding: 30px; border-radius: 12px; margin-top: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
h2 { text-align: center; margin-bottom: 30px; }
h4 { background: #dee2e6; padding: 10px; border-radius: 6px; margin-top: 30px; }
.campo { margin-bottom: 10px; }
label { font-weight: bold; }
.opciones label { font-weight: normal; margin-right: 15px; }
</style>
</head>
<body>
<div class="container">
    <h2>Formulario Recibo de Unidad</h2>
    <form method="POST" action="acciones/guardar_recibo.php?id=<?= $id ?>">
        <!-- Secciones dinámicas con campos comunes -->
        <?php
        function grupo($titulo, $campos) {
            echo "<h4>$titulo</h4><div class='row'>";
            foreach ($campos as $campo) {
                echo "<div class='col-md-6 campo'>
                        <label>".strtoupper(str_replace("_", " ", $campo))."</label>
                        <div class='opciones'>
                            <label><input type='radio' name='$campo' value='bueno' required> Bueno</label>
                            <label><input type='radio' name='$campo' value='regular'> Regular</label>
                            <label><input type='radio' name='$campo' value='malo'> Malo</label>
                        </div>
                    </div>";
            }
            echo "</div>";
        }

        grupo("MOTOR", ["cilindros", "pistones", "anillos", "inyectores", "block", "cabeza", "varillas", "resortes", "punterias", "cigueñal", "arbol_de_elevas", "retenes", "ligas", "sensores", "poleas", "concha", "cremallera", "clutch", "coples", "bomba_de_inyeccion", "juntas", "marcha", "tuberia", "alternador", "filtros", "bases", "soportes", "turbo", "escape", "chicotes"]);
        grupo("SISTEMA MECÁNICO", ["transmision", "diferenciales", "cardan"]);
        grupo("SISTEMA HIDRÁULICO", ["banco_de_valvulas", "bombas_de_transito", "bombas_de_precarga", "bombas_de_accesorios", "coples_hidraulicos", "clutch_hidraulico", "gatos_de_levante", "gatos_de_direccion", "gatos_de_accesorios", "mangueras", "motores_hidraulicos", "orbitrol", "torques_huv", "valvulas_de_retencion", "reductores"]);
        grupo("SISTEMA ELÉCTRICO Y ELECTRÓNICO", ["alarmas", "arneses", "bobinas", "botones", "cables", "cables_de_sensores", "conectores", "electro_valvulas", "fusibles", "porta_fusibles", "indicadores", "presion_agua_temp_voltimetro", "luces", "modulos", "torreta", "relevadores", "switch_llave", "sensores_electricos"]);
        grupo("ESTÉTICO", ["pintura", "calcomanias", "asiento", "tapiceria", "tolvas", "cristales", "accesorios", "sistema_de_riego"]);
        grupo("CONSUMIBLES", ["puntas", "porta_puntas", "garras", "cuchillas", "cepillos", "separadores", "llantas", "rines", "bandas_orugas"]);
        ?>
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success px-4 py-2">Guardar Recibo</button>
            <a href="index.php" class="btn btn-secondary px-4 py-2">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
