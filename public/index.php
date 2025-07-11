<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$pages = [
    "myfriends" => "My Friends",
    "myalbums" => "My Albums",
    "mypictures" => "My Pictures",
    "uploadpictures" => "Upload Pictures",
    "addalbum" => "Add Album",
    "addfriend" => "Add Friend",
    // "testconn" => "TESTING",
];

// Page routing
if ($requestUri === '/' || $requestUri === '/index')
{
    unset($pageTitle);
    $body = __DIR__ . '/../views/home.php';
}
else
{
    foreach ($pages as $page => $title)
    {
        if ($requestUri === "/$page")
        {
            $pageTitle = $title;
            $body =  __DIR__ . "/../views/$page.php";
        }
    }
}

// Render the header
include_once __DIR__ . '/../includes/header.php';

// Render the body
if (!isset($body))
{
    http_response_code(404);
    header('Location: /404.php');
    die();
}
else
{
    include_once $body;
}

// Add the footer
include_once __DIR__ . '/../includes/footer.php';
