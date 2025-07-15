<?php
include 'conexion.php';

$nuevas = $conn->query("SELECT COUNT(*) FROM maquinaria WHERE tipo = 'nueva'")->fetch_row()[0];
$usadas = $conn->query("SELECT COUNT(*) FROM maquinaria WHERE tipo = 'usada'")->fetch_row()[0];

$busqueda = $_GET['busqueda'] ?? '';
$sql = "SELECT * FROM maquinaria WHERE nombre LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%' OR ubicacion LIKE '%$busqueda%' ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inventario de Maquinaria</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { font-family: sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
header { background: #1c2d48; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
h1 { margin: 0; font-size: 1.5rem; }
header .boton { background: #2ecc71; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; color: white; font-weight: bold; }

.tarjetas { display: flex; gap: 1rem; margin: 1.5rem; flex-wrap: wrap; justify-content: center; }
.card { flex: 1; min-width: 200px; border-radius: 12px; padding: 1rem; color: white; text-align: center; font-size: 1.2rem; }
.azul { background: #2980b9; }
.verde { background: #27ae60; }

.botones { display: flex; justify-content: center; gap: 1rem; margin-top: 1rem; flex-wrap: wrap; }
.botones a { text-decoration: none; background: #2ecc71; color: white; padding: 0.6rem 1rem; border-radius: 8px; font-weight: bold; }

.buscador { text-align: center; margin: 1rem; }
.buscador input[type=text] { padding: 0.6rem; width: 280px; max-width: 90%; border: 1px solid #ccc; border-radius: 6px; }

table { width: 95%; margin: 1rem auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1); border-collapse: collapse; }
th, td { padding: 0.8rem; text-align: center; border-bottom: 1px solid #ddd; }
th { background: #ecf0f1; font-weight: bold; }
td img { width: 60px; border-radius: 5px; }

.acciones a { margin: 0 4px; color: white; padding: 6px 10px; border-radius: 5px; display: inline-block; }
.editar { background: #3498db; }
.eliminar { background: #e74c3c; }
.ver { background: #9b59b6; }

.barra { background: #ddd; border-radius: 10px; height: 14px; overflow: hidden; }
.barra span { display: block; height: 100%; }
.verde-barra { background: #2ecc71; }
.amarilla-barra { background: #f1c40f; }
.roja-barra { background: #e74c3c; }

@media (max-width: 768px) {
    table { font-size: 0.85rem; }
    td img { width: 40px; }
}
</style>
</head>
<body>
<header>
    <h1>Inventario de Maquinaria</h1>
    <a href="#" class="boton" onclick="installApp()">Instalar App</a>
</header>

<div class="tarjetas">
    <div class="card azul">
        Maquinaria Nueva<br><strong><?= $nuevas ?></strong>
    </div>
    <div class="card verde">
        Maquinaria Usada<br><strong><?= $usadas ?></strong>
    </div>
</div>

<div class="botones">
    <a href="agregar.php"><i class="fa fa-plus"></i> Agregar Maquinaria</a>
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
    <th>Tipo</th>
    <th>Modelo</th>
    <th>Ubicación</th>
    <th>Condición</th>
    <th>Acciones</th>
</tr>
<?php while($row = $result->fetch_assoc()):
    $cond = (int)$row['condicion_estimada'];
    $color = $cond >= 80 ? 'verde-barra' : ($cond >= 50 ? 'amarilla-barra' : 'roja-barra');
?>
<tr>
    <td><img src="<?= $row['imagen'] ?>" alt="img"></td>
    <td><?= $row['nombre'] ?></td>
    <td><?= ucfirst($row['tipo']) ?></td>
    <td><?= $row['modelo'] ?></td>
    <td><?= $row['ubicacion'] ?></td>
    <td>
        <div class="barra"><span class="<?= $color ?>" style="width:<?= $cond ?>%"></span></div>
        <small><?= $cond ?>%</small>
    </td>
    <td class="acciones">
        <a class="editar" href="editar.php?id=<?= $row['id'] ?>"><i class="fa fa-pen"></i></a>
        <a class="eliminar" href="eliminar.php?id=<?= $row['id'] ?>"><i class="fa fa-trash"></i></a>
        <?php if ($row["tipo"] == "usada"): ?>
        <a class="ver" href="recibo_formato_hoja.php?id=<?= $row['id'] ?>"><i class="fa fa-file-alt"></i></a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>

<script>
function installApp() {
  alert("Funcionalidad PWA: Instalar App (simulada)");
}
</script>
</body>
</html>
