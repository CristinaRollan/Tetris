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
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            die("Usuario no encontrado.");
        }

        if (isset($_POST['nombre_usuario'], $_POST['email'], $_POST['bloqueado'], $_POST['validado'])) {
            $nombre_usuario = $_POST['nombre_usuario'];
            $email = $_POST['email'];
            $bloqueado = $_POST['bloqueado'];
            $validado = $_POST['validado'];

            $stmt = $pdo->prepare("UPDATE usuario SET nombre_usuario = ?, email = ?, bloqueado = ?, validado = ? WHERE id_usuario = ?");
            $stmt->execute([$nombre_usuario, $email, $bloqueado, $validado, $id_usuario]);

            header("Location: panel_admin.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: panel_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Editar Usuario</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h1>Editar Usuario</h1>
    <form action="editar_usuario.php" method="post">
      <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($usuario['id_usuario']); ?>">
      <div class="form-group">
        <label for="nombre_usuario">Nombre de Usuario</label>
        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario['nombre_usuario']); ?>" required>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
      </div>
      <div class="form-group">
        <label for="bloqueado">Bloqueado</label>
        <select class="form-control" id="bloqueado" name="bloqueado" required>
          <option value="0" <?php echo $usuario['bloqueado'] == 0 ? 'selected' : ''; ?>>No</option>
          <option value="1" <?php echo $usuario['bloqueado'] == 1 ? 'selected' : ''; ?>>Sí</option>
        </select>
      </div>
      <div class="form-group">
        <label for="validado">Validado</label>
        <select class="form-control" id="validado" name="validado" required>
          <option value="0" <?php echo $usuario['validado'] == 0 ? 'selected' : ''; ?>>Sí</option>
          <option value="1" <?php echo $usuario['validado'] == 1 ? 'selected' : ''; ?>>No</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Guardar Cambios</button>
      <a href="panel_admin.php" class="btn btn-secondary">Cancelar</a>
    </form>
  </div>
</body>
</html>