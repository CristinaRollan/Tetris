<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID de usuario del usuario actual
$id_usuario = $_SESSION["id_usuario"];

try {
    require_once '../parts/constantes.php';
    $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener las últimas 10 partidas del usuario actual
    $query = "SELECT id_partida, id_usuario, puntuaje_total, fecha FROM partida WHERE id_usuario = :id_usuario ORDER BY fecha DESC LIMIT 5";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $partidas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener las partidas: " . $e->getMessage();
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
    <title>Área Personal</title>
    <style>
        .instrucciones {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    
    <?php include_once "../parts/header.php";?>

    <div class="container mt-5">
    <h2>Historial de tus últimas 5 partidas:</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Puntuación total</th>
                    <th>Fecha</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($partidas as $index => $partida): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo $partida['puntuaje_total']; ?></td>
                        <td><?php echo (date("d/m/Y H:i:s", strtotime($partida['fecha']))); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <a href="../juego/tetris.php" class="btn btn-success">Jugar</a>
        <div class="instrucciones">
            <h3>Instrucciones del Juego</h3>
            <p>Para jugar, sigue estos pasos:</p>
            <ul>
                <li>Usa las teclas de flecha para mover las piezas.</li>
                <li>Presiona la tecla "C" para guardar e intercambiar piezas.</li>
                <li>Presiona la tecla "Espacio" para hacer caer la pieza directamente.</li>
                <li>Completa líneas para ganar puntos y evita que las piezas lleguen a la parte superior.</li>
            </ul>
            <p>¡Diviértete jugando!</p>
        </div>
    </div>
        <footer>
            <?php include_once('../parts/footer.php');?>
        </footer>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> 
</body>
</html>