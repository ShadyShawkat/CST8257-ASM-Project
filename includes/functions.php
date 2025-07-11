<?php

// PHP containing the various functions for the entire app/website.

// Simple function to test database connection.
function testDB()
{
    $conn = new mysqli(APP_SERVER, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error)
    {
        die("CONNECTION FAILED: " . $conn->connect_error);
    }

    echo "CONNECTION SUCCESSFUL";

    $conn->close();
}

// Function to log user in
function logIn($userName, $password)
{
    $db = Database::getInstance();

    $conn = $db->getConnection();

    // Refactor to use hash. For now, I'm using plaintext password
    // $stmt = $conn -> prepare("SELECT UserId, password_hash FROM User WHERE UserId = ?");
    $stmt = $conn->prepare("SELECT UserID, Name, Password FROM User WHERE UserId = ? AND Password =?");

    if ($stmt == false)
    {
        die("PREPARE FAILED: " . $conn->connect_error);
    }

    $stmt->bind_param("ss", $userName, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1)
    {
        $userInfo = $result->fetch_assoc();

        $_SESSION['loggedIn'] = true;
        $_SESSION['userID'] = $userInfo['UserID'];
        $_SESSION['userName'] = $userInfo['Name'];

        session_regenerate_id(true);

        $stmt->close();
        $conn->close();
    }
}

// Function to show message to logged in user
function loggedInMsg($userName)
{
    if (isset($userName))
    {
        echo '<p>Welcome <span class="fw-bold">' . $userName . '</span>! (not you? change user <a href="/login">here</a>)</p>';
    }
}
