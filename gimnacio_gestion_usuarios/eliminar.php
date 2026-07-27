<?php
require "conexion.php";

$id = (int) $_GET['id']; 

if ($id > 0) {
    // 1. Buscamos los datos del cliente antes de eliminarlo para la posteridad
    $buscar = $conn->prepare("SELECT DNI, nombre FROM clientes WHERE id = ?");
    $buscar->bind_param("i", $id);
    $buscar->execute();
    $cliente = $buscar->get_result()->fetch_assoc();

    if ($cliente) {
        $dni_antiguo = $cliente['DNI'];
        $nombre_antiguo = $cliente['nombre'];

        // 2. Guardamos la acción en el historial. El DNI pasará a ser NULL automáticamente en la BD,
        // pero la descripción mantendrá quién era el dueño.
        $accion_texto = "Se eliminó al cliente: $nombre_antiguo con DNI: $dni_antiguo";
        $log = $conn->prepare("INSERT INTO historial (DNI, accion) VALUES (?, ?)");
        $log->bind_param("is", $dni_antiguo, $accion_texto);
        $log->execute();

        // 3. Ahora sí, borramos el cliente sin romper restricciones
        $sentencia = $conn->prepare("DELETE FROM clientes WHERE id = ?");
        $sentencia->bind_param("i", $id); 
        $sentencia->execute();  
        
        $_SESSION['flash'] = [
            'texto' => 'Cliente eliminado correctamente.',
            'tipo'  => 'danger'
        ];          
    }
}

header("Location: index.php");
exit; 
?>