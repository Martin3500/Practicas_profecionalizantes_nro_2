<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form action="registro.php" method="POST" class="form-registro">
            <h2>Crear Cuenta</h2>
            <p>Únete a nuestra comunidad hoy mismo.</p>
            
            <div class="input-group">
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan Pérez" required>
            </div>

            <div class="input-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Min. 8 caracteres" required>
            </div>

            <button type="submit" class="btn-enviar">Registrarse</button>
            
            <p class="footer-text">¿Ya tienes cuenta? <a href="#">Inicia sesión</a></p>
        </form>
    </div>
</body>
</html>