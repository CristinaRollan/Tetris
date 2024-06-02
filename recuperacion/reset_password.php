<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = cleanInput($_POST["email"]);

    // Comprueba si el formato de correo electrónico es válido en servidor
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "¡Por favor, introduce un correo electrónico válido!";
    } else {
        try {
            require_once('../parts/constantes.php');

            $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT nombre_usuario, email, bloqueado, validado FROM usuario WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                if ($user['bloqueado'] == 1) {
                    echo "account_blocked";
                    exit();
                } elseif ($user['validado'] == 1) {
                    echo "account_not_validated";
                    exit();
                } else {                
                    // Generar token
                    $key = md5(time() + 123456789 % rand(4000, 55000000));
                    
                    // Insertar esta clave temporal en la base de datos
                    $stmt = $pdo->prepare("INSERT INTO recuperar_contrasenia (email, temp_key) VALUES (:email, :temp_key)");
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':temp_key', $key, PDO::PARAM_STR);
                    $stmt->execute();
                    
                    // Enviar correo electrónico con la información para restablecer la contraseña
                    $to = $email;
                    $subject = 'Restablecer contraseña';
                    $msg = "Copie el enlace y péguelo en la barra de direcciones de su navegador.". "\r\n"."www.cristinarollan.es/recuperacion/recuperacion_reset.php?key=" . $key . "&email=" . $email;
                    $headers = 'From: cristinarollan@cristinarollan.es' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
                    mail($to, $subject, $msg, $headers);
                    echo "success";
                    exit();
                }
            } else {
                echo "NoAccountError";
                exit();
            }
        }catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            exit();
        }
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
    <title>Recuperar contraseña</title>
    <link rel="icon" href="..\style\favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="..\style\style3.css">
</head>
<body>
    <section>
        <?php
        $num_squares = 700;
        for ($i = 0; $i < $num_squares; $i++) {
            echo '<span></span>';
        }
        ?>
        <div class="signin">
            <div class="content">
                <h1>Tetris</h1>
                <h2>Recuperar contraseña</h2>
                <p>Ingrese su correo electrónico para restablecer su contraseña.</p>
                <div class="form">
                        <div class="inputBox">
                            <input type="email" id="email" name="email" required>
                            <i>Email</i>
                        </div>
                        <div class="inputBox">
                            <input type="submit" id="submit" value="Recuperar contraseña">
                        </div>
                </div>
            </div>
        </div>
    </section>
    <script src="script/recuperacion.js"></script>
</body>
</html>