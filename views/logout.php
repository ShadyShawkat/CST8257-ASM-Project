<?php
// lougout.php
// Page handling user logouts

if ($_SESSION['loggedIn'])
{
    session_unset();
    session_destroy();
}

header('Location: index');
exit();