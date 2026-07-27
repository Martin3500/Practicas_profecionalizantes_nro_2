<?php
require "conexion.php"; // Cargamos la conexión a la BD

// ── PROCESAR EL FORMULARIO cuando el usuario hace clic en "Guardar" ──
// $_SERVER['REQUEST_METHOD'] dice cómo llegó la petición: GET (abrir página) o POST (enviar formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Leemos los datos que vienen del formulario y les quitamos espacios sobrantes con trim()
    $nombre = trim($_POST['nombre']); // $_POST['nombre'] = lo que escribió en el campo "nombre"
    $email  = trim($_POST['email']);
    $edad   = (int) $_POST['edad'];  // (int) convierte el valor a número entero para mayor seguridad

    // ── Sentencia preparada (prepared statement) ─────────────────
    // En lugar de poner los datos directo en el SQL (peligroso),
    // usamos ? como marcadores de posición. Así evitamos inyección SQL.
    $sentencia = $conn->prepare(
        "INSERT INTO usuarios (nombre, email, edad) VALUES (?, ?, ?)"
    );
    // Vinculamos los valores reales a los ?
    // "ssi" = string, string, integer → el tipo de cada parámetro en orden
    $sentencia->bind_param("ssi", $nombre, $email, $edad);

    $sentencia->execute(); // Ejecutamos el INSERT

    // Redirigimos al index para ver el usuario recién creado
    // header("Location:...") envía al navegador a otra página
    header("Location: index.php");
    exit; // Detenemos el script después del redirect para evitar que siga ejecutándose
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear usuario</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">

    <h2>Agregar usuario nuevo</h2>

    <!-- action="" significa que el formulario se envía al mismo archivo (crear.php)
         method="post" envía los datos de forma oculta (no aparecen en la URL) -->
    <form action="" method="post" class="mt-3" style="max-width:400px">

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <!-- name="nombre" es la clave con la que PHP lo leerá en $_POST['nombre'] -->
            <input type="text" name="nombre" class="form-control" required>
            <!-- required = el navegador no deja enviar si está vacío -->
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
            <!-- type="email" hace que el navegador valide el formato del correo -->
        </div>

        <div class="mb-3">
            <label class="form-label">Edad</label>
            <input type="number" name="edad" class="form-control" min="1" max="120" required>
        </div>

        <!-- Botón de enviar el formulario -->
        <button type="submit" class="btn btn-success">Guardar</button>

        <!-- Enlace para cancelar y volver sin guardar nada -->
        <a href="index.php" class="btn btn-secondary">Cancelar</a>

    </form>

</body>
</html>
