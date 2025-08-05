<?php
// login.php
// Page handling user logins

require_once BASE_PATH . '/config/database.php';

// Start session
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (strtolower($_POST['submit']) === strtolower('submit'))
    {
        $loginError = "";
        if ((isset($_POST["userid"]) and !empty($_POST["userid"])) and (isset($_POST["password"]) and !empty($_POST["password"])))
        {
            $userid = htmlspecialchars($_POST["userid"]);
            $password = $_POST["password"];

    logIn($userid, $password);
    // logIn('user001', 'pass123');
    // logIn('user007', 'pass404');
    // logIn($userid, $password);

            if (isset($_SESSION['loggedIn'])) $loggedInFlag = $_SESSION['loggedIn'];
            if (isset($loggedInFlag) and $loggedInFlag === true) header('Location: index');
        }
        else
        {
            $loginError = "Username and password required.";
        }
    }
    elseif (strtolower($_POST['submit']) === strtolower('submit')) {
        $_POST = "";
        $loginError = "";
    }
}

?>

<body>
    <main class="d-flex flex-column container-fluid main-content h-100 justify-content-center align-items-center">
        <div class="container-sm p-4 border rounded" style="width:576px;">
            <h1 class="text-center">Log In</h1>
            <p class="text-center">You need to <a href="signup">sign up</a> if you are a new user.</p>
            <?php if (isset($loginError) and !empty($loginError)): ?>
                <div class="alert alert-danger alert-dismissible"><?php echo $loginError ?></div>
            <?php endif; ?>
            <form method="post" id="login" name="login">
                <div class="form-group row mb-2">
                    <label class="form-label col-sm-3" for="userid">User ID : </label>
                    <input class="form-control col-sm" type="text" name="userid" id="userid">
                </div>
                <div class="form-group row mb-2">
                    <label class="form-labe col-sm-3" for="password">Password : </label>
                    <input class="form-control col-sm" type="password" name="password" id="password">
                </div>
                <div class="form-group row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm">
                        <button class="btn btn-primary col-sm-5" type="submit" name="submit" value="Submit">Submit</button>
                        <button class="btn btn-secondary col-sm-5" type="submit" name="submit" value="Clear">Clear</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="text-center mt-2">
            <a href="index">&larr; Go to Home</a>
        </div>
    </main>