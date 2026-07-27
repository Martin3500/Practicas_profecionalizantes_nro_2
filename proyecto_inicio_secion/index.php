<?php

session_start();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Inicio</title>
</head>
<body>
    <div> 
        <h4>INICIAR SESSION</h4>
    </div>
    <div>
        <form action="validar.php" method="POST">
            <div>   
                <label for="usuario"> Usuario </label>
                <input type="text" name="usuario" id="usuario">
                <button type="sumit"> precioname</button>
            </div>
        </form>
</body>
</html>