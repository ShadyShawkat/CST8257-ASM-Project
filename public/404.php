<?php
// Ensure the 404 status code is sent even if served directly
http_response_code(404);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Gamaliel Cabana">
    <meta name="email" content="90541124989@algonquincdistudent.ca">

    <title>Algonquin Social Media</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/style.css">
</head>

<body class="vh-100 align-content-center">
    <div class="container text-center">
        <header class="row">
            <a class="" href="/"><img src="/images/aclogo.png" width="100px"></a>
            <h1 class="h1 mb-5">Algonquin Social Media</h1>
        </header>
        <main class="row mb-3">
            <h2 class="h2">Oops! Page Not Found</h2>
            <p>We couldn't find the page you were looking for.</p>
            <p>You can try going back to the <a href="/">homepage</a>.</p>
        </main>
    </div>

    <?php

    include_once __DIR__ . '/../includes/footer.php';

    ?>