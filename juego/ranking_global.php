<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: ../index.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

try {
    require_once '../parts/constantes.php';
    $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener la última partida del usuario actual
    $queryLastScore = "SELECT puntuaje_total FROM partida WHERE id_usuario = :id_usuario ORDER BY fecha DESC LIMIT 1";
    $stmtLastScore = $pdo->prepare($queryLastScore);
    $stmtLastScore->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
    $stmtLastScore->execute();
    $lastScore = $stmtLastScore->fetch(PDO::FETCH_ASSOC);
    $lastScoreValue = $lastScore ? $lastScore['puntuaje_total'] : 'No hay partidas registradas';

    // Consulta para obtener los 10 mejores jugadores
    $query = "
        SELECT p.id_usuario, u.nombre_usuario, MAX(p.puntuaje_total) as max_puntuaje
        FROM partida p
        JOIN usuario u ON p.id_usuario = u.id_usuario
        GROUP BY p.id_usuario, u.nombre_usuario
        ORDER BY max_puntuaje DESC
        LIMIT 10";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener el ranking: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="..\style\favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Ranking Global</title>
</head>
<body>
    
    <?php include_once "../parts/header.php";?>

    <div class="container mt-5">
        <h2>Última Puntuación Obtenida</h2>
            <p class="lead">Puntuación obtenida: <strong><?php echo htmlspecialchars($lastScoreValue, ENT_QUOTES, 'UTF-8'); ?> puntos.</strong></p>

        <h2>Ranking Global:</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre de usuario</th>
                    <th>Puntuación máxima</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($ranking as $index => $record): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($record['nombre_usuario'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo $record['max_puntuaje']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="tetris.php" class="btn btn-success">Volver a jugar</a>
        <a href="../usuario/usuario.php" class="btn btn-info">Volver a área personal</a>
    </div>
    <footer>
        <?php include_once('../parts/footer.php');?>
    </footer>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> 
</body>
</html>