<?php
// fuctions.php
// Contains the various functions for the entire website.

// Function to log user in
function logIn($userName, $password)
{
    require_once './config/database.php';

    // If needed fields are empty, return an error
    if (!isset($userName) or !isset($password) or empty($userName) or empty($password))
    {
        return ["error" => "Username and password required."];
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
                    return ["error" => "Invalid username and/or password."];
                }
            }
            else
            {
                return ["error" => "Invalid username and/or password."];
            }
        }
        catch (PDOException $e)
        {
            return "Error encountered: " . $e;
        }
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

// Working on this
function getAlbumZZ($userId)
{
    require_once './config/database.php';

    try
    {
        $sql = "
            SELECT
                Album.Album_Id,
                Album.Title,
                Album.Date_Updated,
                COUNT(Picture.Picture_Id) AS Number_Of_Pictures,
                Album.Accessibility_Code AS Accessibility_Code
            FROM Album
            LEFT JOIN Picture ON Album.Album_Id = Picture.Album_Id
            JOIN Accessibility ON Album.Accessibility_Code = Accessibility.Accessibility_Code
            WHERE Album.Owner_Id = ?
            GROUP BY Album.Album_Id, Album.Title, Album.Date_Updated, Accessibility.Description;
        ";


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
        return "Error encountered: " . $e;
    }
}


function getAlbums($userId)
{
    require_once './config/database.php';

    try
    {
        $sql = "SELECT * FROM Album WHERE Album.Owner_ID = ?";

        $db = Database::getInstance();
        $albums = $db->run($sql, [$userId])->fetchAll();

        // Query successful
        if ($albums)
        {
            return $albums;
        }
        else
        {
            return "No album found.";
        }
    }
    catch (PDOException $e)
    {
        return "Error encountered: " . $e;
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
            return "No accessibility options found.";
        }
    }
    catch (PDOException $e)
    {
        return "Error encountered: " . $e;
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
        return "Error encountered: " . $e;
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