<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>TETRIS</title>
  <link rel="icon" href="..\style\favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="..\style\styletetris.css">
</head>
<body>
<audio id="background-music" src="../music/tetrisMusic.mp3" autoplay loop></audio>
  <div class="container-fluid">
    <div class="row justify-content-center">
        <div id="gameContainer" class="d-flex flex-wrap justify-content-center">
              <div id="holdText" class="hold-text">HOLD</div>
              <div id="nextPiece"></div>
        </div>         
        <canvas width="320" height="640" id="game"></canvas>  
        <div id="gameContainer" class="d-flex flex-wrap justify-content-center">
          <div class="next-text">NEXT PIECES</div>
          <div id="nextPieceContainer"></div>
            <div class="score-container">
              <div class="score" id="score">Score: 0</div>
            </div>
        </div>
            <div id="controls">
                <button id="save-piece" class="glyphicon glyphicon-save disable-dbl-tap-zoom"></button>
                <button id="move-left" class="glyphicon glyphicon-menu-left disable-dbl-tap-zoom"></button>
                <button id="move-right" class="glyphicon glyphicon-menu-right disable-dbl-tap-zoom"></button>
                <button id="rotate-piece" class="glyphicon glyphicon-repeat disable-dbl-tap-zoom"></button>
                <button id="drop-piece" class="glyphicon glyphicon-menu-down disable-dbl-tap-zoom"></button>
            </div>
          </div>
        </div>
  </div>
  <script type="module" src="controller.js"></script>
</body>
</html>