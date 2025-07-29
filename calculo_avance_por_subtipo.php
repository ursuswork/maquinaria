<?php
$avance = 0;

if ($subtipo === 'bachadora') {
  $sql_avance = "SELECT etapa, peso FROM avance_bachadora WHERE id_maquinaria = $id";
} elseif ($subtipo === 'esparcidor de sello') {
  $sql_avance = "SELECT etapa, peso FROM avance_esparcidor WHERE id_maquinaria = $id";
} elseif ($subtipo === 'petrolizadora') {
  $sql_avance = "SELECT etapa, peso FROM avance_petrolizadora WHERE id_maquinaria = $id";
}

if (isset($sql_avance)) {
  $res_avance = $conn->query($sql_avance);
  while ($fila_av = $res_avance->fetch_assoc()) {
    $avance += intval($fila_av['peso']);
  }
}
?>
