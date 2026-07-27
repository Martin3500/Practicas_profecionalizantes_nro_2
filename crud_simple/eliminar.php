<?php
require "conexion.php";

// Leemos el id desde la URL (ej: eliminar.php?id=5)
$id = (int) $_GET['id']; // (int) protege contra inyección SQL

// Solo borramos si el id es válido (mayor que 0)
if ($id > 0) {
    // Prepared statement para el DELETE — siempre usar ? en vez de meter el id directo
    $sentencia = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $sentencia->bind_param("i", $id); // "i" = integer
    $sentencia->execute();            // Ejecuta el DELETE
}

// Tras borrar (o si el id era inválido), redirigimos al listado
header("Location: index.php");
exit; // Siempre detener el script después de un redirect
?>
