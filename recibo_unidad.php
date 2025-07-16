<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit();
}

include '../conexion.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("❌ ID inválido.");
}

$maquinaria = $conn->query("SELECT * FROM maquinaria WHERE id = $id")->fetch_assoc();
if (!$maquinaria) {
    die("❌ Maquinaria no encontrada.");
}

// Lista de secciones y componentes
$secciones = [
    "MOTOR" => ["Cilindros", "Inyectores", "Radiador", "Turbocargador"],
    "SISTEMA ELÉCTRICO Y ELECTRÓNICO" => ["Luces", "Tablero", "Sensores"],
    "SISTEMA HIDRÁULICO" => ["Bombas", "Mangueras", "Válvulas"],
    "ESTÉTICO" => ["Pintura", "Cabina", "Cristales"],
    "CONSUMIBLES" => ["Aceite", "Filtro de aire", "Filtro de combustible"]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Unidad</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .form-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            padding: 25px;
            margin-bottom: 30px;
        }
        h5 {
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        select {
            border-radius: 10px;
        }

        @media print {
            .btn,
            nav,
            .navbar,
            .form-select,
            textarea {
                display: none !important;
            }

            body {
                background: white;
            }

            .form-section {
                box-shadow: none;
                border: 1px solid #ccc;
            }
        }
    </style>
</head>
<body>

<div class="container my-5">
    <h3 class="mb-4 text-center text-primary">📄 Recibo de Unidad - <?= htmlspecialchars($maquinaria['nombre']) ?></h3>

    <form action="procesar_recibo.php" method="POST">
        <input type="hidden" name="id_maquinaria" value="<?= $id ?>">

        <?php foreach ($secciones as $titulo => $componentes): ?>
            <div class="form-section">
                <h5><?= $titulo ?></h5>
                <div class="row">
                    <?php foreach ($componentes as $comp): ?>
                        <div class="col-md-6 mb-3">
                            <label><strong><?= $comp ?>:</strong></label>
                            <select name="componente[<?= $titulo ?>][<?= $comp ?>]" class="form-select" required>
                                <option value="">-- Selecciona --</option>
                                <option value="bueno">Bueno</option>
                                <option value="regular">Regular</option>
                                <option value="malo">Malo</option>
                            </select>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="form-section">
            <label for="observaciones"><strong>📝 Observaciones:</strong></label>
            <textarea name="observaciones" class="form-control" rows="4" placeholder="Notas adicionales..."></textarea>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">💾 Guardar Evaluación</button>
            <a href="../inventario.php" class="btn btn-secondary">← Cancelar</a>
            <button type="button" class="btn btn-warning" onclick="window.print()">🖨️ Imprimir Recibo</button>
        </div>
    </form>
</div>

</body>
</html>
