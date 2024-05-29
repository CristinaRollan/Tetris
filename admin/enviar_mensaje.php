<?php
session_start();

if (!isset($_SESSION["admin_name"])) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuarios = isset($_POST['usuarios']) ? $_POST['usuarios'] : [];
    $subject = isset($_POST['subject']) ? cleanInput($_POST['subject']) : '';
    $message = isset($_POST['message']) ? cleanInput($_POST['message']) : '';

    if (empty($usuarios) || empty($subject) || empty($message)) {
        $_SESSION['message_status'] = ["Error: Todos los campos son obligatorios."];
        header("Location: panel_admin.php");
        exit();
    }

    try {
        require_once '../parts/constantes.php';
        $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $message_status = [];

        if (in_array('todos', $usuarios)) {
            $stmt = $pdo->query("SELECT email FROM usuario");
            $usuarios = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $placeholders = str_repeat('?,', count($usuarios) - 1) . '?';
            $stmt = $pdo->prepare("SELECT email FROM usuario WHERE id_usuario IN ($placeholders)");
            $stmt->execute($usuarios);
            $usuarios = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        foreach ($usuarios as $email) {
            $to = $email;
            $headers = 'From: cristinarollan@cristinarollan.es' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
            
            if (mail($to, $subject, $message, $headers)) {
                $message_status[] = "Mensaje enviado a: " . htmlspecialchars($email);
            } else {
                $message_status[] = "Error al enviar mensaje a: " . htmlspecialchars($email);
                error_log("Error al enviar mensaje a: " . htmlspecialchars($email));
            }
        }

        $_SESSION['message_status'] = $message_status;
    } catch (PDOException $e) {
        $_SESSION['message_status'] = ["Error: " . $e->getMessage()];
        error_log("Error de PDO: " . $e->getMessage());
    }

    header("Location: panel_admin.php");
    exit();
}

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>