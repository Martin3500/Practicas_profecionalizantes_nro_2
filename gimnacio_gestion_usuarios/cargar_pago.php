<?php
require "conexion.php";

// Obtener el id que viene en la URL (ej: cargar_pago.php?id=3)
$id = (int) $_GET['id'];

// Si el id no es válido, regresamos al index
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Cargamos los datos actuales del cliente para mostrarlos en el formulario
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

// Procesar el formulario cuando se presiona "Confirmar Pago"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clases_a_sumar = (int) $_POST['clases_a_sumar'];
    $nombre_usuario = $usuario['nombre'];
    $DNI_usuario = $usuario['DNI'];
    // Validamos que por lo menos sume 1 clase
    if ($clases_a_sumar > 0) {
        // Incrementamos el contador directamente en la consulta SQL (clasesdisponibles = clasesdisponibles + ?)
        // Esto es más seguro y evita errores si el dato cambió en milisegundos
        $actualizar = $conn->prepare("UPDATE clientes SET clasesdisponibles = clasesdisponibles + ? WHERE id = ?");
        $actualizar->bind_param("ii", $clases_a_sumar, $id);
        $actualizar->execute();
        // REGISTRO EN HISTORIAL
        $accion_texto = "Cargó pago: +$clases_a_sumar clases a $nombre_usuario con DNI: $DNI_usuario";
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
    <title>Cargar Pago - GymFlow</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Cargar Pago / Añadir Clases</h2>

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
            <label class="form-label">Cantidad de clases a sumar</label>
            <input type="number" name="clases_a_sumar" class="form-control" min="1" max="365" placeholder="Ej: 1, 8, 12, 20" required>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-warning">Confirmar Pago y Sumar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>