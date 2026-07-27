<?php
// Incluimos conexion.php: crea la BD y tabla si no existen, deja $conn listo
require "conexion.php";

// ── BUSCADOR ─────────────────────────────────────────────────────────────────

// isset() comprueba si existe la variable $_GET['buscar']
// $_GET['buscar'] se llena cuando el usuario escribe en el campo y envía el formulario
// La URL quedaría así: index.php?buscar=ana
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
// trim() elimina espacios al principio y al final del texto buscado
// Si no vino nada en la URL, $buscar queda como cadena vacía ""

// ── CONSTRUIR LA CONSULTA según haya búsqueda o no ───────────────────────────

if ($buscar !== '') {
    // El usuario escribió algo → filtramos con LIKE
    // LIKE '%texto%' busca ese texto en cualquier posición del campo
    // Buscamos en nombre, email Y edad al mismo tiempo con OR

    // Usamos prepared statement para evitar inyección SQL:
    // los ? son marcadores que se reemplazarán de forma segura por los valores reales
    $sentencia = $conn->prepare("
        SELECT * FROM usuarios
        WHERE nombre LIKE ?        -- filtra si el nombre contiene el texto
           OR email  LIKE ?        -- O si el email contiene el texto
           OR edad   LIKE ?        -- O si la edad contiene el texto
        ORDER BY id DESC
    ");

    // Preparamos el valor con % a cada lado para el LIKE
    // Si $buscar = "ana" → $like = "%ana%"
    $like = "%" . $buscar . "%";

    // bind_param vincula los valores a los ?
    // "sss" = tres parámetros de tipo string (s)
    // Los tres ? reciben el mismo valor $like
    $sentencia->bind_param("sss", $like, $like, $like);

    $sentencia->execute(); // Ejecuta el SELECT con los filtros

    // get_result() convierte la respuesta en un objeto que podemos recorrer
    $resultado = $sentencia->get_result();

} else {
    // No hay búsqueda → traemos todos los registros normalmente
    $resultado = $conn->query("SELECT * FROM usuarios ORDER BY id DESC");
}

// Guardamos el total de filas que devolvió la consulta
// Lo usaremos para mostrar "X resultado(s) encontrado(s)"
$total = $resultado->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Simple</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">

    <!-- d-flex justify-content-between align-items-center = fila flexible:
         el título queda a la izquierda y el formulario a la derecha -->
    <div class="d-flex justify-content-between align-items-center mb-3">

        <h1 class="mb-0">Usuarios</h1>

        <!-- ── FORMULARIO DE BÚSQUEDA ──────────────────────────────────────── -->
        <!-- method="get" pone el texto en la URL: index.php?buscar=lo_que_escribio
             Esto permite compartir el enlace con el filtro activo y usar el botón atrás -->
        <!-- ms-auto empuja el formulario hacia la derecha si el h1 no ocupa todo el ancho -->
        <form method="get" action="index.php" class="d-flex gap-2 align-items-center ms-auto">

            <!-- name="buscar" define la clave que PHP leerá en $_GET['buscar'] -->
            <!-- value="..." rellena el campo con lo que se buscó antes,
                 para que no se borre al recargar la página -->
            <!-- htmlspecialchars evita que comillas o < > rompan el atributo HTML -->
            <input
                type="text"
                name="buscar"
                class="form-control"
                placeholder="Buscar por nombre, email o edad..."
                value="<?= htmlspecialchars($buscar) ?>"
                style="width:280px"
            >
            <!-- style="width:280px" fija el ancho del campo para que no se estire demasiado -->

            <!-- Botón que envía el formulario y dispara la búsqueda -->
            <button type="submit" class="btn btn-primary">Buscar</button>

            <!-- Solo mostramos "Limpiar" cuando hay una búsqueda activa -->
            <?php if ($buscar !== ''): ?>
                <!-- href="index.php" sin ?buscar= borra el filtro y muestra todos -->
                <a href="index.php" class="btn btn-outline-secondary">Limpiar</a>
            <?php endif; ?>

        </form>

    </div>

    <!-- ── CONTADOR DE RESULTADOS ─────────────────────────────────────────── -->
    <?php if ($buscar !== ''): ?>
        <!-- text-end alinea el texto a la derecha, igual que el buscador -->
        <!-- mt-1 agrega un pequeño margen superior para separarlo del formulario -->
        <p class="text-muted text-end mt-1 mb-2">
            <!-- $total tiene el número de filas que devolvió la consulta filtrada -->
            <?= $total ?> resultado<?= $total !== 1 ? 's' : '' ?> para
            <!-- Mostramos el término buscado entre comillas, escapado para seguridad -->
            "<strong><?= htmlspecialchars($buscar) ?></strong>"
        </p>
    <?php endif; ?>

    <!-- Botón para agregar un usuario nuevo -->
    <a href="crear.php" class="btn btn-success mb-3">+ Agregar usuario</a>

    <!-- ── TABLA DE RESULTADOS ────────────────────────────────────────────── -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Edad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // $total > 0 comprueba si la consulta devolvió al menos un registro
        if ($total > 0):
            // fetch_assoc() devuelve cada fila como arreglo ["id"=>1, "nombre"=>"Ana", ...]
            // El while avanza fila por fila hasta que no queden más
            while ($fila = $resultado->fetch_assoc()):
        ?>
            <tr>
                <!-- htmlspecialchars() protege contra XSS al mostrar datos de la BD -->
                <td><?= htmlspecialchars($fila['id']) ?></td>
                <td><?= htmlspecialchars($fila['nombre']) ?></td>
                <td><?= htmlspecialchars($fila['email']) ?></td>
                <td><?= htmlspecialchars($fila['edad']) ?></td>
                <td>
                    <a href="editar.php?id=<?= $fila['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="eliminar.php?id=<?= $fila['id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Seguro que quieres eliminar este usuario?')">
                       Eliminar
                    </a>
                </td>
            </tr>
        <?php
            endwhile;
        else:
            // Si no hay resultados distinguimos dos casos para el mensaje
        ?>
            <tr>
                <td colspan="5" class="text-center text-muted">
                    <?php if ($buscar !== ''): ?>
                        <!-- Búsqueda activa pero sin coincidencias -->
                        No se encontraron usuarios que coincidan con
                        "<strong><?= htmlspecialchars($buscar) ?></strong>".
                    <?php else: ?>
                        <!-- Tabla completamente vacía -->
                        No hay usuarios registrados.
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
