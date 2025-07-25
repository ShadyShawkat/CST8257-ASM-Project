<?php
// functions.php
// Contains the various functions for the entire website.

// Function to show a friendly error message
function displayError(string $message, string $title = 'ERROR')
{
    $html = '<div class="m-2 alert alert-danger alert-dismissible fade show" id="errMessage" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M479.56-254Q507-254 526-272.56q19-18.56 19-46t-18.56-46.94q-18.56-19.5-46-19.5T434-365.71q-19 19.29-19 46.73 0 27.44 18.56 46.21t46 18.77ZM421-430h118v-263H421v263Zm59.28 368Q393-62 317.01-94.58q-75.98-32.59-132.91-89.52-56.93-56.93-89.52-132.87Q62-392.92 62-480.46t32.58-163.03q32.59-75.48 89.52-132.41 56.93-56.93 132.87-89.52Q392.92-898 480.46-898t163.03 32.58q75.48 32.59 132.41 89.52 56.93 56.93 89.52 132.64Q898-567.55 898-480.28q0 87.28-32.58 163.27-32.59 75.98-89.52 132.91-56.93 56.93-132.64 89.52Q567.55-62 480.28-62Z"/></svg>
                <strong>' . $title . ' : </strong>' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    echo $html;
}

// Function to log user in
function logIn($userName, $password)
{
    require_once './config/database.php';

    // If needed fields are empty, return an error
    if (!isset($userName) or !isset($password) or empty($userName) or empty($password))
    {
        displayError("Username and password required.");
    }
    else
    {
        // Parse username
        $safeUserName = htmlspecialchars($userName);

        try
        {
            $db = Database::getInstance();
            $sql = "SELECT UserID, Name, Password FROM user WHERE UserId = ? LIMIT 1";
            $user = $db->run($sql, [$safeUserName])->fetch();

            // User found
            if ($user)
            {
                if (password_verify($password, $user['Password']))
                {
                    $_SESSION['loggedID'] = $user['UserID'];
                    $_SESSION['loggedName'] = $user['Name'];
                    $_SESSION['loggedIn'] = true;

                    session_regenerate_id(true);
                }
                else
                {
                    displayError("Invalid username and/or password.");
                }
            }
            else
            {
                displayError("Invalid username and/or password.");
            }
        }
        catch (PDOException $e)
        {
            displayError($e->getMessage());
        }
    }
}

// Function to show message to logged in user
function loggedInMsg($userName)
{
    if (isset($userName))
    {
        echo '<p>Welcome <span class="fw-bold">' . $userName . '</span>! (not you? change user <a href="login">here</a>)</p>';
    }
}

// Function to get albums by user id from the database
function getAlbums($userId)
{
    require_once './config/database.php';

    try
    {
        $sql = 'SELECT
                Album.Album_Id,
                Album.Title,
                Album.Date_Updated,
                COUNT(Picture.Picture_Id) AS Number_Of_Pictures,
                Album.Accessibility_Code AS Accessibility_Code
            FROM Album
            LEFT JOIN Picture ON Album.Album_Id = Picture.Album_Id
            JOIN Accessibility ON Album.Accessibility_Code = Accessibility.Accessibility_Code
            WHERE Album.Owner_Id = ?
            GROUP BY Album.Album_Id, Album.Title, Album.Date_Updated, Accessibility.Description;';


        $db = Database::getInstance();
        // $sql = "SELECT UserID, Name, Password FROM user WHERE UserId = ? LIMIT 1";
        $album = $db->run($sql, [$userId])->fetchAll();

        // Album found
        if ($album)
        {
            return $album;
        }
    }
    catch (PDOException $e)
    {
        displayError($e->getMessage());
    }
}

function changeAccessibilityOptions($albumId, $option)
{
    require_once './config/database.php';

    try
    {
        $sql = 'UPDATE Album SET Accessibility_Code = ? WHERE Album_Id = ?';

        $db = Database::getInstance();
        $result = $db->run($sql, [$option, $albumId])->fetchAll();

        // Query successful
        return $result;
        // else
        // {
        //     displayError($e->getMessage());
        // }
    }
    catch (PDOException $e)
    {
        displayError($e->getMessage());
    }
}

// Function to get accessibility options
function getAccessibilityOptions()
{
    require_once './config/database.php';

    try
    {
        $sql = 'SELECT Accessibility_Code, Description FROM Accessibility';

        $db = Database::getInstance();
        $accessibilityOptions = $db->run($sql)->fetchAll(PDO::FETCH_ASSOC);

        // Query successful
        if ($accessibilityOptions)
        {
            return $accessibilityOptions;
        }
        else
        {
            displayError("No accessibility options found");
        }
    }
    catch (PDOException $e)
    {
        displayError($e->getMessage());
    }
}

// Get pictures based on album id
function getPictures($albumId)
{
    require_once './config/database.php';

    try
    {
        $sql = 'SELECT * FROM Picture WHERE Album_Id = ?';

        $db = Database::getInstance();
        $albums = $db->run($sql, [$albumId])->fetchAll(PDO::FETCH_ASSOC);

        // Query successful
        if ($albums)
        {
            return $albums;
        }
        else
        {
            return "No pictures found.";
        }
    }
    catch (PDOException $e)
    {
        displayError($e->getMessage());
    }
}

function getComments($pictureId)
{
    require_once './config/database.php';

    try
    {
        $sql = 'SELECT * FROM Comment WHERE Picture_Id = ?';

        $db = Database::getInstance();
        $comments = $db->run($sql, [$pictureId])->fetchAll(PDO::FETCH_ASSOC);

        // Query successful
        if ($comments)
        {
            return $comments;
        }
        else
        {
            return "No comments found.";
        }
    }
    catch (PDOException $e)
    {
        return "Error encountered: " . $e;
    }
}
