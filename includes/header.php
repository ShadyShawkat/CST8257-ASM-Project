<?php 

session_start(); 

?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Gamaliel Cabana">
    <meta name="email" content="90541124989@algonquincdistudent.ca">

    <title><?php echo isset($pageTitle) ? 'Algonquin Social Media - ' . htmlspecialchars($pageTitle) : 'Algonquin Social Media'; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/style.css">
</head>

<body class="d-flex flex-column h-100">

    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="/"><img src="/images/aclogo.png" width="50px"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-item nav-link" href="/">Home</a>
                    <a class="nav-item nav-link" href="/myfriends">My Friends</a>
                    <a class="nav-item nav-link" href="/myalbums">My Albums</a>
                    <a class="nav-item nav-link" href="/mypictures">My Pictures</a>
                    <a class="nav-item nav-link" href="/uploadpictures">Upload Pictures</a>
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