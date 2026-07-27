<?php
session_start();
include('config.php');


$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$error ='';
$exito ='';


if ($_POST) {
    $email = $_POST['email'];
    $pass  = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user'] = $user;
        header('location:dashboard.php');
        exit();
    } else {
        $error = "Los datos son incorrectos";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        body{
            min-height: 100vh;
            background: linear-gradient(135deg,#020024 0%, #090979 75%, #00D4FF 100%);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card{
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.4);
        }
        .card-header{
            background: #090979;
            border-radius: 1rem 1rem 0 0 !important;
            text-align: center;
            padding: 1rem
        }
        .card-body{
            padding: 2rem;
        }
        .btn-primary{
            background: #020024;
            border-color: #020024;
        }
        .btn-primary:hover{
            background: #090979;
            border-color: #090979;
        }
        .form-control:focus{
            border-color: #090979;
            box-shadow: 0 0 0 .2rem rgba(15, 52, 96, 0.25); 
        }
        .icono{
            width: 64px;
            height: 64px
            baclground: #090979;
            border-radius: 50%; 
            display: flex;
            justify-content: center;
            align-items: center;    
            font-size: 1.8rem;
        }
       
    </style>
</head>
<body>


<div style="whidth: 100%; max-width: 420px padding: 1rem;">
    <div class="card">
        <div class="card-header text-white">
        <div class="icono">&#128274;</div>
                <h4 class="mb-0 fw-bold"> Mi Sistema</h4>
                <p class="mb-0 text-white-50 small">Ingresar sus credenciales para continuar</p>
            </div>

            <div class="card-body">

                 <?php if($exito):?>
                    <div class="alert alert-success py-2">
                        <small><?=htmlspecialchars($exito)?></small>
                    </div> 
                 <?php endif; ?>

              <?php if($error):?>
                    <div class="alert alert-danger py-2">
                        <small><?=htmlspecialchars($error)?></small>
                    </div>
              <?php endif; ?>                    

    <form method="post" >
        <div class="mb-3">
           <label class="form-label fw-semibold">Email</label>     
           <input type="email" class="form-control"  name="email" placeholder="ingrese su Email" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Contraseña</label>
            <input type="password" class="form-control"  name="password" placeholder="ingrese su Contraseña" required>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Ingresar</button>
    </form>
  </div>
    
    <div class="card-footer bg-light text-center py-3 rounded-bottom">
       <small class="text-muted">No estas registrado?
        <a href="registro.php" class="fw-semibold text-decoration-none">Registrarse </a>
        </small>         
    </div>
</div>   
</div>
    
</body>

</html>


