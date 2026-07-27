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
        $mensaje = 'Producto actualizado correctamente.';
        $tipo    = 'success'; // Bootstrap: fondo verde

    // Si el valor es 'agregado', configura la alerta verde de nuevo registro
    } elseif ($_GET['msg'] === 'agregado') {
        $mensaje = 'Producto agregado correctamente.';
        $tipo    = 'success';

    // Si el valor es 'eliminado', configura la alerta amarilla de eliminación
    } elseif ($_GET['msg'] === 'eliminado') {
        $mensaje = 'Producto eliminado correctamente.';
        $tipo    = 'warning'; // Bootstrap: fondo amarillo
    }
}

// Verifica si la URL trae ?accion=eliminar&id_prod=N para borrar un producto
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar' && isset($_GET['id_prod'])) {

    // Convierte el id_prod a entero para prevenir inyección SQL
    $id_prod = (int) $_GET['id_prod'];

    // Prepara la consulta DELETE de forma segura con marcador de posición
    $stmt = $conn->prepare("DELETE FROM productos WHERE id_prod = ?");

    // Vincula el id_prod al marcador ? ("i" = integer)
    $stmt->bind_param("i", $id_prod);

    // Ejecuta el DELETE en la base de datos
    $stmt->execute();

    // Libera los recursos del statement
    $stmt->close();

    // Redirige a esta misma página con el mensaje de confirmación en la URL
    header('location: productos.php?msg=eliminado');

    // Detiene la ejecución para no seguir cargando la página
    exit();
}

// Obtiene todos los productos de la base de datos, ordenados por id_prod de menor a mayor
$resultado = $conn->query("SELECT * FROM productos ORDER BY id_prod ASC");
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
    <title>Productos</title>
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
        <a href="usuarios.php"  class="text-white d-block mb-2">Usuarios</a>
        <a href="proveedores.php" class="text-white d-block mb-2">Proveedores</a>
        <!-- href="#" porque esta ES la página de Productos (enlace inactivo) -->
        <a href="#" class="text-white d-block mb-2">Productos</a>
        <a href="logout.php"    class="text-white d-block mb-2">Logout</a>
    </div>

    <!-- Área de contenido principal; p-4 = padding, w-100 = ocupa todo el ancho restante -->
    <div class="p-4 w-100">
        <!-- Título de la sección, centrado -->
        <h2 class="text-center mb-4">Gestión de productos</h2>

        <!-- Alerta de feedback: solo se renderiza si $mensaje tiene contenido -->
        <?php if ($mensaje != '') : ?>
            <!-- alert-<?= $tipo ?> cambia el color: success=verde, warning=amarillo -->
            <!-- alert-dismissible + fade + show permiten cerrar la alerta con el botón X -->
            <div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
                <!-- htmlspecialchars convierte caracteres especiales para prevenir XSS -->
                <strong><?= htmlspecialchars($mensaje) ?></strong>
                <!-- Botón X para cerrar la alerta; data-bs-dismiss="alert" lo activa Bootstrap JS -->
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Botón que lleva al formulario de agregar nuevo producto -->
        <a href="agregar_producto.php" class="btn btn-primary mb-3">Agregar Producto</a>

        <!-- Tabla con bordes, filas alternadas y efecto hover al pasar el mouse -->
        <table class="table table-bordered table-striped table-hover align-middle">
            <!-- Encabezado con fondo oscuro -->
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Productos</th>
                    <!-- style="width:180px" fija el ancho de la columna de acciones -->
                    <th class="text-center" style="width:180px">Acciones</th>
                </tr>
            </thead>
            <tbody>

                <!-- Verifica que la consulta devolvió resultados y que hay al menos una fila -->
                <?php if ($resultado && $resultado->num_rows > 0) : ?>

                    <!-- Loop: itera una vez por cada producto devuelto por la consulta -->
                    <?php while ($producto = $resultado->fetch_assoc()) : ?>
                        <tr>
                            <!-- Muestra el id del producto -->
                            <td><?= $producto['id_prod'] ?></td>
                            <!-- htmlspecialchars evita que un nombre con < > rompa el HTML -->
                            <td><?= htmlspecialchars($producto['nombre_prod']) ?></td>
                            <td class="text-center">
                                <!-- Botón Editar: pasa el id del producto por la URL -->
                                <a href="editar_usuario.php?id_prod=<?= $producto['id_prod'] ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                                <!-- Botón Eliminar: pasa la acción y el id por la URL -->
                                <!-- onclick pide confirmación en el navegador antes de eliminar -->
                                <a href="productos.php?accion=eliminar&id_prod=<?= $producto['id_prod'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Eliminar al producto <?= htmlspecialchars($producto['nombre_prod'], ENT_QUOTES) ?>?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <!-- Si la consulta no devolvió ninguna fila, muestra mensaje vacío -->
                <?php else : ?>
                    <tr>
                        <!-- colspan="4" hace que la celda abarque las 4 columnas de la tabla -->
                        <td colspan="4" class="text-center text-muted">No hay productos registrados.</td>
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
