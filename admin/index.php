<?php
session_start();

if (isset($_SESSION["admin_name"])) {
  header("Location: panel_admin.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en" >
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
    <link rel="icon" href="..\style\favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="..\style\style4.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  </head>
  <body>
    <section>
    <?php
      $num_squares = 700;
      for ($i = 0; $i < $num_squares; $i++) {
        echo '<span></span>';
      }
    ?>
      <div class="signin">
        <div class="content">
        <h1>Tetris</h1>
          <h2>Admin</h2>
          <div class="form">
            <div class="inputBox">
              <input type="email" id="email" required>
              <i>Email</i>
            </div>
            <div class="inputBox">
              <input type="password" id="password" required>
              <i>Contraseña</i>
            </div>
            <div class="g-recaptcha" data-sitekey="6LdwOOUpAAAAAHEsXofdXZVmbptJbjA707b4uV08"></div>
            <div class="inputBox">
              <input type="submit" value="Iniciar sesión" onclick="submitForm()">            
            </div>
          </div>
        </div>
      </div>
  </section>
  <script src="script/index.js"></script>
</body>
</html>