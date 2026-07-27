<?php
require "conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']); 
    $email  = trim($_POST['email']);
    $edad   = (int) $_POST['edad'];  
    $DNI = (int) $_POST['DNI'];
    
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
    
    $sentencia = $conn->prepare("INSERT INTO clientes (nombre, email, edad, DNI) VALUES (?, ?, ?, ?)");
    $sentencia->bind_param("ssii", $nombre, $email, $edad, $DNI);
    
    $sentencia->execute();
    
    // Si llega aquí, todo salió bien
    // REGISTRO EN HISTORIAL
    $log = $conn->prepare("INSERT INTO historial (DNI, accion) VALUES (?, 'Registró un cliente nuevo')");
    $log->bind_param("i", $DNI);
    $log->execute();
    
    $_SESSION['flash'] = [
        'texto' => '¡Cliente registrado con éxito!',
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
    <title>Crear usuario - GymFlow</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Agregar cliente nuevo</h2>

    <form action="" method="post" class="form-container">
        <div class="form-group">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Edad</label>
            <input type="number" name="edad" class="form-control" min="10" max="100" required>
        </div>
        <div class="form-group">
            <label class="form-label">DNI</label>
            <input type="number" name="DNI" class="form-control" min="1000000" max="999999999" required>
        </div>    
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>