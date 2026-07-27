<?php
require "conexion.php";

$id = (int) $_GET['id'];

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$sentencia = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$sentencia->bind_param("i", $id);
$sentencia->execute();
$resultado = $sentencia->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email  = trim($_POST['email']);
    $edad   = (int) $_POST['edad'];
    $DNI  = (int) $_POST['DNI'];

    
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
    
    $sentencia = $conn->prepare("UPDATE clientes SET nombre=?, email=?, edad=?, DNI=? WHERE id = ?");
    $sentencia->bind_param("ssiii", $nombre, $email, $edad, $DNI, $id);
    
    $sentencia->execute();
    
    // Si llega aquí, todo salió bien
    // REGISTRO EN HISTORIAL
    $log = $conn->prepare("INSERT INTO historial (DNI, accion) VALUES (?, 'Registró un cliente nuevo')");
    $log->bind_param("i", $DNI);
    $log->execute();
    
    $_SESSION['flash'] = [
        'texto' => '¡Se edito la informacion del cliente con éxito!',
        'tipo'  => 'success'
    ];

    } catch (mysqli_sql_exception $e) {
    // ¡Aquí evitamos que todo se rompa! El error se atrapa antes de que tire la web.
    
    // Guardamos el error real en el log del servidor (invisible para el usuario)
    error_log("Error en BD: " . $e->getMessage());

    // Personalizamos el mensaje según el problema
    if ($e->getCode() == 1062) {
        $_SESSION['flash'] = [
        'texto' => 'Error: El DNI o el correo electrónico ya están registrados.',
        'tipo'  => 'danger'
    ];
    } else {
        // Mensaje genérico para el usuario (por seguridad, nunca les muestres el error interno de la BD)
        $_SESSION['flash'] = [
        'texto' => "La base de datos rechazó los datos: " . $e->getMessage(),
        'tipo'  => 'danger'
    ];
    }
    } finally {
    // Esto se ejecuta SIEMPRE, haya habido error o no, para no dejar procesos colgados
    if (isset($sentencia)) {
        $sentencia->close();
    }
    }

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario - GymFlow</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Editar cliente #<?= $id ?></h2>

    <form action="" method="post" class="form-container">
        <div class="form-group">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required value="<?= htmlspecialchars($usuario['nombre']) ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($usuario['email']) ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Edad</label>
            <input type="number" name="edad" id="edad" class="form-control" min="10" max="120" required value="<?= htmlspecialchars($usuario['edad']) ?>">
        </div>
        <div class="form-group">
            <label class="form-label">DNI</label>
            <input type="number" name="DNI" id="DNI" min="1000000" max="999999999" class="form-control"  required value="<?= htmlspecialchars($usuario['DNI']) ?>">
        </div> 
        

        <div class="form-group">
            <button type="submit" class="btn btn-warning">Actualizar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>