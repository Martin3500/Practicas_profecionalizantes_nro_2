<?php
// session_start() activa el sistema de sesiones de PHP para poder leer $_SESSION
session_start();

// isset() verifica si la variable $_SESSION['user'] existe
// El ! (negación) invierte la condición: "si el usuario NO está logueado..."
if (!isset($_SESSION['user'])){

    // header() envía al navegador una orden de redireccionamiento a login.php
    header('location:login.php');

    // exit detiene la ejecución del resto del archivo para que no cargue la página protegida
    exit;
}
?>
