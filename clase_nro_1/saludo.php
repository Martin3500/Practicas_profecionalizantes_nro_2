<?php
// Verificamos si el nombre fue enviado para evitar errores
if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
    // Usamos htmlspecialchars por seguridad (evita inyección de código malicioso)
    $nombre = htmlspecialchars($_POST['nombre']);
} else {
    // Si alguien entra a esta página sin pasar por el formulario
    $nombre = "Invitado";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Saludo</title>
</head>
<body>
    <h1>¡Hola, <?php echo $nombre; ?>!</h1>
    <p>Bienvenido a la página de destino.</p>
    <br>
    <a href="index.php">Volver al inicio</a>
</body>
</html>