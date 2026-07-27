<?php
require "conexion.php";

$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if ($buscar !== '') {
    $sentencia = $conn->prepare("
        SELECT * FROM clientes
        WHERE DNI LIKE ?        
           OR nombre  LIKE ?        
           OR edad   LIKE ?        
        ORDER BY id DESC
    ");
    $like = "%" . $buscar . "%";
    // CORRECCIÓN: Eran 3 parámetros (sss) para 3 signos de interrogación
    $sentencia->bind_param("sss", $like, $like, $like);
    $sentencia->execute(); 
    $resultado = $sentencia->get_result();
} else {
    $resultado = $conn->query("SELECT * FROM clientes ORDER BY id DESC");
}

$total = $resultado->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>GymFlow - Gestión</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h1>GymFlow - Gestión de Clientes</h1>

        <form method="get" action="index.php" class="search-form">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar..." value="<?= htmlspecialchars($buscar) ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <?php if ($buscar !== ''): ?>
                <a href="index.php" class="btn btn-secondary">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

   
    <?php if ($buscar !== ''): ?>
        <p class="search-results">
            <?= $total ?> resultado<?= $total !== 1 ? 's' : '' ?> para "<strong><?= htmlspecialchars($buscar) ?></strong>"
        </p>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['tipo'] ?>">
            <?= htmlspecialchars($_SESSION['flash']['texto']) ?>
        </div>
        <?php unset($_SESSION['flash']); // Lo borramos de la sesión inmediatamente ?>
    <?php endif; ?>

    
    <div class="action-bar" style="display: flex; gap: 10px;">
        <a href="crear.php" class="btn btn-primary">+ Agregar usuario</a>
        <a href="historial.php" class="btn btn-secondary"> Ver Historial General</a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>id</th>
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Edad</th>
                    <th>Clases Disponibles</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($total > 0): while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['id']) ?></td>
                    <td><?= htmlspecialchars($fila['DNI']) ?></td>
                    <td><?= htmlspecialchars($fila['nombre']) ?></td>
                    <td><?= htmlspecialchars($fila['email']) ?></td>
                    <td><?= htmlspecialchars($fila['edad']) ?></td>
                    <td><?= htmlspecialchars($fila['clasesdisponibles']) ?></td>
                    <td>
                        <a href="editar.php?id=<?= $fila['id'] ?>" class="btn btn-warning btn-sm">Editar datos</a>
                        <a href="cargar_pago.php?id=<?= $fila['id'] ?>" class="btn btn-primary btn-sm">Cargar pago</a>
                        <a href="descontar_clase.php?id=<?= $fila['id'] ?>" class="btn btn-danger btn-sm">Descontar clase</a>
                        <a href="eliminar.php?id=<?= $fila['id'] ?>" class="btn btn-secondary btn-sm" onclick="return confirm('¿Seguro que quieres eliminar este usuario?')">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        <?php if ($buscar !== ''): ?>
                            No se encontraron clientes que coincidan con "<strong><?= htmlspecialchars($buscar) ?></strong>".
                        <?php else: ?>
                            No hay clientes registrados.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>