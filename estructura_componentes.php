<?php
session_start();
include 'conexion.php';

// Agregar componente
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nueva_seccion'], $_POST['nuevo_componente'])) {
    $seccion = trim($_POST['nueva_seccion']);
    $componente = trim($_POST['nuevo_componente']);
    if ($seccion && $componente) {
        $stmt = $conn->prepare("INSERT INTO estructura_recibo_unidad (seccion, componente) VALUES (?, ?)");
        $stmt->bind_param("ss", $seccion, $componente);
        $stmt->execute();
        header("Location: estructura_componentes.php");
        exit;
    }
}

// Eliminar componente
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM estructura_recibo_unidad WHERE id = $id");
    header("Location: estructura_componentes.php");
    exit;
}

// Obtener todos los componentes agrupados
$componentes = [];
$result = $conn->query("SELECT id, seccion, componente FROM estructura_recibo_unidad ORDER BY seccion, componente");
while ($row = $result->fetch_assoc()) {
    $componentes[$row['seccion']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estructura de Componentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7fa; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 30px; }
        .seccion-title { margin-top: 20px; color: #0d6efd; }
        .btn-delete { color: red; text-decoration: none; }
        .form-inline input { margin-right: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>🧩 Administración de Componentes Técnicos</h2>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="text" name="nueva_seccion" class="form-control" placeholder="Sección (ej: MOTOR)" required>
        </div>
        <div class="col-md-5">
            <input type="text" name="nuevo_componente" class="form-control" placeholder="Nombre del componente" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Agregar</button>
        </div>
    </form>

    <?php foreach ($componentes as $seccion => $items): ?>
        <div class="seccion">
            <h5 class="seccion-title"><?= htmlspecialchars($seccion) ?></h5>
            <ul class="list-group mb-3">
                <?php foreach ($items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($item['componente']) ?>
                        <a href="?eliminar=<?= $item['id'] ?>" class="btn-delete" onclick="return confirm('¿Eliminar este componente?')">🗑</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
