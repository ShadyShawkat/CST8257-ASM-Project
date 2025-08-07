<?php
// index.php
// Main entry point

require_once 'globals.php';
// include_once 'config/hash_passwords_once.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$trimmedUri = trim($requestUri, '/');
$splitUri = explode('/', $trimmedUri);

$page = end($splitUri);

// If you want to check the routing, uncomment the echoes below.
// echo "ROOT FOLDER : ".BASE_PATH  . "<br>";
// echo "RAW REQUEST URI : ". $_SERVER["REQUEST_URI"] . "<br>";
// echo "PARSED REQUEST URI : ". $requestUri . "<br>";
// echo "SCRIPT NAME : ".  $_SERVER['SCRIPT_NAME'] . "<br>";
// echo "PAGE : " . $page;

// Main view page
$rootAliases = ["", "/", "home", "index", trim(BASE_URL, '/')];

// Pages that need the header, menu, and footer
$regularPages = [
    "myfriends" => "My Friends",
    "myalbums" => "My Albums",
    "mypictures" => "My Pictures",
    "uploadpictures" => "Upload Pictures",
    "addalbum" => "Add Album",
    "addfriend" => "Add Friend",
    "friendpictures" => "Friend Pictures",
];

// Pages that need the header, and footer
$specialPages = [
    "login" => "Log In",
    "logout" => "Log Out",
    "signup" => "Sign Up",
];

// Combine all routable pages
$allRoutablePages = array_merge($regularPages, $specialPages);

// Handle routing for the hompage
if (in_array($page, $rootAliases))
{
    unset($pageTitle);
    $body = BASE_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'home.php';
}
else
{
    // Check if the requested page exists in our routable pages
    if (array_key_exists($page, $allRoutablePages))
    {
        $pageTitle = $allRoutablePages[$page];
        $_SESSION['page'] = $page;
        $body = BASE_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR  . $page . '.php';
    }
    else
    {
        // Page does not exist - set 404 status and include the 404 page directly
        header("HTTP/1.0 404 Not Found");
        $pageTitle = "Page Not Found";
        $body = BASE_PATH . '/404.php';
    }
}

// Add the header
include_once BASE_PATH . '/includes/header.php';

if (!isset($_SESSION['loggedIn']))
{
    if (!in_array($page, $rootAliases) && !in_array($page, array_keys($specialPages)))
    {
        header("Location: index");
        exit();
    }
}

// Add the menu if its in any of the homepage alias or the regular pages
if (in_array($page, $rootAliases) or array_key_exists($page, $regularPages))
{
    // Add the menu to regular pages
    include_once BASE_PATH . '/includes/menu.php';
}

// Include the body content
if (file_exists($body))
{
    include_once $body;
}
else
{
    // Endpoint do not exist
    header("HTTP/1.0 404 Not Found");
    $pageTitle = "Page Not Found";
    include_once BASE_PATH . '/404.php'; // Fallback to 404 if view is missing
}

// Add the footer
include_once BASE_PATH . '/includes/footer.php';
