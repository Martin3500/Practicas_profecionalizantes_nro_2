<?php
session_start();
//destruir todas las variables de sessión
$_SESSION =[];
//destruir la session
session_destroy();

//redirigir al login
header('location:login.php');
exit;

?>  