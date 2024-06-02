<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta</title>
    <link rel="icon" href="..\style\favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="..\style\style2.css">
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
                    <h2>Crear una cuenta</h2>
                    <div class="form">
                        <div class="inputBox">
                            <input type="text" id="username" maxlength="30" required>
                            <i>Nombre de usuario</i>
                        </div>
                        <div class="inputBox">
                            <input type="email" id="email" maxlength="50" required>
                            <i>Email</i>
                        </div>
                        <div class="inputBox">
                            <input type="password" id="password1" maxlength="50" required>
                            <i>Contraseña</i>
                        </div>
                        <div class="inputBox">
                            <input type="password" id="password2" maxlength="50" required>
                            <i>Confirmar contraseña</i>
                        </div>
                        <div>
                            <input type="checkbox" name="privacy" id="privacy" required> <a class="privacy" href="proteccion_datos.php">Política de protección de datos</a>
                        </div>
                        <div class="g-recaptcha" data-sitekey="6LdwOOUpAAAAAHEsXofdXZVmbptJbjA707b4uV08"></div>
                        <div class="inputBox">
                            <input type="submit" value="Registrarse" onclick="submitForm(event)">
                        </div>
                        <div class="links">
                            <h5>¿Tienes una cuenta?</h5>
                            <a href="../index.php">Iniciar sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <script src="script/registro.js"></script>
</body>
</html>