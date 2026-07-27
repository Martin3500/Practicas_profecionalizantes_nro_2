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

    // Toma el código de producto que escribió el usuario en el formulario
    $cod_prod = $_POST['cod_prod'];

    // Toma el nombre del producto que escribió el usuario en el formulario
    $nombre   = $_POST['nombre'];

    // Prepara una consulta segura para verificar si el código de producto ya existe
    $stmt = $conn->prepare("SELECT id_prod FROM productos WHERE cod_prod = ?");

    // Vincula cod_prod al marcador ? ("s" = string)
    $stmt->bind_param("s", $cod_prod);

    // Ejecuta la consulta de búsqueda
    $stmt->execute();

    // store_result() guarda internamente el resultado para poder usar num_rows
    $stmt->store_result();

    // num_rows contiene la cantidad de filas encontradas; si es > 0 el código ya existe
    if ($stmt->num_rows > 0) {

        // El código de producto ya está registrado: guarda el mensaje de error
        $error = 'Ese producto ya está registrado. Usá otro.';

        // Libera los recursos del statement
        $stmt->close();

    } else {

        // El código no existe: libera el statement de búsqueda antes de crear uno nuevo
        $stmt->close();

        // Prepara el INSERT con dos marcadores de posición (cod_prod, nombre)
        $stmt = $conn->prepare("INSERT INTO productos (cod_prod, nombre_prod) VALUES (?, ?)");

        // Vincula los dos valores; "ss" significa que los dos son strings
        $stmt->bind_param("ss", $cod_prod, $nombre);

        // Ejecuta el INSERT para guardar el nuevo producto en la base de datos
        $stmt->execute();

        // Libera los recursos del statement
        $stmt->close();

        // Redirige a la lista de productos con el mensaje de confirmación en la URL
        header('location: productos.php?msg=agregado');

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
    <title>Agregar Producto</title>
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
        <h2 class="text-center mb-4">Agregar Producto</h2>

        <!-- Alerta de error: solo se muestra si $error tiene contenido -->
        <?php if ($error != '') : ?>
            <!-- alert-danger = fondo rojo de Bootstrap -->
            <!-- htmlspecialchars convierte caracteres especiales para evitar XSS -->
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Formulario; method="post" envía datos en el cuerpo; action="" = esta misma página -->
        <form method="post" action="" class="col-md-6">

            <!-- Campo del código de producto -->
            <div class="mb-3">
                <label class="form-label">Id del Producto</label>
                <!-- name="cod_prod" debe coincidir con $_POST['cod_prod'] en el PHP de arriba -->
                <input type="text" name="cod_prod" class="form-control" required>
            </div>

            <!-- Campo del nombre del producto -->
            <div class="mb-3">
                <label class="form-label">Nombre del producto</label>
                <!-- name="nombre" debe coincidir con $_POST['nombre'] en el PHP de arriba -->
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <!-- name="guardar" es lo que detecta isset($_POST['guardar']) en el PHP de arriba -->
            <button type="submit" name="guardar" class="btn btn-primary">Guardar</button>
            <!-- Botón Cancelar: vuelve a la lista sin guardar nada -->
            <a href="productos.php" class="btn btn-secondary ms-2">Cancelar</a>

        </form>
    </div>
</div>

<!-- Carga el JS de Bootstrap (necesario para componentes interactivos) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-YUe2LzesAfftltw+PEaao2tjU/QATaW/rOitAq67e0CT0Zi2VVRL0oC4+gAaeBKu" crossorigin="anonymous"></script>
</body>
</html>
