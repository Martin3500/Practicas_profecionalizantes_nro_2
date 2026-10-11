<?php
// Configuracion de conexion a la base de datos y arranque de sesion

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'Sis_Gestion_GYM');
define('DB_USER', 'root');
define('DB_PASS', '');

// Datos del gimnasio (COMPLETAR con los datos reales)
define('GYM_NOMBRE', 'Mi Gimnasio');
define('GYM_WHATSAPP', '5490000000000'); // TODO: reemplazar por el numero real, formato: codigo pais + numero, sin '+' ni espacios

// Scripts que definen la estructura de la base (se ejecutan solo si la base todavia no existe)
const ARCHIVOS_INSTALACION = [
    __DIR__ . '/Consulata_para_creear_las_tablas_de_sistema_gestion_gym.sql',
    __DIR__ . '/04_usuarios.sql',
];

function conectarDB(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        $servidor = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $servidor->exec(
            'CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` ' .
            'DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci'
        );

        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        asegurarEstructuraDB($pdo);
    } catch (PDOException $e) {
        die('No se pudo conectar a la base de datos. Verificá que MySQL esté corriendo. Detalle: ' . htmlspecialchars($e->getMessage()));
    }

    return $pdo;
}

// Si la base ya tiene las tablas creadas, no hace nada. Si es la primera vez, crea todo el esquema.
function asegurarEstructuraDB(PDO $pdo): void
{
    $yaInstalada = $pdo->query("SHOW TABLES LIKE 'usuarios'")->fetchColumn();
    if ($yaInstalada) {
        return;
    }

    foreach (ARCHIVOS_INSTALACION as $archivo) {
        ejecutarArchivoSql($pdo, $archivo);
    }
}

function ejecutarArchivoSql(PDO $pdo, string $ruta): void
{
    if (!is_file($ruta)) {
        return;
    }
    $contenido = file_get_contents($ruta);
    foreach (explode(';', $contenido) as $sentencia) {
        $sentencia = trim($sentencia);
        if ($sentencia === '') {
            continue;
        }
        $pdo->exec($sentencia);
    }
}
