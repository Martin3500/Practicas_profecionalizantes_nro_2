<?php
// session_start() activa el sistema de sesiones para poder manipular $_SESSION
session_start();

// Vacía el arreglo $_SESSION borrando todas las variables guardadas (usuario, permisos, etc.)
$_SESSION = [];

// Destruye completamente la sesión del servidor (elimina el archivo de sesión en el disco)
session_destroy();

// Redirige al login para que el usuario vuelva a ingresar sus credenciales
header('location:login.php');

// Detiene la ejecución del script para que no corra ningún código después del redirect
exit;
