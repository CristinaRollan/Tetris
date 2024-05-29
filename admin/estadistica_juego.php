<?php
try {
    require_once '../parts/constantes.php';
    $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $stmt = $pdo->prepare("
        SELECT u.nombre_usuario, COUNT(p.id_partida) AS games_played
        FROM usuario u
        JOIN partida p ON u.id_usuario = p.id_usuario
        GROUP BY u.nombre_usuario
    ");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>