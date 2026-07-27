<?php
// Validamos que se haya enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitización básica (simulada)
    $nombre = htmlspecialchars($_POST['nombre']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password']; // En el backend real, esto se debe hashear con password_hash()

    echo "<h1>¡Datos recibidos con éxito!</h1>";
    echo "<p>Pronto procesaremos el registro de: <strong>$nombre</strong> ($email)</p>";
    echo "<br><a href='index.php'>Volver al formulario</a>";

    /* 
       Aquí iría tu lógica de conexión a la base de datos:
       1. Conectar con PDO o mysqli
       2. Validar si el email ya existe
       3. Encriptar contraseña
       4. INSERT INTO usuarios...
    */
    
} else {
    // Si intentan entrar directo al archivo sin usar el formulario, los mandamos de vuelta
    header("Location: index.php");
    exit();
}
?>