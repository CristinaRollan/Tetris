<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = cleanInput($_POST["email"]);
    $password = cleanInput(MD5($_POST["password"]));
    $recaptcha = $_POST['g-recaptcha-response'];

	$secret_key = '6LdwOOUpAAAAAGl7nZAA-kbJVogqSk6XzirE3NB9'; 

	$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $recaptcha; 
	$response = file_get_contents($url); 
	$response = json_decode($response);

    if (empty($email) || empty($password)) {
        echo "Por favor, completa todos los campos.";
        exit();
    }
    if ($response->success == true){
        require_once('../parts/constantes.php');
        try {
            $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && $password == $user['contrasenia']) {
                $_SESSION["admin_name"] = $user['nombre'];
                $_SESSION["id_admin"] = $user['id_super_usuario'];
                echo "OK";
            } else {
                echo "Usuario o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Recaptcha fallida";
    }
}

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>