<?php
    // define() crea una constante global que no puede cambiar durante la ejecución del script
    // 'DB_HOST' es el nombre de la constante; 'localhost' significa que MySQL está en este mismo equipo
    define('DB_HOST', 'localhost');

    // 'root' es el usuario administrador que crea XAMPP por defecto
    define('DB_USER', 'root');

    // Contraseña del usuario root; en XAMPP local generalmente está vacía
    define('DB_PASS', '');

    // Nombre de la base de datos a la que se conectará el sistema
    define('DB_NAME', 'Beltran_TM');
?>
