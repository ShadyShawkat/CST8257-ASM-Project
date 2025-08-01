<?php
// friendspicture.php
// Show friend's public albums

?>

<div class="container mt-2">

    <h1 class="h1">{FRIENDS_NAME}'s Pictures</h1>
    <?php

    if (isset($_SESSION['userName']))
    {
        loggedInMsg($_SESSION['userName']);
    }
    ?>

</div>