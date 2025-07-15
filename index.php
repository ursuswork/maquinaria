<?php
include 'conexion.php';

$busqueda = $_GET['busqueda'] ?? '';
$sql = "SELECT * FROM maquinaria WHERE nombre LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%' ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inventario de Maquinaria</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        h2 { text-align: center; }
        form { text-align: center; margin-bottom: 20px; }
        input[type=text] { padding: 5px; width: 300px; }
        input[type=submit], .boton { padding: 6px 12px; margin: 5px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .acciones a { margin: 0 5px; text-decoration: none; color: white; background: #007BFF; padding: 5px 10px; border-radius: 5px; }
        .acciones a:hover { background: #0056b3; }
    </style>
</head>
<body>
<h2>Inventario de Maquinaria</h2>

<form method="get">
    <input type="text" name="busqueda" placeholder="Buscar por nombre o modelo" value="<?= htmlspecialchars($busqueda) ?>">
    <input type="submit" value="Buscar">
    <a href="agregar.php" class="boton" style="background-color: green; color: white;">Agregar Maquinaria</a>
    <a href="exportar_excel.php" class="boton" style="background-color: darkorange; color: white;">Exportar a Excel</a>
</form>

<table>
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Modelo</th>
    <th>Año</th>
    <th>Tipo</th>
    <th>Condición Estimada</th>
    <th>Acciones</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row["id"] ?></td>
    <td><?= $row["nombre"] ?></td>
    <td><?= $row["modelo"] ?></td>
    <td><?= $row["anio"] ?></td>
    <td><?= ucfirst($row["tipo"]) ?></td>
    <td><?= $row["condicion_estimada"] !== null ? $row["condicion_estimada"] . "%" : "-" ?></td>
    <td class="acciones">
        <a href="editar.php?id=<?= $row["id"] ?>">Editar</a>
        <a href="eliminar.php?id=<?= $row["id"] ?>" style="background-color: red;">Eliminar</a>
        <?php if ($row["tipo"] == "usada"): ?>
        <a href="recibo_formato_hoja.php?id=<?= $row["id"] ?>" style="background-color: purple;">Ver Recibo</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
