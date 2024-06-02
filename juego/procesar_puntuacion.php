<?php
session_start();
$id_usuario = $_SESSION['id_usuario'];

try {
    require_once('../parts/constantes.php');

    $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $score = $data['score'];

        $sql = "INSERT INTO partida (id_usuario, puntuaje_total) VALUES (:id_usuario, :puntuaje_total)";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':puntuaje_total' => $score
        ]);

        echo json_encode(['status' => 'success']);
    }

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>