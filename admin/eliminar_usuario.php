<?php
session_start();

if (!isset($_SESSION["admin_name"])) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_usuario"])) {
    $id_usuario = $_POST["id_usuario"];

    require_once('../parts/constantes.php');

    try {
        $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $stmt = $pdo->prepare("DELETE FROM usuario WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);

        header("Location: panel_admin.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: panel_admin.php");
    exit();
}