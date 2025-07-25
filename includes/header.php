<?php 
// header.php
// Included at the top of every page

if(session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once BASE_PATH . '/includes/functions.php';

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
    <link rel="stylesheet" href="/assets/css/style.css">
</head>