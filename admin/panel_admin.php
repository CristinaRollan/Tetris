<?php
session_start();

if (!isset($_SESSION["admin_name"])) {
    header("Location: admin.php");
    exit();
}

try {
  require_once('../parts/constantes.php');
  $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

  // Obtener todos los usuarios
  $stmt = $pdo->query("SELECT id_usuario, nombre_usuario, email, fecha_registro, bloqueado, validado FROM usuario");
  $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Obtener usuarios con el mayor puntuaje
  $stmt = $pdo->query("
      SELECT u.id_usuario, u.nombre_usuario, MAX(p.puntuaje_total) AS max_puntuaje
      FROM usuario u
      JOIN partida p ON u.id_usuario = p.id_usuario
      GROUP BY u.id_usuario, u.nombre_usuario
      ORDER BY max_puntuaje DESC
  ");
  $usuarios_con_puntuaje = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="icon" href="..\style\favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
<?php include_once "../parts/header.php"; ?>
    <div class="container mt-5">
        <h1>Panel de Administrador</h1>
        <?php if (isset($_SESSION['message_status'])): ?>
            <div class="alert alert-info">
                <?php
                foreach ($_SESSION['message_status'] as $status) {
                    echo htmlspecialchars($status) . '<br>';
                }
                unset($_SESSION['message_status']);
                ?>
            </div>
        <?php endif; ?>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-tab="dashboard">Usuarios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-tab="usuarios">Puntuajes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-tab="juego">Juego</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-tab="comunicacion">Comunicación</a>
            </li>
        </ul>
        <div id="dashboard" class="tab-content active mt-4">
            <h2>Gestionar Usuarios</h2>
            <form id="filter-form" method="get" class="mb-3">
                <div class="form-row">
                    <div class="col">
                        <input type="text" class="form-control" name="id" placeholder="ID">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="nombre" placeholder="Nombre">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="email" placeholder="Email">
                    </div>
                    <div class="col">
                        <select class="form-control" name="bloqueado">
                            <option value="">Bloqueado</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col">
                        <select class="form-control" name="validado">
                            <option value="">Validado</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </div>
            </form>
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Bloqueado</th>
                    <th>Validado</th>
                    <th>Fecha de Registro</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody id="user-table-body">
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['id_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td><?php echo $usuario['bloqueado'] ? 'Sí' : 'No'; ?></td>
                        <td><?php echo $usuario['validado'] ? 'No' : 'Sí'; ?></td>
                        <td><?php echo htmlspecialchars(date("d/m/Y H:i:s", strtotime($usuario['fecha_registro']))); ?></td>
                        <td>
                            <form action="editar_usuario.php" method="post" style="display:inline;">
                                <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                <button type="submit" class="btn btn-sm btn-warning">Editar</button>
                            </form>
                            <form action="eliminar_usuario.php" method="post" style="display:inline;">
                                <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="usuarios" class="tab-content mt-4">
            <h2>Puntuajes de los usuarios</h2>
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Puntaje Máximo</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios_con_puntuaje as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['id_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['max_puntuaje']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="juego" class="tab-content mt-4">
            <h2>Estadísticas del Juego</h2>
            <div class="chart-container" style="position: relative; height:40vh; width:80vw">
                <canvas id="gameStatsChart"></canvas>
            </div>
        </div>
        <div id="comunicacion" class="tab-content mt-4">
            <h2>Enviar Mensajes</h2>
            <form id="send-message-form" action="enviar_mensaje.php" method="post">
                <div class="form-group">
                    <label for="usuarios">Seleccionar Usuarios</label>
                    <select class="form-control" id="usuarios" name="usuarios[]" multiple required>
                        <option value="todos">Todos los Usuarios</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?php echo htmlspecialchars($usuario['id_usuario']); ?>"><?php echo htmlspecialchars($usuario['nombre_usuario']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subject">Asunto</label>
                    <input type="text" class="form-control" name="subject" id="subject" placeholder="Escribe el asunto aquí..." required>
                </div>
                <div class="form-group">
                    <label for="message">Mensaje</label>
                    <textarea class="form-control" name="message" id="message" placeholder="Escribe tu mensaje aquí..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
        </div>
    </div>
    <footer>
        <?php include_once('../parts/footer.php');?>
    </footer>
  <script src="script/admin.js"></script>
</body>
</html>