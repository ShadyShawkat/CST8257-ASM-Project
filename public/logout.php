<?php
include_once __DIR__ . '/../includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if($_POST["logout"] === 'yes') {
        session_unset();
        session_destroy();

        header('Location: /');
        exit();
    }
    else {
        header('Location: /');  
    }
}
?>

<div class="container justify-content-center align-items-center text-center" style="width:576px;">
    <h1 class="h1">Logout</h1>
    <p>Are you sure you want to log out?</p>
    <form method="post">
        <button class="btn btn-primary" type="submit" name="logout" value="yes">Yes</button>
        <button class="btn btn-secondary" type="submit" name="logout" value="no">No</button>
    </form>
</div>


<?php

include_once __DIR__ . '/../includes/footer.php';
