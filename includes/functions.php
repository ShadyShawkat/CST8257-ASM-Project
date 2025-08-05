<?php
// functions.php
// Contains the various functions for the entire website.

// Function to show a friendly error message
function displayMessage(string $message, string $title = 'ERROR', string $type = 'error')
{
    $alertClass = 'alert-light';
    $svg = '';

    switch (strtolower($type))
    {
        case 'error':
            $class = 'alert-danger';
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M479.56-254Q507-254 526-272.56q19-18.56 19-46t-18.56-46.94q-18.56-19.5-46-19.5T434-365.71q-19 19.29-19 46.73 0 27.44 18.56 46.21t46 18.77ZM421-430h118v-263H421v263Zm59.28 368Q393-62 317.01-94.58q-75.98-32.59-132.91-89.52-56.93-56.93-89.52-132.87Q62-392.92 62-480.46t32.58-163.03q32.59-75.48 89.52-132.41 56.93-56.93 132.87-89.52Q392.92-898 480.46-898t163.03 32.58q75.48 32.59 132.41 89.52 56.93 56.93 89.52 132.64Q898-567.55 898-480.28q0 87.28-32.58 163.27-32.59 75.98-89.52 132.91-56.93 56.93-132.64 89.52Q567.55-62 480.28-62Z"/></svg>';
            break;
        case 'info':
        case 'success':
            $class = 'alert-success';
            if (strtolower($title) == strtolower('ERROR')) $title = 'SUCCESS';
            break;
    }

    $html = '<div class="m-2 alert ' . $class . ' alert-dismissible fade show" id="alertMessage" role="alert">
                ' . $svg . '
                <strong>' . $title . ' : </strong>' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';

    echo $html;
}

