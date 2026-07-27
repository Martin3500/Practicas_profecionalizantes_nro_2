<?php
// Carga las constantes de conexión a la base de datos (DB_HOST, DB_USER, DB_PASS, DB_NAME)
include 'config.php';

// Crea la conexión a MySQL usando las constantes definidas en config.php
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica si el formulario fue enviado (si llegaron datos por POST)
if ($_POST) {

    // Toma el nombre que escribió el usuario en el formulario
    $nombre = $_POST['nombre'];

    // Toma el email que escribió el usuario en el formulario
    $email  = $_POST['email'];

    // Cifra la contraseña con bcrypt (PASSWORD_DEFAULT); nunca se guarda en texto plano
    $pass   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Prepara una consulta segura para verificar si el email ya existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");

    // Vincula el email al marcador ? ("s" = string)
    $stmt->bind_param("s", $email);

    // Ejecuta la consulta de búsqueda
    $stmt->execute();

    // fetch_assoc() devuelve la fila encontrada como arreglo, o NULL si no existe
    $user = $stmt->get_result()->fetch_assoc();

    // Libera los recursos de esta consulta
    $stmt->close();

    // Si $user no es NULL, el email ya estaba registrado
    if ($user) {

        // Guarda el mensaje de error en la variable $error
        $error = "El email ya está registrado.";

        // Muestra el error directamente en la página (método básico sin redirigir)
        echo "<p>$error</p>";

    } else {

        // El email no existe: prepara el INSERT con tres marcadores de posición
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");

        // Vincula los tres valores; "sss" significa que los tres son strings
        $stmt->bind_param("sss", $nombre, $email, $pass);

        // Ejecuta el INSERT y verifica si se guardó correctamente
        if ($stmt->execute()) {

            // Muestra mensaje de éxito (aunque el redirect de abajo lo oculta casi inmediatamente)
            echo "Usuario registrado con éxito.";

            // Redirige al login para que el nuevo usuario inicie sesión
            header('location:login.php');

            // Detiene la ejecución para no seguir cargando la página
            exit();
        }

        // Libera los recursos del statement
        $stmt->close();
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
    <title>Mi Sistema</title>
    <!-- Carga Bootstrap 5 desde CDN para usar sus clases de diseño -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<!-- bg-dark = fondo oscuro | text-white = texto blanco (clases de Bootstrap) -->
<body class="bg-dark text-white">

<!-- container = div centrado con márgenes automáticos | mt-5 = margen superior grande -->
<div class="container mt-5">

    <!-- Título del formulario -->
    <h2>Registro de usuarios</h2>

    <!-- Muestra el error si la variable $error fue definida (email duplicado) -->
    <?php if(isset($error)) echo "<p>$error</p>"; ?>

    <!-- Formulario que se envía a esta misma página con método POST -->
    <form method="post">

        <!-- Campo de texto para el nombre; form-control = estilo Bootstrap; mb-2 = margen inferior pequeño -->
        <input class="form-control mb-2" type="text" name="nombre" placeholder="ingrese su nombre" required>

        <!-- Campo de email; type="email" activa validación nativa del navegador -->
        <input class="form-control mb-2" type="email" name="email" placeholder="ingrese su Email" required>

        <!-- Campo de contraseña; type="password" oculta los caracteres mientras se escribe -->
        <input class="form-control mb-2" type="password" name="password" placeholder="ingrese su Password" required>

        <!-- Botón que envía el formulario -->
        <button class="btn btn-primary">Grabar</button>
    </form>

</div>

</body>
</html>
