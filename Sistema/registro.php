<?php
include('config.php');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($_POST) {

    $nombre = $_POST['nombre'];
    $email  = $_POST['email'];
    $pass   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user) {
        $error = "El email ya está registrado.";
        echo "<p>$error</p>";
       
       
    }else{
   
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $email, $pass);

    if ($stmt->execute()) {
        echo "Usuario registrado con éxito.";
        header('location:login.php');
        exit();
    } else {
       // $error = ($conn->errno === 1062)
         //   ? "El email ya está registrado."
         //   : "Error al registrar usuario. Intente nuevamente.";
    }

    $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-dark text-white">

<div class="container mt-5">

    <h2>Registro de usuarios</h2>

    <?php if(isset($error)) echo "<p>$error</p>"; ?>

    <form method="post">
        
        <input class="form-control mb-2" type="text" name="nombre" placeholder="ingrese su nombre" required>
        <input class="form-control mb-2" type="email" name="email" placeholder="ingrese su Email" required>
        <input class="form-control mb-2" type="password" name="password" placeholder="ingrese su Password" required>
        <button class="btn btn-primary">Grabar</button>
    </form>
    
</div>


</body>
</html>