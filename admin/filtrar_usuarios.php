<?php
session_start();

if (!isset($_SESSION["admin_name"])) {
    header("Location: admin.php");
    exit();
}
try {
    require_once('../parts/constantes.php');
    $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    $query = "SELECT id_usuario, nombre_usuario, email, fecha_registro, bloqueado, validado FROM usuario WHERE 1=1";
    $params = [];

    if (!empty($_GET['id'])) {
        $query .= " AND id_usuario = :id";
        $params[':id'] = $_GET['id'];
    }
    if (!empty($_GET['nombre'])) {
        $query .= " AND nombre_usuario LIKE :nombre";
        $params[':nombre'] = '%' . $_GET['nombre'] . '%';
    }
    if (!empty($_GET['email'])) {
        $query .= " AND email LIKE :email";
        $params[':email'] = '%' . $_GET['email'] . '%';
    }
    if (isset($_GET['bloqueado']) && $_GET['bloqueado'] !== '') {
        $query .= " AND bloqueado = :bloqueado";
        $params[':bloqueado'] = $_GET['bloqueado'];
    }
    if (isset($_GET['validado']) && $_GET['validado'] !== '') {
        $query .= " AND validado = :validado";
        $params[':validado'] = $_GET['validado'];
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($usuarios);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>