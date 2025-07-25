<?php
// signup.php
// Handles user sign up
?>

<body class="d-flex flex-column h-100">
    <main class="d-flex flex-column container-fluid main-content h-100 justify-content-center align-items-center">
        <div class="container-sm p-4 border rounded" style="width:720px;">
            <h1 class="text-center">Sign Up</h1>
            <p>All fields are required.</p>
            <form method="post">
                <div class="form-group row mb-2">
                    <label class="form-label col-sm-3" for="userid">User ID : </label>
                    <input class="form-control col-sm" type="text" name="userid" id="userid">
                </div>
                <div class="form-group row mb-2">
                    <label class="form-label col-sm-3" for="name">Name : </label>
                    <input class="form-control col-sm" type="text" name="name" id="name">
                </div>
                <div class="form-group row mb-2">
                    <label class="form-label col-sm-3" for="userid">Phone Number : </label>
                    <input class="form-control col-sm" type="tel" name="phone" id="phone">
                </div>
                <div class="form-group row mb-2">
                    <label class="form-label col-sm-3" for="password">Password : </label>
                    <input class="form-control col-sm" type="password" name="password" id="password">
                </div>
                <div class="form-group row mb-2">
                    <label class="form-label col-sm-3" for="passwordagain">Password Again : </label>
                    <input class="form-control col-sm" type="password" name="passwordagain" id="passwordagain">
                </div>
                <div class="form-group row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm">
                        <input class="btn btn-primary col-sm-5" type="submit" value="Submit">
                        <input class="btn btn-secondary col-sm-5" type="reset" value="Clear">
                    </div>
                </div>
            </form>
        </div>
        <div class="text-center mt-2">
            <a href="index">&larr; Go to Home</a>
        </div>
    </main>