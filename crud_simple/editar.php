<?php
require "conexion.php";

// ── OBTENER el id que viene en la URL ───────────────────────────
// Cuando el usuario hace clic en "Editar" la URL es: editar.php?id=3
// $_GET['id'] lee ese número desde la URL
// (int) lo convierte a entero para evitar que alguien pase texto malicioso
$id = (int) $_GET['id'];

// Si el id es 0 o negativo (valor inválido), mandamos al index
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// ── CARGAR los datos actuales del usuario para llenar el formulario ──
$sentencia = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$sentencia->bind_param("i", $id); // "i" = integer
$sentencia->execute();

// get_result() convierte el resultado en un objeto que podemos leer
$resultado = $sentencia->get_result();

// fetch_assoc() devuelve la fila como arreglo o NULL si no existe
$usuario = $resultado->fetch_assoc();

// Si no encontramos ningún usuario con ese id, volvemos al index
if (!$usuario) {
    header("Location: index.php");
    exit;
}

// ── PROCESAR el formulario cuando el usuario guarda los cambios ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $email  = trim($_POST['email']);
    $edad   = (int) $_POST['edad'];

    // UPDATE modifica los datos del registro que tenga ese id
    $actualizar = $conn->prepare(
        "UPDATE usuarios SET nombre = ?, email = ?, edad = ? WHERE id = ?"
    );
    // "ssii" = string, string, integer, integer
    $actualizar->bind_param("ssii", $nombre, $email, $edad, $id);
    $actualizar->execute();

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">

    <h2>Editar usuario #<?= $id ?></h2>

    <form action="" method="post" class="mt-3" style="max-width:400px">

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <!-- value="..." rellena el campo con el dato que ya tiene guardado en la BD
                 htmlspecialchars evita que comillas o < > rompan el HTML -->
            <input type="text" name="nombre" class="form-control" required
                   value="<?= htmlspecialchars($usuario['nombre']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required
                   value="<?= htmlspecialchars($usuario['email']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Edad</label>
            <input type="number" name="edad" class="form-control" min="1" max="120" required
                   value="<?= htmlspecialchars($usuario['edad']) ?>">
        </div>

        <button type="submit" class="btn btn-warning">Actualizar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>

    </form>

</body>
</html>