// Function to log user in
function logIn($userName, $password)
{
    require_once BASE_PATH . '/config/database.php';

    // If needed fields are empty, return an error
    if (!isset($userName) or !isset($password) or empty($userName) or empty($password))
    {
        displayMessage("Username and password required.");
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
                echo password_hash($password, PASSWORD_DEFAULT);
                echo "<br>" . $user['Password'];
                if (password_verify($password, $user['Password']))
                {
                    $_SESSION['loggedID'] = $user['UserID'];
                    $_SESSION['loggedName'] = $user['Name'];
                    $_SESSION['loggedIn'] = true;

                    session_regenerate_id(true);

                    header("Location: home"); // or whatever main page you want
                    exit();
                }
                else
                {
                    return "Invalid username and/or password.";
                }
            }
            else
            {
                return "Invalid username and/or password.";
            }
        }
        catch (PDOException $e)
        {
            displayMessage($e->getMessage());
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

// Function to validate ID entered.
function validateID(string $id)
{
    require_once BASE_PATH . '/config/database.php';

    $db = Database::getInstance();
    // $sql = "SELECT UserID, Name, Password FROM user WHERE UserId = ? LIMIT 1";
    // $user = $db->run($sql, [$safeUserName])->fetch();

    // Check if id is set
    if (!isset($id))
    {
        return ["error" => "User ID is required."];
    }

    $validId = trim($id);
    $validId = htmlspecialchars($validId);

    // Check if the ID already exists in the database
    try
    {
        $sql = "SELECT UserId FROM User WHERE UserId = ?";
        $result = $db->run($sql, [$validId])->fetch();
    }
    catch (PDOException $e)
    {
        echo "ERROR ENCOUNTERED: " . $e->getMessage() . "<br>";
    }

    if (empty($validId))
    {
        return ["error" => "User ID is required."];
    }
    elseif (isset($result['UserId']))
    {
        // If the entered User ID already exists in the system, display an error message next to the Student ID field
        return ["error" => "User ID has already been taken."];
    }
    else
    {
        return ["success" => $validId];
    }
}

// Function to validate name
function validateName(string $name)
{
    $pattern = "/^[a-zA-Z\s'\-.]{2,100}$/";

    // Check if name is set
    if (!isset($name))
    {
        return ["error" => "Name is required."];
    }

    $validName = trim($name);
    $validName = htmlspecialchars($validName);

    if (empty($validName))
    {
        return ["error" => "Name is required."];
    }
    else if (!preg_match($pattern, $validName))
    {
        return ["error" => "Name can only have spaces, hyphens, or apostrophes."];
    }
    else
    {
        return ["success" => $validName];
    }
}

// Phone Number is in the format of nnn-nnn-nnnn
function validatePhone(string $phone)
{
    $pattern = '/^\d{10}$/';

    // Remove the dashes if it already exists
    $validPhone = str_replace('-', '', $phone);
    $validPhone = filter_var($validPhone, FILTER_SANITIZE_NUMBER_INT);

    if (!isset($phone) or empty($validPhone))
    {
        return ["error" => "Phone is required."];
    }
    elseif (!preg_match($pattern, $validPhone))
    {
        return ["error" => "Phone number must be exactly 10 digits."];
    }
    else
    {
        return ["success" => $validPhone];
    }
}

// Password is at least 6 characters long, contains at least one upper case, one lowercase and one digit.
function validatePass(string $password1)
{
    $criteria = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/';

    if (!isset($password1) or empty($password1))
    {
        return ["error" => "Password is required."];
    }
    // Check if the input matches the criteria
    elseif (!preg_match($criteria, $password1))
    {
        return ["error" => "Password should be at least 6 characters long, must have 1 uppercase, and 1 digit."];
    }
    else
    {
        return ["success" => $password1];
    }
}

function confirmPass(string $password1, string $password2)
{
    if (!isset($password2) or empty($password2))
    {
        return ["error" => "Confirm password is required."];
    }
    elseif ($password1 !== $password2)
    {
        return ["error" => "Password and confirm password are not the same."];
    }
}

// Function to add the user to the database and create their folder
function addUser(string $userId, string $name, string $phone, string $password)
{
    require_once BASE_PATH . '/config/database.php';

    $hashedPass = password_hash($password, PASSWORD_DEFAULT);

    try
    {
        $sql = 'INSERT INTO User (UserId, Name, Phone, Password) VALUES (?, ?, ?, ?)';

        $db = Database::getInstance();
        $newUser = $db->run($sql, [$userId, $name, $phone, $hashedPass]);

        if ($newUser->rowCount() > 0)
        {
            // Since there's no profile deletion and IDs are checked before they are craeted, 
            // I did not take into account if there is already a folder of the same name anymore.
            mkdir(UPLOADS_FOLDER . '/' . $userId);
            displayMessage('Profile created successfully.', 'SUCCESS', 'success');
        }
    }
    catch (PDOException $e)
    {
        displayMessage($e->getMessage());
    }
}

// Function to get a specific user by ID
function getUser(string $userId)
{
    require_once BASE_PATH . '/config/database.php';

    try
    {
        $sql = 'SELECT * FROM User WHERE UserId = ? LIMIT 1;';

        $db = Database::getInstance();
        $foundUser = $db->run($sql, [$userId])->fetch(PDO::FETCH_ASSOC);

        if ($foundUser) return $foundUser;
    }
    catch (PDOException $e)
    {
        displayMessage($e->getMessage());
    }
}

// Function to add an album
function addAlbum(string $ownerId, string $title, ?string $description, string $accessibility)
{
    require_once BASE_PATH . '/config/database.php';

    $currentDate = date('Y-m-d');

    // c just means clean
    $cTitle = trim($title);
    $cTitle = htmlspecialchars($cTitle);

    $cDescription = trim($description);
    $cDescription = htmlspecialchars($cDescription);

    try
    {
        $sql = 'INSERT INTO Album (Title, Description, Date_Updated, Owner_Id, Accessibility_Code) VALUES (?, ?, ?, ?, ?)';

        $db = Database::getInstance();
        $newAlbum = $db->run($sql, [$cTitle, $cDescription, $currentDate, $ownerId, $accessibility]);
        $newAlbumId = $db->pdo->lastInsertId();

        // Album created
        if ($newAlbum)
        {
            //check if the uploads folder exists, if not create it
            if (!is_dir(UPLOADS_FOLDER . "/$ownerId")) {
                mkdir(UPLOADS_FOLDER . '/' . $ownerId);
            }
            mkdir(UPLOADS_FOLDER . "/$ownerId/$newAlbumId");
            return $newAlbum;
        }
    }
    catch (PDOException $e)
    {
        displayMessage($e->getMessage());
    }
}

// Function to get albums by user id from the database
function getAlbums($userId)
{
    require_once BASE_PATH . '/config/database.php';

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
        displayMessage($e->getMessage());
    }
}

// Function to delete an album
function deleteAlbum(string $albumId, string $userId)
{
    require_once BASE_PATH . '/config/database.php';

    try
    {
        $db = Database::getInstance();
        $db->pdo->beginTransaction();

        // Get the album name for later
        $sql = 'SELECT Title FROM Album WHERE Album_Id = ?';
        $albumName = $db->run($sql, [$albumId])->fetchColumn();

        // Delete all the comments associated to the pictures in the album first
        $commentSql = 'DELETE FROM Comment
            WHERE Picture_Id IN (
                SELECT Picture_Id
                FROM Picture
                WHERE Album_Id = ?);';
        $db->run($commentSql, [$albumId]);

        // Delete the pictures associated with the album 
        $pictureSql = 'DELETE FROM Picture
            WHERE Album_Id = ?';
        $db->run($pictureSql, [$albumId]);

        $albumSql = 'DELETE FROM Album WHERE Album_Id = ?';
        $deleteAlbum = $db->run($albumSql, [$albumId]);
        $albumCount = $deleteAlbum->rowCount();

        if ($albumCount > 0)
        {
            $db->pdo->commit();

            // Delete the actual files and folder
            $folder = UPLOADS_FOLDER . "/$userId/$albumId";
            $files = scandir($folder);

            // Loop through file contents first and delete
            foreach ($files as $file)
            {
                // Skip dots
                if ($file === '.' || $file === '..')
                {
                    continue;
                }

                $filePath = $folder . "/$file";
                if (is_file($filePath)) unlink($filePath);
            }

            // Remove the folder after
            rmdir($folder);

            return [
                'success' => true,
                'message' => '"' . $albumName . '" and all its pictures has been deleted successfully.',
            ];
        }
        else
        {
            $db->pdo->rollBack();
            return [
                'success' => false,
                'message' => "Failed to delete $albumName. Please try again.",
            ];
        }
    }
    catch (PDOException $e)
    {
        if ($db->pdo->inTransaction())
        {
            $db->pdo->rollBack();
        }
        return $e->getMessage();
    }
}

