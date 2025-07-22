<?php
// login.php
// Handles user log ins

require_once __DIR__ . '../config/database.php';
require_once __DIR__ . '../includes/functions.php';

// Start session
if (session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE)
{
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $userid = htmlspecialchars($_POST["userid"]);
    $password = $_POST["password"];

    logIn('user001', 'pass123');
    // logIn($userid, $password);

    if ($_SESSION['loggedIn'] === true) header('Location: /');
}

?>

<body class="d-flex flex-column h-100">
    <main class="d-flex flex-column container-fluid main-content h-100 justify-content-center align-items-center">
        <div class="container-sm p-4 border rounded" style="width:576px;">
            <h1 class="text-center">Log In</h1>
            <p class="text-center">You need to <a href="signup">sign up</a> if you are a new user.</p>
            <form method="post" id="login" name="login">
                <div class="form-group row mb-2">
                    <label class="form-label col-sm-3" for="userid">User ID : </label>
                    <input class="form-control col-sm" type="text" name="userid" id="userid">
                </div>
                <div class="form-group row mb-2">
                    <label class="form-label col-sm-3" for="password">Password : </label>
                    <input class="form-control col-sm" type="password" name="password" id="password">
                </div>
                <div class="form-group row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm">
                        <input class="btn btn-primary col-sm-5" type="submit" value="Submit">
                        <input class="btn btn-secondary col-sm-5" type="reset" value="Clear">
                    </div>
                </div>
            </form>
        </div>
        <div class="text-center mt-2">
            <a href="/">&larr; Go to Home</a>
        </div>
    </main>

<?php