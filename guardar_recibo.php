<?php
include 'conexion.php';

$maquinaria_id = $_POST['maquinaria_id'];
$campos = ['cilindros','pistones','anillos','inyectores','block','cabeza',
           'transmision','diferenciales','cardan','alarmas','arneses',
           'sistema_hidraulico','estetico','consumibles'];

$valores = [];
$total = 0;
foreach ($campos as $campo) {
    $val = $_POST[$campo];
    $valores[$campo] = $val;

    // Asignar puntaje por condición
    $p = ($val == 'bueno') ? 100 : (($val == 'regular') ? 60 : 30);
    $total += $p;
}
$condicion_total = round($total / count($campos));

// Verificar si ya existe
$existe = $conn->query("SELECT * FROM recibo_unidad WHERE maquinaria_id = $maquinaria_id")->num_rows;
if ($existe) {
    $sql = "UPDATE recibo_unidad SET ";
    foreach ($valores as $k => $v) {
        $sql .= "$k = '$v', ";
    }
    $sql .= "condicion_total = $condicion_total WHERE maquinaria_id = $maquinaria_id";
} else {
    $cols = implode(",", array_keys($valores));
    $vals = implode("','", array_values($valores));
    $sql = "INSERT INTO recibo_unidad (maquinaria_id, $cols, condicion_total)
            VALUES ($maquinaria_id, '$vals', $condicion_total)";
}

$conn->query($sql);

// También actualizar maquinaria
$conn->query("UPDATE maquinaria SET condicion_estimada = $condicion_total WHERE id = $maquinaria_id");

header("Location: index.php?mensaje=recibo_guardado");
?>