// Function to get accessibility options
function getAccessibilityOptions()
{
    require_once BASE_PATH . '/config/database.php';

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
            displayMessage("No accessibility options found");
        }
    }
    catch (PDOException $e)
    {
        displayMessage($e->getMessage());
    }
}

// function to update the accessibility option by album id and the new code 
function changeAccessibilityOptions($albumId, $accessibilityCode)
{
    require_once BASE_PATH . '/config/database.php';

    try
    {
        $currentDate = date('Y-m-d');

        $sql = 'UPDATE Album SET Date_Updated = ?, Accessibility_Code = ? WHERE Album_Id = ?';

        $db = Database::getInstance();
        $result = $db->run($sql, [$currentDate, $accessibilityCode, $albumId]);

        // Query successful
        return $result->rowCount();
        // else
        // {
        //     displayMessage($e->getMessage());
        // }
    }
    catch (PDOException $e)
    {
        displayMessage($e->getMessage());
    }
}

// Get pictures based on album id
function getPictures($albumId)
{
    require_once BASE_PATH . '/config/database.php';

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
        displayMessage($e->getMessage());
    }
}

function addPictures(string $albumId, string $fileName, ?string $title, ?string $description)
{
    require_once BASE_PATH . '/config/database.php';

    $currentDate = date('Y-m-d');

    $cTitle = trim($title);
    $cTitle = htmlspecialchars($cTitle);

    $cDescription = trim($description);
    $cDescription = htmlspecialchars($cDescription);

    try
    {
        $sql = 'INSERT INTO Picture (Album_Id, FileName, Title, Description, Date_Added) VALUES (?,?,?,?,?);';
        $db = Database::getInstance();
        $picturesAdded = $db->run($sql, [$albumId, $fileName, $cTitle, $cDescription, $currentDate]);
    }
    catch (PDOException $e)
    {
        displayMessage($e->getMessage());
    }
}

function getComments(string $pictureId)
{
    require_once BASE_PATH . '/config/database.php';

    try
    {
        $sql = "SELECT 
                C.*, U.Name AS Author_Name,
                DATE_FORMAT(C.Date, '%Y-%m-%d') AS Comment_Date 
            FROM Comment AS C
            JOIN User AS U ON C.Author_Id = U.UserId 
            WHERE C.Picture_Id = ?
            ORDER BY C.Date DESC;";

        $db = Database::getInstance();
        $comments = $db->run($sql, [$pictureId])->fetchAll(PDO::FETCH_ASSOC);

        // Query successful
        if ($comments)
        {
            return $comments;
        }
        else
        {
            return [];
        }
    }
    catch (PDOException $e)
    {
        displayMessage($e->getMessage());
        return [];
    }
}

function addComment(string $authorId, string $pictureId, string $comment)
{
    require_once BASE_PATH . '/config/database.php';

    $cComment = trim($comment);
    $cComment = htmlspecialchars($cComment);

    try
    {
        $sql = 'INSERT INTO Comment (Author_Id, Picture_Id, Comment_Text) VALUES (?, ?, ?);';
        $db = Database::getInstance();
        $commentAdded = $db->run($sql, [$authorId, $pictureId, $cComment]);

        if($commentAdded) 
        { 
            return $commentAdded;
        }
    }
    catch (PDOException $e)
    {
        displayMessage($e->getMessage());
    }
}

function getSharedAlbums($userId)
{
    require_once BASE_PATH . '/config/database.php';

    try {
        $sql = 'SELECT Album.Album_Id, Album.Title, Album.Date_Updated
                FROM Album
                WHERE Owner_Id = ? AND Accessibility_Code = "shared"
                ORDER BY Album.Date_Updated DESC';

        $db = Database::getInstance();
        $albums = $db->run($sql, [$userId])->fetchAll(PDO::FETCH_ASSOC);
        return $albums ?: [];
    } catch (PDOException $e) {
        displayMessage($e->getMessage());
    }
}

