<?php
include 'conexion.php';

// Contadores
$nuevas = $conn->query("SELECT COUNT(*) FROM maquinaria WHERE tipo = 'nueva'")->fetch_row()[0];
$usadas = $conn->query("SELECT COUNT(*) FROM maquinaria WHERE tipo = 'usada'")->fetch_row()[0];

$busqueda = $_GET['busqueda'] ?? '';
$sql = "SELECT * FROM maquinaria WHERE nombre LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%' ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard Maquinaria</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    body { font-family: sans-serif; margin: 0; padding: 0; background: #f9f9f9; }
    header { background: #2c3e50; color: white; padding: 1rem 2rem; text-align: center; }
    .cards { display: flex; justify-content: center; gap: 2rem; margin: 2rem; flex-wrap: wrap; }
    .card { background: white; border-radius: 12px; padding: 1.5rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); min-width: 200px; text-align: center; }
    .card h3 { margin: 0.5rem 0; font-size: 1.2rem; }
    .botones { display: flex; justify-content: center; gap: 1rem; margin-bottom: 1.5rem; }
    .botones a { text-decoration: none; background: #3498db; color: white; padding: 0.6rem 1rem; border-radius: 6px; }
    .buscador { text-align: center; margin-bottom: 1rem; }
    .buscador input[type=text] { padding: 0.5rem; width: 300px; max-width: 90%; border: 1px solid #ccc; border-radius: 6px; }
    table { width: 100%; border-collapse: collapse; margin: 1rem auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 5px rgba(0,0,0,0.1); }
    th, td { padding: 0.8rem; text-align: center; }
    th { background: #34495e; color: white; }
    td img { width: 60px; height: auto; border-radius: 5px; }
    .acciones a { margin: 0 4px; text-decoration: none; color: white; padding: 6px 10px; border-radius: 5px; }
    .editar { background: #27ae60; }
    .eliminar { background: #c0392b; }
    .barra { height: 14px; border-radius: 6px; background: #eee; overflow: hidden; }
    .barra span { display: block; height: 100%; }
    .verde { background: #2ecc71; }
    .amarillo { background: #f1c40f; }
    .rojo { background: #e74c3c; }
    @media (max-width: 768px) {
        .cards { flex-direction: column; align-items: center; }
        table { font-size: 0.9rem; }
        td img { width: 40px; }
    }
</style>
</head>
<body>
<header>
    <h1>Inventario de Maquinaria</h1>
</header>
<div class="cards">
    <div class="card">
        <h3>Maquinaria Nueva</h3>
        <p><strong><?= $nuevas ?></strong></p>
    </div>
    <div class="card">
        <h3>Maquinaria Usada</h3>
        <p><strong><?= $usadas ?></strong></p>
    </div>
</div>
<div class="botones">
    <a href="agregar.php"><i class="fa fa-plus"></i> Agregar Maquinaria</a>
    <a href="#" onclick="installApp()"><i class="fa fa-download"></i> Instalar App</a>
</div>
<div class="buscador">
    <form method="get">
        <input type="text" name="busqueda" placeholder="Buscar maquinaria..." value="<?= htmlspecialchars($busqueda) ?>">
    </form>
</div>
<table>
<tr>
    <th>Imagen</th>
    <th>Nombre</th>
    <th>Modelo</th>
    <th>Año</th>
    <th>Tipo</th>
    <th>Condición</th>
    <th>Acciones</th>
</tr>
<?php while($row = $result->fetch_assoc()): 
    $cond = (int)$row['condicion_estimada'];
    $color = $cond >= 80 ? 'verde' : ($cond >= 50 ? 'amarillo' : 'rojo');
?>
<tr>
    <td><img src="<?= $row['imagen'] ?>" alt="img"></td>
    <td><?= $row['nombre'] ?></td>
    <td><?= $row['modelo'] ?></td>
    <td><?= $row['anio'] ?></td>
    <td><?= ucfirst($row['tipo']) ?></td>
    <td>
        <div class="barra"><span class="<?= $color ?>" style="width:<?= $cond ?>%"></span></div>
        <small><?= $cond ?>%</small>
    </td>
    <td class="acciones">
        <a class="editar" href="editar.php?id=<?= $row['id'] ?>" title="Editar"><i class="fa fa-pen"></i> Editar</a>
        <a class="eliminar" href="eliminar.php?id=<?= $row['id'] ?>" title="Eliminar"><i class="fa fa-trash"></i> Eliminar</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<script>
function installApp() {
    alert('Instalación de app simulada. Requiere soporte PWA.');
}
</script>
</body>
</html>