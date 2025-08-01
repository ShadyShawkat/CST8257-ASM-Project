<?php
// signup.php
// Handles user sign up

require_once BASE_PATH . '/includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{

    if (strtolower($_POST['action']) == strtolower('submit'))
    {
        $errFound = 0;

        $id = validateID($_POST['userid']);
        $name = validateName($_POST['name']);
        $phone = validatePhone($_POST['phone']);
        $password = validatePass($_POST['password']);
        $confirmPass = confirmPass($_POST['password'], $_POST['confirmPass']);

        isset($id['error']) ? $errFound++ : $finalId = $id['success'];
        isset($name['error']) ? $errFound++ : $finalName = $name['success'];
        isset($phone['error']) ? $errFound++ : $finalPhone = $phone['success'];
        isset($password['error']) ? $errFound++ : $finalPass = $password['success'];;

        if (isset($confirmPass['error'])) $errFound++;

        if ($errFound === 0)
        {
            addUser($finalId, $finalName, $finalPhone, $finalPass);
        }
    }
    elseif (strtolower($_POST['action']) == strtolower('clear'))
    {
        var_dump(($_POST));
        $_POST = "";
    }
}

?>

<body class="d-flex flex-column h-100">
    <main class="d-flex flex-column container-fluid main-content h-100 justify-content-center align-items-center">
        <div class="container-sm p-4 border rounded" style="width:720px;">
            <h1 class="text-center">Sign Up</h1>
            <p>All fields are required.</p>
            <form method="post" id="signUpForm">
                <div class="form-group row mb-2">
                    <div class="col-sm-3">
                        <label class="form-label" for="userid">User ID : </label>
                    </div>
                    <div class="col-sm">
                        <input class="form-control" type="text" name="userid" id="userid" value="<?php echo htmlspecialchars($_POST['userid'] ?? ''); ?>">
                        <span class="text-danger"><?php echo isset($id['error']) ? $id['error'] : '' ?></span>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <div class="col-sm-3">
                        <label class="form-label" for="name">Name : </label>
                    </div>
                    <div class="col-sm">
                        <input class="form-control" type="text" name="name" id="name">
                        <span class="text-danger"><?php echo isset($name['error']) ? $name['error'] : '' ?></span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label class="form-label" for="userid">Phone Number : </label>
                    </div>
                    <div class="col-sm"><input class="form-control col-sm" type="tel" name="phone" id="phone">
                        <span class="text-danger"><?php echo isset($phone['error']) ? $phone['error'] : '' ?></span>
                    </div>
                </div>
                <hr class="mb-4">
                <div class="form-group row mb-2">
                    <div class="col-sm-3">
                        <label class="form-label" for="password">Password : </label>
                    </div>
                    <div class="col-sm"><input class="form-control col-sm" type="password" name="password" id="password">
                        <span class="text-danger"><?php echo isset($password['error']) ? $password['error'] : '' ?></span>
                    </div>
                </div>
                <div class="form-group row mb-4">
                    <div class="col-sm-3">
                        <label class="form-label" for="confirmPass">Password Again : </label>
                    </div>
                    <div class="col-sm"><input class="form-control col-sm" type="password" name="confirmPass" id="confirmPass">
                        <span class="text-danger"><?php echo isset($confirmPass['error']) ? $confirmPass['error'] : '' ?></span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm">
                        <button class="btn btn-primary col-sm-5" type="submit" name="action" id="submit" type="submit" value="Submit">Submit</button>
                        <button class="btn btn-secondary col-sm-5" type="submit" name="action" id="clear" type="reset" value="Clear">Clear</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="text-center mt-2">
            <a href="index">&larr; Go to Home</a>
        </div>
    </main>