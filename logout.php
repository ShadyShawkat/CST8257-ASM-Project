<?php

if ($_SESSION['loggedIn'])
{
    session_unset();
    session_destroy();
}

header('Location: /');
exit();