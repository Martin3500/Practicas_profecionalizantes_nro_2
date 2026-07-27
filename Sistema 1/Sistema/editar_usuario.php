<?php
// Verifica que el usuario esté logueado; si no hay sesión activa, redirige al login
include 'includes/auth.php';

// Carga las constantes de conexión (DB_HOST, DB_USER, DB_PASS, DB_NAME)
include 'config.php';

// Crea la conexión a MySQL usando las constantes del config
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica que la URL traiga el parámetro ?id= (sin id no sabemos qué usuario editar)
if (!isset($_GET['id'])) {
    // Si no hay id en la URL, vuelve a la lista de usuarios
    header('location: usuarios.php');
    exit();
}

// Convierte el id a entero para prevenir inyección SQL (nunca confiar en datos de la URL)
$id = (int) $_GET['id'];

// Prepara la consulta para obtener los datos actuales del usuario que se va a editar
$stmt = $conn->prepare("SELECT id, nombre, email FROM usuarios WHERE id = ?");

// Vincula el id al marcador ? ("i" = integer)
$stmt->bind_param("i", $id);

// Ejecuta la consulta de búsqueda
$stmt->execute();

// fetch_assoc() devuelve la fila como arreglo asociativo o NULL si no existe ese id
$usuario = $stmt->get_result()->fetch_assoc();

// Libera los recursos del statement
$stmt->close();

// Si no se encontró ningún usuario con ese id, vuelve a la lista
if (!$usuario) {
    header('location: usuarios.php');
    exit();
}

// Variable para mensajes de error; empieza vacía y solo se llena si algo sale mal
$error = '';

// Verifica si el formulario fue enviado (el botón Guardar tiene name="guardar")
if (isset($_POST['guardar'])) {

    // Toma el nuevo nombre ingresado en el formulario
    $nombre = $_POST['nombre'];

    // Toma el nuevo email ingresado en el formulario
    $email  = $_POST['email'];

    // Verifica que el email no lo use OTRO usuario distinto al que estamos editando
    // "AND id != ?" excluye al propio usuario de la verificación (puede conservar su email)
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");

    // Vincula email (string) e id (integer) a los marcadores ?
    $stmt->bind_param("si", $email, $id);

    // Ejecuta la consulta de verificación de duplicado
    $stmt->execute();

    // store_result() guarda el resultado internamente para poder usar num_rows
    $stmt->store_result();

    // Si num_rows > 0, otro usuario ya usa ese email
    if ($stmt->num_rows > 0) {
        $error = 'Ese email ya lo usa otro usuario.';
        $stmt->close();

    } else {
        $stmt->close();

        // Verifica si el usuario ingresó una nueva contraseña (campo no vacío)
        if ($_POST['password'] != '') {

            // Cifra la nueva contraseña con bcrypt antes de guardarla
            $pass_cifrada = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // UPDATE que modifica nombre, email Y contraseña
            $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ? WHERE id = ?");

            // Vincula tres strings y un integer ("sssi")
            $stmt->bind_param("sssi", $nombre, $email, $pass_cifrada, $id);

        } else {

            // Si no se ingresó contraseña, UPDATE que solo modifica nombre y email
            $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");

            // Vincula dos strings y un integer ("ssi")
            $stmt->bind_param("ssi", $nombre, $email, $id);
        }

        // Ejecuta el UPDATE en la base de datos
        $stmt->execute();

        // Libera los recursos del statement
        $stmt->close();

        // Redirige a la lista con mensaje de confirmación en la URL
        header('location: usuarios.php?msg=actualizado');

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
    <title>Editar Usuario</title>
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
        <h2 class="text-center mb-4">Editar Usuario</h2>

        <!-- Alerta de error: solo se muestra si $error tiene contenido -->
        <?php if ($error != '') : ?>
            <!-- htmlspecialchars convierte caracteres especiales para evitar XSS -->
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Formulario; method="post" envía datos en el cuerpo; action="" = esta misma página -->
        <form method="post" action="" class="col-md-6">

            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <!-- value= pre-llena el campo con el nombre actual del usuario -->
                <!-- htmlspecialchars evita que un nombre con comillas rompa el atributo HTML -->
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <!-- value= pre-llena el campo con el email actual del usuario -->
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>

            <div class="mb-3">
                <!-- El texto "(dejar vacío para no cambiarla)" avisa que la contraseña es opcional -->
                <label class="form-label">Nueva contraseña <small class="text-muted">(dejar vacío para no cambiarla)</small></label>
                <!-- No se pre-llena la contraseña actual por seguridad -->
                <input type="password" name="password" class="form-control">
            </div>

            <!-- name="guardar" es lo que detecta isset($_POST['guardar']) en el PHP de arriba -->
            <button type="submit" name="guardar" class="btn btn-primary">Guardar</button>
            <!-- Botón Cancelar: vuelve a la lista sin guardar cambios -->
            <a href="usuarios.php" class="btn btn-secondary ms-2">Cancelar</a>

        </form>
    </div>
</div>

<!-- Carga el JS de Bootstrap (necesario para componentes interactivos) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-YUe2LzesAfftltw+PEaao2tjU/QATaW/rOitAq67e0CT0Zi2VVRL0oC4+gAaeBKu" crossorigin="anonymous"></script>
</body>
</html>
