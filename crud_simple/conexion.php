<?php
// ── CONFIGURACIÓN ──────────────────────────────────────────────
$host     = "localhost";   // Servidor donde corre MySQL (en XAMPP siempre es localhost)
$usuario  = "root";        // Usuario de MySQL (por defecto en XAMPP es "root")
$password = "";            // Contraseña de MySQL (en XAMPP viene vacía por defecto)
$nombre_bd = "crud_simple"; // Nombre que tendrá nuestra base de datos

// ── PASO 1: Conectar SIN especificar base de datos ─────────────
// Primero conectamos solo al servidor para poder CREAR la BD si no existe
$conn = new mysqli($host, $usuario, $password);

// Verificamos si la conexión falló
if ($conn->connect_error) {
    // Si falla, detenemos todo y mostramos el error
    die("Error de conexión: " . $conn->connect_error);
}

// ── PASO 2: Crear la base de datos si no existe ─────────────────
// "IF NOT EXISTS" hace que MySQL no tire error si ya existe
$sql_crear_bd = "CREATE DATABASE IF NOT EXISTS `$nombre_bd`
                 CHARACTER SET utf8mb4
                 COLLATE utf8mb4_unicode_ci";
// utf8mb4 soporta todos los caracteres, incluyendo emojis y tildes

$conn->query($sql_crear_bd); // Ejecutamos el SQL para crear la BD

// ── PASO 3: Seleccionar (usar) la base de datos ─────────────────
$conn->select_db($nombre_bd); // A partir de aquí todas las consultas van a esta BD

// ── PASO 4: Crear la tabla "usuarios" si no existe ──────────────
$sql_crear_tabla = "
    CREATE TABLE IF NOT EXISTS `usuarios` (
        `id`     INT AUTO_INCREMENT PRIMARY KEY,  -- Número único que se asigna solo
        `nombre` VARCHAR(100) NOT NULL,           -- Texto de hasta 100 caracteres, obligatorio
        `email`  VARCHAR(150) NOT NULL,           -- Correo electrónico, obligatorio
        `edad`   INT NOT NULL                     -- Número entero, obligatorio
    )
";

$conn->query($sql_crear_tabla); // Ejecutamos el SQL para crear la tabla

// $conn queda disponible para cualquier archivo que incluya este script
?>
