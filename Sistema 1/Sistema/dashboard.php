<?php
// Verifica que el usuario esté logueado; si no hay sesión activa, redirige al login
include 'includes/auth.php';

// Carga las constantes de base de datos (no se usan en esta página pero es buena práctica incluirlas)
include 'config.php';
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
    <title>Mi Sistema</title>
    <style>
        .sidebar{
            /* El sidebar ocupa toda la altura visible de la pantalla */
            height: 100vh;
            /* Fondo negro */
            background: #111;
            color: white;
        }
    </style>
</head>
<body>

<!-- d-flex: pone el sidebar y el contenido uno al lado del otro (layout en fila) -->
<div class="d-flex">

    <!-- Barra lateral de navegación; p-3 = padding interno -->
    <div class="sidebar p-3">
        <!-- Título del menú lateral -->
        <h5>Panel</h5>
        <!-- href="#" porque esta ES la página del Dashboard (no redirige a ningún lado) -->
        <a href="#" class="text-white d-block mb-2">Dashboard</a>
        <!-- Enlace a la lista de usuarios -->
        <a href="usuarios.php" class="text-white d-block mb-2">Usuarios</a>
        <!-- Enlace a la lista de proveedores -->
        <a href="proveedores.php" class="text-white d-block mb-2">Proveedores</a>
        <!-- Enlace a la lista de productos -->
        <a href="productos.php" class="text-white d-block mb-2">Productos</a>
        <!-- Enlace para cerrar sesión -->
        <a href="logout.php" class="text-white d-block mb-2">Logout</a>
    </div>

    <!-- Área de contenido principal; w-100 = ocupa todo el ancho restante -->
    <div class="p4 w-100">
        <!-- Mensaje de bienvenida -->
        <h2>Bienvenido a mi sistema</h2>
        <!-- Descripción breve del proyecto -->
        <p>este es mi primer programa en php</p>
    </div>

</div>
</body>
</html>
