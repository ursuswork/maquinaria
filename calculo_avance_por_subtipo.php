<?php
$avance = 0;

if ($subtipo === 'bachadora') {
  $query = "SELECT peso FROM avance_bachadora WHERE id_maquinaria = $id";
} elseif ($subtipo === 'esparcidor de sello') {
  $query = "SELECT peso FROM avance_esparcidor WHERE id_maquinaria = $id";
} elseif ($subtipo === 'petrolizadora') {
  $query = "SELECT peso FROM avance_petrolizadora WHERE id_maquinaria = $id";
} else {
  $query = null;
}

if ($query) {
  $resultado_avance = $conn->query($query);
  if ($resultado_avance) {
    while ($etapa = $resultado_avance->fetch_assoc()) {
      $avance += intval($etapa['peso']);
    }
  }
}
?>
