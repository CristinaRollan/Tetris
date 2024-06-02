<?php 
$message = "";
$valid = 'true';
session_start();

if (isset($_GET['key']) && isset($_GET['email'])) {
    $key = cleanInput($_GET['key']);
    $email = cleanInput($_GET['email']);
    
    try {
        include("../parts/constantes.php");
        $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT * FROM recuperar_contrasenia WHERE email = :email AND temp_key = :temp_key");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':temp_key', $key, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() != 1) {
            echo "Esta URL no es válida o ya se ha utilizado. Por favor verifique e intente nuevamente.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];

    if ($password2 == $password1) {
        $message_success = "Se ha establecido una nueva contraseña para: " . $email;
        $password = md5($password1);

        try {
            $stmt = $pdo->prepare("DELETE FROM recuperar_contrasenia WHERE email = :email AND temp_key = :temp_key");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':temp_key', $key, PDO::PARAM_STR);
            $stmt->execute();
            
            $stmt = $pdo->prepare("UPDATE usuario SET contrasenia = :password WHERE email = :email");
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    } else {
        $message = "Verifica tu contraseña";
    }
}

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="..\style\favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Restablecer contraseña</title>
    <style>
        .instrucciones {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4 offset-md-4" style="background-color: #D2D1D1; border-radius:15px;">
                <br><br>
                <form role="form" method="POST">
                    <div class="form-group">
                        <label for="pwd1">Por favor ingrese su nueva contraseña</label>
                        <input type="password" class="form-control" id="pwd1" name="password1" placeholder="Contraseña">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="pwd2" name="password2" placeholder="Confirmar contraseña">
                    </div>
                    <?php if ($message != ""): ?>
                        <div class="alert alert-danger" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <span class="sr-only">Error:</span> <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($message_success)): ?>
                        <div class="alert alert-success" role="alert">
                            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                            <span class="sr-only">Success:</span> <?php echo $message_success; ?>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary btn-block" name="submit">Guardar Contraseña</button>
                    <br>
                    <label>Este enlace solo funcionará una vez por un período de tiempo limitado.</label>
                    <center> <a href="..\index.php">Volver al inicio de sesión</a></center>
                    <br>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> 
</body>
</html>