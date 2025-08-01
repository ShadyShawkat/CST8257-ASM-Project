<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// To gain access to constants
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'globals.php';
require_once BASE_PATH . '/includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $albumId = $_POST['albumId'] ?? null;
    $userId = trim($_SESSION['loggedID']);

    if ($albumId)
    {
        $deleted = deleteAlbum($albumId, $userId);
        echo json_encode($deleted);
        exit();
    }
}