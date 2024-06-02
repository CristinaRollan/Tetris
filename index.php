<?php
session_start();

if (isset($_SESSION["username"])) {
  header("Location: usuario.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tetris Game</title>
  <link rel="icon" href="style\favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="style\style.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <script src="https://accounts.google.com/gsi/client" async defer></script>
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
            <input type="submit" value="Iniciar sesión" onclick="submitForm(event)">            
            <div class="links">
              <a href="recuperacion/reset_password.php">Recuperar contraseña</a>
              <a href="registro/registro.php">Crear Cuenta</a>
            </div>
          </div>
          <div id="g_id_onload"
            data-client_id="440042372836-l1dls17m5qeo31l8mesujiee4aqsb45r.apps.googleusercontent.com"
            data-context="signin"
            data-ux_mode="popup"
            data-callback="handleCredentialResponse"
            data-auto_prompt="false">
          </div>
          <div class="g_id_signin" 
            data-type="standard"
            data-shape="rectangular"
            data-theme="outline"
            data-text="sign_in_with"
            data-size="large">
          </div>
        </div>
      </div>
    </div>
  </section>
  <script src="scriptIni/index.js"></script>
</body>
</html>