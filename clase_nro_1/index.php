<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Inicio</title>
</head>
<body>
    <h2>Ingresa tu nombre</h2>
    <form action="saludo.php" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>
        <br>
        <button type="submit">Enviar y Saludar</button>
    </form>
</body>
</html>