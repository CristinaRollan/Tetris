<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = cleanInput($_POST["username"]);
    $email = cleanInput($_POST["email"]);
    $password1 = cleanInput(MD5($_POST["password1"]));
    $password2 = cleanInput(MD5($_POST["password2"]));
    $recaptcha = $_POST['g-recaptcha-response'];

	$secret_key = '6LdwOOUpAAAAAGl7nZAA-kbJVogqSk6XzirE3NB9'; 

	$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $recaptcha; 
	$response = file_get_contents($url); 
	$response = json_decode($response);

    if ($response && $response->success == true && $password1 == $password2) {
        try {
            require_once('../parts/constantes.php');

            $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Verificar si el email ya está registrado y su estado
            $sql = "SELECT validado, bloqueado FROM usuario WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row['bloqueado'] == 1) {
                    echo "account_blocked";
                } elseif ($row['validado'] == 1) {
                    echo "account_not_validated";
                } else {
                    echo "email_exists";
                }
            } else {
                // Generar token
                $key = md5(time() + 123456789 % rand(4000, 55000000));
                
                // Insertar nuevo usuario
                $sql = "INSERT INTO usuario (nombre_usuario, email, contrasenia, bloqueado, validado) VALUES (:username, :email, :password, 0, 1)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password1, PDO::PARAM_STR);
                $stmt->execute();

                // Insertar token en la base de datos
                $sql = "INSERT INTO validacion_usuario (email, temp_key) VALUES (:email, :temp_key)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':temp_key', $key, PDO::PARAM_STR);
                
                if ($stmt->execute()) {
                    // Enviar correo electrónico con la información para restablecer la contraseña
                    $to = $email;
                    $subject = 'Confirmación de registro';
                    $msg = "Copie el enlace y péguelo en la barra de direcciones de su navegador.". "\r\n"."https://www.cristinarollan.es/registro/validar_ingreso.php?key=" . $key . "&email=" . $email;
                    $headers = 'From: cristinarollan@cristinarollan.es' . "\r\n" . 
                                'MIME-Version: 1.0' . "\r\n" .
                                'Content-type: text/plain; charset=utf-8' . "\r\n" .
                                'X-Mailer: PHP/' . phpversion();
                    mail($to, $subject, $msg, $headers);
                    echo "OK";
                } else {
                    echo "error";
                }
            }
        } catch (PDOException $e) {
            echo "error: " . $e->getMessage();
        }
    } else {
        echo "error: Las contraseñas no coinciden o reCAPTCHA fallado.";
    }
}



function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    
    return $data;
}
?>