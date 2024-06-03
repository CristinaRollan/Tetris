<?php
if (isset($_SESSION["admin_name"])) {
    $username = $_SESSION["admin_name"];
    $isAdmin = true;
} elseif (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
    $isAdmin = false;
} else {
    $username = "Invitado";
    $isAdmin = false;
}

?>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand"><?php echo "Bienvenido, $username"; ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <?php if (isset($_SESSION["username"]) || isset($_SESSION["admin_name"])): ?>
                            <a class="nav-link" href="../parts/logout.php">Cerrar sesión</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>