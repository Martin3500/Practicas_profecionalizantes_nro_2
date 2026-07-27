<?php
// Verifica que el usuario esté logueado; si no hay sesión activa, redirige al login
include 'includes/auth.php';

// Carga las constantes de conexión (DB_HOST, DB_USER, DB_PASS, DB_NAME)
include 'config.php';

// Crea la conexión a MySQL usando las constantes del config
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Variable para el texto de la alerta de feedback (vacía al inicio)
$mensaje = '';

// Variable para el tipo/color de la alerta Bootstrap (vacía al inicio)
$tipo    = '';

// Verifica si la URL trae el parámetro ?msg= (lo envían otras páginas al redirigir)
if (isset($_GET['msg'])) {

    // Si el valor es 'actualizado', configura la alerta verde de edición exitosa
    if ($_GET['msg'] === 'actualizado') {
        $mensaje = 'Usuario actualizado correctamente.';
        $tipo    = 'success'; // Bootstrap: fondo verde

    // Si el valor es 'agregado', configura la alerta verde de nuevo registro
    } elseif ($_GET['msg'] === 'agregado') {
        $mensaje = 'Usuario agregado correctamente.';
        $tipo    = 'success';

    // Si el valor es 'eliminado', configura la alerta amarilla de eliminación
    } elseif ($_GET['msg'] === 'eliminado') {
        $mensaje = 'Usuario eliminado correctamente.';
        $tipo    = 'warning'; // Bootstrap: fondo amarillo
    }
}

// Verifica si la URL trae ?accion=eliminar&id=N para borrar un usuario
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar' && isset($_GET['id'])) {

    // Convierte el id a entero para prevenir inyección SQL
    $id = (int) $_GET['id'];

    // Prepara la consulta DELETE de forma segura con marcador de posición
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");

    // Vincula el id al marcador ? ("i" = integer)
    $stmt->bind_param("i", $id);

    // Ejecuta el DELETE en la base de datos
    $stmt->execute();

    // Libera los recursos del statement
    $stmt->close();

    // Redirige a esta misma página con el mensaje de confirmación en la URL
    header('location: usuarios.php?msg=eliminado');

    // Detiene la ejecución para no seguir cargando la página
    exit();
}

// Obtiene todos los usuarios de la base de datos, ordenados por id de menor a mayor
$resultado = $conn->query("SELECT * FROM usuarios ORDER BY id ASC");
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
    <title>Usuarios</title>
    <style>
        .sidebar {
            /* El sidebar ocupa toda la altura visible de la pantalla */
            height: 100vh;
            background: #111;
            color: white;
            /* Ancho mínimo para que el sidebar no se encoja con pantallas pequeñas */
            min-width: 180px;
        }
    </style>
</head>
<body>

<!-- d-flex: pone el sidebar y el contenido uno al lado del otro (layout en fila) -->
<div class="d-flex">

    <!-- Barra lateral de navegación -->
    <div class="sidebar p-3">
        <h5>Panel</h5>
        <a href="dashboard.php" class="text-white d-block mb-2">Dashboard</a>
        <!-- href="#" porque esta ES la página de Usuarios (enlace inactivo) -->
        <a href="#"  class="text-white d-block mb-2">Usuarios</a>
        <a href="proveedores.php" class="text-white d-block mb-2">Proveedores</a>
        <a href="productos.php" class="text-white d-block mb-2">Productos</a>
        <a href="logout.php"    class="text-white d-block mb-2">Logout</a>
    </div>

    <!-- Área de contenido principal; p-4 = padding, w-100 = ocupa todo el ancho restante -->
    <div class="p-4 w-100">
        <!-- Título de la sección, centrado -->
        <h2 class="text-center mb-4">Gestión de Usuarios</h2>

        <!-- Alerta de feedback: solo se renderiza si $mensaje tiene contenido -->
        <?php if ($mensaje != '') : ?>
            <!-- alert-<?= $tipo ?> cambia el color: success=verde, warning=amarillo -->
            <!-- alert-dismissible + fade + show permiten cerrar la alerta con el botón X -->
            <div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
                <!-- htmlspecialchars convierte < > & en entidades HTML para prevenir XSS -->
                <strong><?= htmlspecialchars($mensaje) ?></strong>
                <!-- Botón X para cerrar la alerta; data-bs-dismiss="alert" lo activa Bootstrap JS -->
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Botón que lleva al formulario de agregar nuevo usuario -->
        <a href="agregar_usuario.php" class="btn btn-primary mb-3">Agregar Usuario</a>

        <!-- Tabla con bordes, filas alternadas y efecto hover al pasar el mouse -->
        <table class="table table-bordered table-striped table-hover align-middle">
            <!-- Encabezado con fondo oscuro -->
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <!-- style="width:180px" fija el ancho de la columna de acciones -->
                    <th class="text-center" style="width:180px">Acciones</th>
                </tr>
            </thead>
            <tbody>

                <!-- Verifica que la consulta devolvió resultados y que hay al menos una fila -->
                <?php if ($resultado && $resultado->num_rows > 0) : ?>

                    <!-- Loop: itera una vez por cada usuario devuelto por la consulta -->
                    <?php while ($usuario = $resultado->fetch_assoc()) : ?>
                        <tr>
                            <!-- Muestra el id del usuario -->
                            <td><?= $usuario['id'] ?></td>
                            <!-- htmlspecialchars evita que un nombre con < > rompa el HTML -->
                            <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                            <!-- Muestra el email del usuario -->
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td class="text-center">
                                <!-- Botón Editar: pasa el id del usuario por la URL -->
                                <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                                <!-- Botón Eliminar: pasa la acción y el id por la URL -->
                                <!-- onclick pide confirmación en el navegador antes de eliminar -->
                                <a href="usuarios.php?accion=eliminar&id=<?= $usuario['id'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Eliminar al usuario <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES) ?>?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <!-- Si la consulta no devolvió ninguna fila, muestra mensaje vacío -->
                <?php else : ?>
                    <tr>
                        <!-- colspan="4" hace que la celda abarque las 4 columnas de la tabla -->
                        <td colspan="4" class="text-center text-muted">No hay usuarios registrados.</td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<!-- Carga el JS de Bootstrap (necesario para la alerta con botón de cerrar) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-YUe2LzesAfftltw+PEaao2tjU/QATaW/rOitAq67e0CT0Zi2VVRL0oC4+gAaeBKu" crossorigin="anonymous"></script>
</body>
</html>
