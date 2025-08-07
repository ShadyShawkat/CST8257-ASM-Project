<?php
// addalbum.php
// Page handing creating of new album/s 

require_once './includes/functions.php';
require_once BASE_PATH . '/config/database.php';

$userName = $_SESSION['loggedName'];
$userId = trim($_SESSION['loggedID']);

$accessibilityOptions = getAccessibilityOptions();
foreach ($accessibilityOptions as $option)
{
    $selectOptions[$option['Accessibility_Code']] = $option['Description'];
}

if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    $errFound = 0;

    if (isset($_POST['title']) and !empty($_POST['title']))
    {
        $title = htmlspecialchars($_POST['title']);
    }
    else
    {
        $title['error'] = 'Album title is required.';
        $errFound++;
    }

    if (isset($_POST['accessibility']) and !empty($_POST['accessibility']))
    {
        $accessibility = htmlspecialchars($_POST['accessibility']);
    }
    else
    {
        $accessibility['error'] = 'Album accessibility is required';
        $errFound++;

    }
    
    // Per the PDF, description is optional.
    if (isset($_POST['description']) and !empty($_POST['description']))
    {
        $description = htmlspecialchars($_POST['description']);
    }
    else
    {
        $description = "";
    }

    if ($errFound === 0)
    {
        $result = addAlbum($userId, $title, $description, $accessibility);
        if ($result)
        {
            displayMessage('Album created successfully.', 'SUCCESS', 'info');
        }
    }
}

?>

<div class="container mt-2">
    <h1 class="h1 text-center">Create New Album</h1>
    <?php
    // Display message to logged in user
    if (isset($_SESSION['userName']))
    {
        loggedInMsg($_SESSION['userName']);
    }
    ?>

    <form method="post">
        <div class="form-group row mb-2">
            <div class="col col-sm-2">
                <label class="form-label" for="title">Title : </label>
            </div>
            <div class="col col-sm">
                <input class="form-control" type="text" name="title" id="title">
                <span class="text-danger"><?php echo isset($title['error']) ? $title['error'] : '' ?></span>
            </div>
        </div>
        <div class="form-group row mb-2">
            <div class="col col-sm-2">
                <label class="form-label" for="accessibility">Accessibility : </label>
            </div>
            <div class="col col-sm">
                <select class="form-select col-sm" name="accessibility" id="accessibility">
                    <option selected disabled hidden>Choose one...</option>
                    <?php
                    foreach ($selectOptions as $value => $option)
                    {
                        echo '<option value="' . $value . '">' . $option . '</option>';
                    }
                    ?>
                </select>
                <span class="text-danger"><?php echo isset($accessibility['error']) ? $accessibility['error'] : '' ?></span>
            </div>
        </div>
        <div class="form-group row mb-2">
            <div class="col col-sm-2">
                <label class="form-label" for="description">Description : </label>
            </div>
            <div class="col col-sm">
                <textarea class="form-control col-sm" name="description" id="description"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <div class="col col-sm-2"></div>
            <div class="col col-sm">
                <input class="btn btn-primary col-sm-5" type="submit" value="Submit">
                <input class="btn btn-secondary col-sm-5" type="reset" value="Clear">
            </div>
        </div>
    </form>
</div>