<?php 
session_start();

if (isset($_GET['key']) && isset($_GET['email'])) {
    $key = cleanInput($_GET['key']);
    $email = cleanInput($_GET['email']);
    
    try {
        include("../parts/constantes.php");
        $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT email, temp_key FROM validacion_usuario WHERE email = :email AND temp_key = :temp_key");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':temp_key', $key, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() != 1) {
            $_SESSION['message'] = "Esta URL no es válida o ya se ha utilizado. Por favor, verifique e intente nuevamente.";
            header('Location: ../index.php');
            exit;
        }
        
        // Iniciar la transacción
        $pdo->beginTransaction();
        
        // Eliminar el registro de validación
        $stmt = $pdo->prepare("DELETE FROM validacion_usuario WHERE email = :email AND temp_key = :temp_key");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':temp_key', $key, PDO::PARAM_STR);
        $stmt->execute();
        
        // Actualizar la validación del usuario
        $stmt = $pdo->prepare("UPDATE usuario SET validado = 0 WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        // Confirmar la transacción
        $pdo->commit();
        
        $_SESSION['message'] = "Su cuenta ha sido validada exitosamente.";
        header('Location: ../index.php');
        exit;
    } catch (PDOException $e) {
        // Revertir la transacción en caso de error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['message'] = "Error: " . $e->getMessage();
        header('Location: ../index.php');
        exit;
    }
} else {
    header('Location: ../index.php');
    exit;
}

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    
    return $data;
}
?>