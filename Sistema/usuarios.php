<?php
// Incluye el control de autenticación (redirige si no hay sesión activa)
include 'includes/auth.php';
// Incluye la configuración de base de datos (constantes DB_HOST, DB_USER, etc.)
include 'config.php';

// Abre la conexión a MySQL usando las constantes definidas en config.php
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Variables para mostrar alertas en la vista
$mensaje = '';
$tipo = '';

// Mapa de claves de URL a textos de alerta
$mensajes = [
    'actualizado' => 'Usuario actualizado correctamente.',
    'agregado'    => 'Usuario agregado correctamente.',
    'eliminado'   => 'Usuario eliminado correctamente.',
];

// Si la URL trae ?msg=..., carga el texto y el tipo de alerta correspondiente
if (isset($_GET['msg']) && array_key_exists($_GET['msg'], $mensajes)) {
    $mensaje = $mensajes[$_GET['msg']];
    $tipo = ($_GET['msg'] === 'eliminado') ? 'warning' : 'success';
}

// Procesa la acción de eliminar cuando llega ?accion=eliminar&id=N
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar' && isset($_GET['id'])) {
    // Castea el id a entero para evitar inyección SQL
    $id = (int) $_GET['id'];
    // Prepara la sentencia DELETE con placeholder para el id
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    // Vincula el parámetro entero al placeholder
    $stmt->bind_param("i", $id);
    // Ejecuta la eliminación en la base de datos
    $stmt->execute();
    // Libera los recursos del statement
    $stmt->close();
    // Redirige con mensaje de confirmación y detiene la ejecución
    header('location: usuarios.php?msg=eliminado');
    exit();
}

// Obtiene todos los usuarios ordenados por id ascendente
$resultado = $conn->query("SELECT id, nombre, email FROM usuarios ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Usuarios</title>
    <style>
        /* Sidebar de navegación lateral de altura completa */
        .sidebar {
            height: 100vh;
            background: #111;
            color: white;
            min-width: 180px;
        }
    </style>
</head>
<body>

<!-- Contenedor principal: sidebar + contenido -->
<div class="d-flex">
    <!-- Sidebar con enlaces de navegación -->
    <div class="sidebar p-3">
        <h5>Panel</h5>
        <a href="dashboard.php" class="text-white d-block mb-2">Dashboard</a>
        <a href="usuarios.php" class="text-white d-block mb-2">Usuarios</a>
        <a href="logout.php" class="text-white d-block mb-2">Logout</a>
    </div>

    <!-- Área de contenido principal -->
    <div class="p-4 w-100">
        <h2 class="text-center mb-4">Gestión de Usuarios</h2>

        <!-- Muestra alerta de feedback si existe un mensaje -->
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
                <strong><?= htmlspecialchars($mensaje) ?></strong>
                <!-- Botón para cerrar la alerta -->
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Enlace para ir al formulario de creación de usuario -->
        <a href="agregar_usuario.php" class="btn btn-primary mb-3">Agregar Usuario</a>

        <!-- Tabla de usuarios con estilos de Bootstrap -->
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th class="text-center" style="width:180px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <!-- Itera cada fila del resultado y la muestra en la tabla -->
                    <?php while ($usuario = $resultado->fetch_assoc()): ?>
                        <tr>
                            <!-- Muestra el id sin escapar (es un entero seguro) -->
                            <td><?= $usuario['id'] ?></td>
                            <!-- Escapa el nombre para prevenir XSS -->
                            <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                            <!-- Escapa el email para prevenir XSS -->
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td class="text-center">
                                <!-- Botón de edición: lleva al formulario con el id del usuario -->
                                <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                                <!-- Botón de eliminación: pide confirmación antes de proceder -->
                                <a href="usuarios.php?accion=eliminar&id=<?= $usuario['id'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Eliminar al usuario <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES) ?>?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <!-- Fila vacía cuando no hay usuarios registrados -->
                    <tr>
                        <td colspan="4" class="text-center text-muted">No hay usuarios registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap 5 JS (incluye Popper) para componentes interactivos como alertas -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-YUe2LzesAfftltw+PEaao2tjU/QATaW/rOitAq67e0CT0Zi2VVRL0oC4+gAaeBKu" crossorigin="anonymous"></script>
</body>
</html>
