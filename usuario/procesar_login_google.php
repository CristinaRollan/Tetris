<?php
session_start();

if (!isset($_POST['credential'])) {
    echo "Error: no se ha enviado el token.";
    exit();
}

$token = $_POST['credential'];
$client_id = "440042372836-l1dls17m5qeo31l8mesujiee4aqsb45r.apps.googleusercontent.com";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://oauth2.googleapis.com/tokeninfo?id_token=" . $token);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    $response_data = json_decode($response, true);

    if (isset($response_data['aud']) && $response_data['aud'] == $client_id) {
        $userid = $response_data['sub'];
        $username = $response_data['name'];
        $email = $response_data['email'];

        try {
            require_once('../parts/constantes.php');
            $conn = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT id_usuario, bloqueado FROM usuario WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $user_data = $stmt->fetch();
                if ($user_data['bloqueado'] == 1) {
                    echo "account_blocked";
                    exit();
                }
                $_SESSION['id_usuario'] = $user_data['id_usuario'];
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                echo "OK";
            } else {
                $stmt = $conn->prepare("INSERT INTO usuario (nombre_usuario, email, contrasenia, bloqueado, validado) VALUES (?, ?, '', 0, 0)");
                $stmt->execute([$username, $email]);
                $db_userid = $conn->lastInsertId();
                $_SESSION['id_usuario'] = $db_userid;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                echo "OK";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error: token inválido.";
    }
} else {
    echo "Error: no se pudo verificar el token.";
}
?>