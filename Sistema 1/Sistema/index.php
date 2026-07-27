<?php
// Carga config.php para tener disponibles las constantes DB_HOST, DB_USER, DB_PASS, DB_NAME
include 'config.php';

// Conecta a MySQL SIN indicar base de datos todavía (necesario para poder crearla si no existe)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Crea la base de datos si no existe; las tildes `` protegen el nombre por si tiene caracteres especiales
$conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");

// Ahora selecciona la base de datos recién creada (o la existente)
$conn->select_db(DB_NAME);

// Crea la tabla 'usuarios' si todavía no existe
// id: entero autoincrementable, clave primaria
// nombre: texto hasta 100 caracteres
// email: texto único (no permite repetidos)
// password: texto largo para guardar el hash bcrypt
$conn->query("CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255) NOT NULL
)");

// Crea la tabla 'productos' si todavía no existe
// id_prod: clave primaria autogenerada
// cod_prod: código numérico del producto
// nombre_prod: nombre descriptivo del producto
$conn->query("CREATE TABLE IF NOT EXISTS productos (
    id_prod INT AUTO_INCREMENT PRIMARY KEY,
    cod_prod INTEGER(20),
    nombre_prod VARCHAR(100))");

// Crea la tabla 'proveedores' si todavía no existe
// id_prov: clave primaria autogenerada
// nombre_prov, direccion_prov, telefono_prov, email_prov: datos de contacto
$conn->query("CREATE TABLE IF NOT EXISTS proveedores (
    id_prov INT AUTO_INCREMENT PRIMARY KEY,
    nombre_prov VARCHAR(100),
    direccion_prov VARCHAR(100),
    telefono_prov VARCHAR(100),
    email_prov VARCHAR(100))");

// Cierra la conexión a MySQL para liberar memoria del servidor
$conn->close();

// Una vez creadas las tablas, redirige al login para que el usuario empiece a trabajar
header('location:login.php');

// Detiene el script para que no ejecute nada más después del redirect
exit();
?>
