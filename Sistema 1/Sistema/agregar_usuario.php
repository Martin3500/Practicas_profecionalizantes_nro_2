<?php
// Verifica que el usuario esté logueado; si no hay sesión activa, redirige al login
include 'includes/auth.php';

// Carga las constantes de conexión (DB_HOST, DB_USER, DB_PASS, DB_NAME)
include 'config.php';

// Crea la conexión a MySQL usando las constantes del config
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Variable para mensajes de error; empieza vacía y solo se llena si algo sale mal
$error = '';

// Verifica si el formulario fue enviado (el botón Guardar tiene name="guardar")
if (isset($_POST['guardar'])) {

    // Toma el nombre que escribió el usuario en el formulario
    $nombre   = $_POST['nombre'];

    // Toma el email que escribió el usuario en el formulario
    $email    = $_POST['email'];

    // Toma la contraseña que escribió el usuario en el formulario
    $password = $_POST['password'];

    // Prepara una consulta segura para verificar si el email ya existe en la tabla
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");

    // Vincula el email al marcador ? ("s" = string)
    $stmt->bind_param("s", $email);

    // Ejecuta la consulta de búsqueda
    $stmt->execute();

    // store_result() guarda internamente el resultado para poder usar num_rows
    $stmt->store_result();

    // num_rows contiene la cantidad de filas encontradas; si es > 0 el email ya existe
    if ($stmt->num_rows > 0) {

        // El email ya está registrado: guarda el mensaje de error
        $error = 'Ese email ya está registrado. Usá otro.';

        // Libera los recursos del statement
        $stmt->close();

    } else {

        // El email no existe: libera el statement de búsqueda antes de crear uno nuevo
        $stmt->close();

        // Cifra la contraseña con bcrypt (PASSWORD_DEFAULT); nunca se guarda en texto plano
        $pass_cifrada = password_hash($password, PASSWORD_DEFAULT);

        // Prepara el INSERT con tres marcadores de posición (nombre, email, password)
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");

        // Vincula los tres valores; "sss" significa que los tres son strings
        $stmt->bind_param("sss", $nombre, $email, $pass_cifrada);

        // Ejecuta el INSERT para guardar el nuevo usuario en la base de datos
        $stmt->execute();

        // Libera los recursos del statement
        $stmt->close();

        // Redirige a la lista de usuarios con el mensaje de confirmación en la URL
        header('location: usuarios.php?msg=agregado');

        // Detiene la ejecución para no seguir cargando esta página
        exit();
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
    <!-- Carga Bootstrap 5 desde CDN para usar sus clases de diseño -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Agregar Usuario</title>
    <style>
        /* El sidebar ocupa toda la altura de la pantalla con fondo negro y ancho mínimo */
        .sidebar { height: 100vh; background: #111; color: white; min-width: 180px; }
    </style>
</head>
<body>

<!-- d-flex: pone el sidebar y el contenido uno al lado del otro (layout en fila) -->
<div class="d-flex">

    <!-- Barra lateral de navegación -->
    <div class="sidebar p-3">
        <h5>Panel</h5>
        <a href="dashboard.php" class="text-white d-block mb-2">Dashboard</a>
        <a href="usuarios.php"  class="text-white d-block mb-2">Usuarios</a>
        <a href="proveedores.php" class="text-white d-block mb-2">Proveedores</a>
        <a href="productos.php" class="text-white d-block mb-2">Productos</a>
        <a href="logout.php"    class="text-white d-block mb-2">Logout</a>
    </div>

    <!-- Área de contenido principal -->
    <div class="p-4 w-100">
        <!-- Título de la sección, centrado -->
        <h2 class="text-center mb-4">Agregar Usuario</h2>

        <!-- Alerta de error: solo se muestra si $error tiene contenido -->
        <?php if ($error != '') : ?>
            <!-- alert-danger = fondo rojo de Bootstrap -->
            <!-- htmlspecialchars convierte caracteres especiales para evitar XSS -->
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Formulario; method="post" envía datos en el cuerpo; action="" = esta misma página -->
        <form method="post" action="" class="col-md-6">

            <!-- Campo de nombre -->
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <!-- name="nombre" debe coincidir con $_POST['nombre'] en el PHP de arriba -->
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <!-- Campo de email; type="email" activa la validación nativa del navegador -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <!-- Campo de contraseña; type="password" oculta los caracteres mientras se escribe -->
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <!-- name="guardar" es lo que detecta isset($_POST['guardar']) en el PHP de arriba -->
            <button type="submit" name="guardar" class="btn btn-primary">Guardar</button>
            <!-- Botón Cancelar: vuelve a la lista sin guardar nada -->
            <a href="usuarios.php" class="btn btn-secondary ms-2">Cancelar</a>

        </form>
    </div>
</div>

<!-- Carga el JS de Bootstrap (necesario para componentes interactivos) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-YUe2LzesAfftltw+PEaao2tjU/QATaW/rOitAq67e0CT0Zi2VVRL0oC4+gAaeBKu" crossorigin="anonymous"></script>
</body>
</html>
