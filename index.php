<?php
include 'conexion.php';
$result = $conn->query("SELECT * FROM maquinaria ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inventario de Maquinaria</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h2>Inventario de Maquinaria</h2>
<table>
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Modelo</th>
    <th>Año</th>
    <th>Empresa Origen</th>
    <th>Empresa Destino</th>
    <th>Tipo</th>
    <th>Condición Estimada</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row["id"] ?></td>
    <td><?= $row["nombre"] ?></td>
    <td><?= $row["modelo"] ?></td>
    <td><?= $row["anio"] ?></td>
    <td><?= $row["empresa_origen"] ?></td>
    <td><?= $row["empresa_destino"] ?></td>
    <td><?= ucfirst($row["tipo"]) ?></td>
    <td><?= $row["condicion_estimada"] !== null ? $row["condicion_estimada"] . "%" : "-" ?></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
