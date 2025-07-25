<?php
// menu.php
// Included for pages where the menu is needed.
?>

<body class="d-flex flex-column h-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="index"><img src="/assets/images/aclogo.png" width="50px"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-item nav-link" href="index">Home</a>
                    <a class="nav-item nav-link" href="myfriends">My Friends</a>
                    <a class="nav-item nav-link" href="myalbums">My Albums</a>
                    <a class="nav-item nav-link" href="mypictures">My Pictures</a>
                    <a class="nav-item nav-link" href="uploadpictures">Upload Pictures</a>
                    <?php if (isset($_SESSION['loggedIn'])) : ?>
                        <a class="nav-item nav-link" href="logout">Log Out</a>
                    <?php else: ?>
                        <a class="nav-item nav-link" href="login">Log In</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="container-fluid main-content h-100">