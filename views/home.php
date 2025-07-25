<?php
// Home.php
// Home page view for the website.

if (isset($_SESSION['loggedIn']))
{
    $isLoggedIn = $_SESSION['loggedIn'];
    $userName = $_SESSION['loggedName'];
}

?>
<div class="container mt-2">
    <?php
    // Message to show if user has not logged in yet 
    if (!isset($isLoggedIn)) : 
    ?>
        <h1 class="h1 mb-4">Welcome to Algonquin Social Media Website</h1>

        <p>If you have never used this before, you have to <a href="signup">sign up</a> first.</p>

        <p>If you have already signed up, you can <a href="login">log in</a> now.</p>
    <?php
    // Message to show if user is logged in
    else : 
    ?>
        <h1 class="h1 mb-4">Welcome back <strong><?php echo $userName ?></strong>!</h1>

        <p>If you are not <?php echo $userName ?>, click <a href="login">here</a>.</p>
    <?php endif; ?>

</div>