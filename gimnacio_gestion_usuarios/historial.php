<?php
require "conexion.php";

// Hacemos un LEFT JOIN para traer el nombre del cliente si es que todavía existe en la base de datos
$resultado = $conn->query("
    SELECT h.*, c.nombre 
    FROM historial h
    LEFT JOIN clientes c ON h.DNI = c.DNI
    ORDER BY h.transaccion DESC
");

$total = $resultado->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial General - GymFlow</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h1>Historial General de Transacciones</h1>
        <a href="index.php" class="btn btn-secondary">Volver al Panel</a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Transacción</th>
                    <th>DNI</th>
                    <th>Cliente</th>
                    <th>Acción Realizada</th>
                    <th>Fecha y Hora</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($total > 0): while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($fila['transaccion']) ?></strong></td>
                    <td>
                        <?= $fila['DNI'] ? htmlspecialchars($fila['DNI']) : '<span class="text-muted"><i>N/A</i></span>' ?>
                    </td>
                    <td>
                        <?= $fila['nombre'] ? htmlspecialchars($fila['nombre']) : '<span class="text-muted"><i>Cliente eliminado</i></span>' ?>
                    </td>
                    <td><?= htmlspecialchars($fila['accion']) ?></td>
                    <td><?= htmlspecialchars($fila['fechaYahora']) ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        No se registran movimientos ni transacciones en el sistema aún.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>