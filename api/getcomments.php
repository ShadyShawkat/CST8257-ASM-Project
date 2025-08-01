<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// To gain access to constants
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'globals.php';
require_once BASE_PATH . '/includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $pictureId = $_POST['pictureId'] ?? null;

    if ($pictureId)
    {
        $comments = getComments($pictureId);
        $result = [
            'status' => 'success',
            'commentsList' => $comments,
            'userId' => trim($_SESSION['loggedID'])
        ];

    //     // Save to session.
    //     $_SESSION['pictureList'] = $pictures;

        echo json_encode($result);
        exit();
    }
}
