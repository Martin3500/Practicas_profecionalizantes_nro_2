

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Mi Sistema</title>
    <style>
        .sidebar{
            height: 100vh;
            background: #111;
            color: white;
        }
    </style>
</head>
<body>

<!--Barra lateral-->
<div class="d-flex">
    <div class="sidebar p-3">
        <h5>Panel</h5>
        <a href="dashboard.php" class="text-white d-block mb-2">Dashboard</a>
        <a href="usuarios.php" class="text-white d-block mb-2">Usuarios</a>
        <a href="logout.php" class="text-white d-block mb-2">Logout</a>
    </div>

    <!--Contenido --->
<div class="p4 w-100">
    <h2>Bienvenido a mi sistema</h2>
    <p>este es mi primer programa en php</p>
</div>

</div>
</body>
</html>