<?php
// addalbum.php
// Page handing creating of new album/s 

require_once './includes/functions.php';
require_once './config/database.php';

$accessibilityOptions = getAccessibilityOptions();
foreach ($accessibilityOptions as $option)
{
    $selectOptions[$option['Accessibility_Code']] = $option['Description'];
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
            <label class="form-label col-sm-3" for="title">Title : </label>
            <input class="form-control col-sm" type="text" name="title" id="title">
        </div>
        <div class="form-group row mb-2">
            <label class="form-label col-sm-3" for="accessibility">Accessibility : </label>
            <select class="form-select col-sm" name="accessibility" id="accessibility">
                <option selected disabled hidden>Choose one...</option>
                <?php
                foreach ($selectOptions as $value => $option)
                {
                    echo '<option value="' . $value . '">' . $option . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group row mb-2">
            <label class="form-label col-sm-3" for="description">Description : </label>
            <textarea class="form-control col-sm" name="description" id="description"></textarea>
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