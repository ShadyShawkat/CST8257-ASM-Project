<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// To gain access to constants
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'globals.php';
require_once BASE_PATH . '/includes/functions.php';

header('Content-Type: application/json');

$authorId = trim($_SESSION['loggedID']);

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $pictureId = $_POST['pictureId'] ?? null;
    $comment = $_POST['commentText'];

    if ($pictureId)
    {
        $addedCommentId = addComment($authorId, $pictureId, $comment);

        $result = [
            'status' => 'success',
            // 'commentAdded' => $addedCommentId,
            // 'userId' => trim($_SESSION['loggedID'])
        ];

        echo json_encode($result);
        exit();
    }
}
