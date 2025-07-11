<?php

?>

<div class="container mt-2">

    <h1 class="h1">My Friends</h1>
    <?php

    if (isset($_SESSION['userName']))
    {
        loggedInMsg($_SESSION['userName']);
    }
    ?>

</div>