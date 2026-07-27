<?php
require "conexion.php";

// Obtener el id que viene en la URL (ej: descontar_clase.php?id=3)
$id = (int) $_GET['id'];

// Si el id no es válido, regresamos al index
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Cargamos los datos actuales del cliente para ver cuántas clases le quedan
$sentencia = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$sentencia->bind_param("i", $id);
$sentencia->execute();
$resultado = $sentencia->get_result();
$usuario = $resultado->fetch_assoc();

// Si no existe el cliente, volvemos al index
if (!$usuario) {
    header("Location: index.php");
    exit;
}

// Procesar el formulario cuando se presiona "Descontar Clases"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clases_a_restar = (int) $_POST['clases_a_restar'];
    $nombre_usuario = $usuario['nombre'];
    $DNI_usuario = $usuario['DNI'];

    // Validamos que por lo menos intente restar 1 clase
    if ($clases_a_restar > 0) {
        // Restamos las clases directamente en la base de datos.
        // Usamos la función GREATEST(0, clasesdisponibles - ?) para asegurarnos de que 
        // el contador NUNCA quede en números negativos si se descuentan de más por error.
        $actualizar = $conn->prepare("UPDATE clientes SET clasesdisponibles = GREATEST(-10, clasesdisponibles - ?) WHERE id = ?");
        $actualizar->bind_param("ii", $clases_a_restar, $id);
        $actualizar->execute();
        // REGISTRO EN HISTORIAL
        $accion_texto = "Asistencia: -$clases_a_restar clases $nombre_usuario con DNI: $DNI_usuario";
        $log = $conn->prepare("INSERT INTO historial (DNI, accion) VALUES (?, ?)");
        $log->bind_param("is", $usuario['DNI'], $accion_texto);
        $log->execute();
    }

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Descontar Clases - GymFlow</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Asistencia / Descontar Clases</h2>

    <form action="" method="post" class="form-container">
        
        <div class="form-group">
            <label class="form-label">Cliente</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" disabled>
        </div>

        <div class="form-group">
            <label class="form-label">Clases Disponibles Actuales</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['clasesdisponibles']) ?>" disabled>
        </div>

        <div class="form-group">
            <label class="form-label">Cantidad de clases a descontar</label>
            <input type="number" name="clases_a_restar" class="form-control" min="1" value="1" required>
        </div>

        <div class="form-group">
            <?php if ($usuario['clasesdisponibles'] > -10): ?>
                <button type="submit" class="btn btn-danger">Descontar e Ingresar</button>
            <?php else: ?>
                <button type="button" class="btn btn-secondary" style="cursor: not-allowed;" disabled>Sin clases disponibles</button>
            <?php endif; ?>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>