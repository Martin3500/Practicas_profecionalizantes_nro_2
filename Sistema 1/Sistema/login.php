<?php
// session_start() activa el sistema de sesiones para poder guardar datos del usuario después del login
session_start();

// Carga las constantes de conexión (DB_HOST, DB_USER, DB_PASS, DB_NAME)
include 'config.php';

// Crea la conexión a MySQL usando las constantes del config
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Variable para mensajes de error (credenciales incorrectas)
$error = '';

// Variable para mensajes de éxito (no se usa actualmente pero está reservada)
$exito = '';

// Verifica si el formulario fue enviado (si llegaron datos por POST)
if ($_POST) {

    // Toma el email que escribió el usuario
    $email = $_POST['email'];

    // Toma la contraseña que escribió el usuario
    $pass  = $_POST['password'];

    // Prepara una consulta segura para buscar el usuario por su email
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");

    // Vincula el email al marcador ? ("s" = string)
    $stmt->bind_param("s", $email);

    // Ejecuta la consulta de búsqueda
    $stmt->execute();

    // get_result() obtiene el resultado completo; fetch_assoc() lo convierte en arreglo
    // Si el email no existe, $user será NULL
    $user = $stmt->get_result()->fetch_assoc();

    // Libera los recursos del statement
    $stmt->close();

    // Verifica dos cosas: que el usuario exista Y que la contraseña coincida con el hash guardado
    // password_verify() compara el texto plano con el hash bcrypt de la base de datos
    if ($user && password_verify($pass, $user['password'])) {

        // Login exitoso: guarda todos los datos del usuario en la sesión
        $_SESSION['user'] = $user;

        // Redirige al área protegida
        header('location:dashboard.php');

        // Detiene la ejecución para no seguir cargando esta página
        exit();

    } else {

        // Email no encontrado o contraseña incorrecta: guarda el mensaje de error
        $error = "Los datos son incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Codificación UTF-8: permite tildes, ñ y otros caracteres especiales -->
    <meta charset="UTF-8">
    <!-- viewport: hace la página responsive en pantallas de celular -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título que aparece en la pestaña del navegador -->
    <title>Mi Sistema</title>
    <!-- Carga Bootstrap 5 desde CDN para usar sus clases de diseño -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        body{
            /* min-height: 100vh asegura que el body ocupe al menos toda la altura de la pantalla */
            min-height: 100vh;
            /* Fondo degradado diagonal de azul muy oscuro a azul a celeste */
            background: linear-gradient(135deg,#020024 0%, #090979 75%, #00D4FF 100%);
            /* display:flex + justify-content:center + align-items:center centra la tarjeta */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card{
            /* Quita el borde gris predeterminado de Bootstrap */
            border: none;
            /* Redondea las cuatro esquinas */
            border-radius: 1rem;
            /* Sombra oscura para dar efecto de tarjeta flotante */
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.4);
        }
        .card-header{
            /* Color de fondo azul oscuro para el encabezado de la tarjeta */
            background: #090979;
            /* Redondea solo las dos esquinas superiores para que combine con .card */
            border-radius: 1rem 1rem 0 0 !important;
            text-align: center;
            padding: 1rem
        }
        .card-body{
            /* Espaciado interno generoso para el cuerpo de la tarjeta */
            padding: 2rem;
        }
        .btn-primary{
            /* Sobreescribe el azul predeterminado de Bootstrap por azul muy oscuro */
            background: #020024;
            border-color: #020024;
        }
        .btn-primary:hover{
            /* Color al pasar el mouse: azul medio (más claro que el estado normal) */
            background: #090979;
            border-color: #090979;
        }
        .form-control:focus{
            /* Color del borde cuando el campo de texto está activo (con foco) */
            border-color: #090979;
            /* Sombra azul suave alrededor del campo activo */
            box-shadow: 0 0 0 .2rem rgba(15, 52, 96, 0.25);
        }
        .icono{
            /* Tamaño del contenedor del emoji del candado */
            width: 64px;
            height: 64px;
            /* border-radius: 50% convierte el div rectangular en un círculo */
            border-radius: 50%;
            /* Flexbox centra el emoji dentro del círculo */
            display: flex;
            justify-content: center;
            align-items: center;
            /* Tamaño del emoji dentro del círculo */
            font-size: 1.8rem;
        }
    </style>
</head>
<body>

<!-- Contenedor que limita el ancho del formulario a 420px y lo centra -->
<div style="width: 100%; max-width: 420px; padding: 1rem;">

    <!-- Tarjeta Bootstrap que envuelve todo el formulario -->
    <div class="card">

        <!-- Encabezado de la tarjeta: ícono, título y subtítulo -->
        <div class="card-header text-white">

            <!-- Contenedor circular del emoji candado -->
            <div class="icono">&#128274;</div>

            <!-- Título principal -->
            <h4 class="mb-0 fw-bold">Mi Sistema</h4>

            <!-- Subtítulo en gris claro semitransparente -->
            <p class="mb-0 text-white-50 small">Ingresar sus credenciales para continuar</p>
        </div>

        <!-- Cuerpo de la tarjeta: alertas + formulario -->
        <div class="card-body">

            <!-- Alerta verde de éxito: solo se muestra si $exito no está vacío -->
            <?php if ($exito) : ?>
                <div class="alert alert-success py-2">
                    <!-- htmlspecialchars previene XSS convirtiendo < > & en entidades HTML -->
                    <small><?= htmlspecialchars($exito) ?></small>
                </div>
            <?php endif; ?>

            <!-- Alerta roja de error: solo se muestra si $error no está vacío -->
            <?php if ($error) : ?>
                <div class="alert alert-danger py-2">
                    <small><?= htmlspecialchars($error) ?></small>
                </div>
            <?php endif; ?>

            <!-- Formulario que se envía a esta misma página con método POST -->
            <form method="post">

                <!-- Campo de email; type="email" activa la validación nativa del navegador -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <!-- autofocus pone el cursor automáticamente en este campo al cargar la página -->
                    <input type="email" class="form-control" name="email" placeholder="ingrese su Email" required autofocus>
                </div>

                <!-- Campo de contraseña; type="password" oculta los caracteres mientras se escribe -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Contraseña</label>
                    <input type="password" class="form-control" name="password" placeholder="ingrese su Contraseña" required>
                </div>

                <!-- Botón de envío; w-100 = ancho completo; py-2 = padding vertical -->
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Ingresar</button>
            </form>
        </div>

        <!-- Pie de tarjeta con enlace para registrarse -->
        <div class="card-footer bg-light text-center py-3 rounded-bottom">
            <small class="text-muted">No estas registrado?
                <!-- Enlace a la página de registro -->
                <a href="registro.php" class="fw-semibold text-decoration-none">Registrarse</a>
            </small>
        </div>
    </div>
</div>

</body>
</html>
