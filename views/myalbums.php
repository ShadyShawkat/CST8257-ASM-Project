<?php
// myalbums.php
// Handles the display of albums

require_once './includes/functions.php';
require_once BASE_PATH . '/config/database.php';

$userName = $_SESSION['loggedName'];
$userId = trim($_SESSION['loggedID']);


// Get all albums
$albums = getAlbums($userId);

if (isset($albums) and !is_null($albums))
{
    foreach ($albums as $album)
    {
        $currentAlbumsInfo[$album['Album_Id']] = $album['Accessibility_Code'];
    }

    $accessibilityOptions = getAccessibilityOptions();
    foreach ($accessibilityOptions as $option) $selectOptions[$option['Accessibility_Code']] = $option['Description'];

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $warningMsg = "";
        $infoMsg = "";

        $optionChanged = 0;

        foreach ($_POST['albumId'] as $key => $value)
        {
            $newAlbumsInfo[$value] = $_POST['accessibilityCode'][$key];
        }

        foreach ($currentAlbumsInfo as $albumId => $accessCode)
        {
            if (array_key_exists($albumId, $newAlbumsInfo))
            {
                if ($currentAlbumsInfo[$albumId] !== $newAlbumsInfo[$albumId])
                {
                    // Save the new accessbility code
                    changeAccessibilityOptions($albumId, $newAlbumsInfo[$albumId]);
                    $optionChanged += 1;
                }
            }
        }

        // Get the albums again if there was a change.
        if ($optionChanged > 0)
        {
            $optionChanged === 1 ? $wordForm = "album" : $wordForm = "albums";

            $infoMsg = "Accessibility on $optionChanged $wordForm has been updated.";
            $warningMsg = "";
            $albums = getAlbums($userId);
        }
        else
        {
            $infoMsg = "";
            $warningMsg = "No change made.";
        }
    }
}

?>

<div class="container mt-2">
    <h1 class="h1 text-center">My Albums</h1>

    <?php
    // Display message to logged in user
    if (isset($userName)) loggedInMsg($userName);
    ?>

    <form method="post" id="albumList">
        <table class="table table-hover">
            <thead>
                <th>Title</th>
                <th>Date Updated</th>
                <th>Number of Pictures</th>
                <th>Accessibility</th>
                <th><a class="btn btn-outline-secondary w-100" href="addalbum">Create a New Album</a></th>
            </thead>
            <tbody>
                <?php
                if (isset($albums) and !is_null($albums))
                {
                    $cols = count($albums[0]);

                    for ($row = 0; $row < count($albums); $row++)
                    {
                        echo "<tr>";
                        foreach ($albums[$row] as $key => $value)
                        {
                            switch ($key)
                            {
                                case 'Album_Id':
                                    $albumId = $value;
                                    break;

                                case 'Title':
                                    $albumName = $value;
                                    echo "<td><a href='mypictures?albumId=$albumId'>";
                                    echo $value;
                                    echo "</a>";
                                    echo "<input type='hidden' name='albumId[]' value='$albumId'>";
                                    echo "</td>";
                                    break;

                                case 'Date_Updated':
                                case 'Number_Of_Pictures':
                                    echo "<td>$value</td>";
                                    break;

                                case 'Accessibility_Code':
                                    echo "<td><select name='accessibilityCode[]' id='accessibilityCode' class='form-select'>";
                                    foreach ($selectOptions as $code => $description)
                                    {
                                        $selected = ($code == $value) ? 'selected' : '';
                                        echo "<option value='$code' $selected>$description</option>";
                                    }
                                    echo "</select></td>";
                                    break;
                            }
                        }
                        echo "<td><button type='button' class='btn btn-outline-danger w-100' data-album-id='$albumId'>Delete</button></td>";
                        echo "</tr>";
                    }
                }
                else {
                    echo '<tr><td class="text-center" colspan="5">No albums found. Click <a href="addalbum">here</a> to add an album.</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <div class="row">
            <div class="col-6">
                <span class="text-success" id="infoSpan"><?php if (isset($infoMsg) and !empty($infoMsg)) echo $infoMsg; ?></span>
                <span class="text-warning" id="warningSpan"><?php if (isset($warningMsg) and !empty($warningMsg)) echo $warningMsg; ?></span>
            </div>
            <div class="form-group col">
                <?php if (isset($albums) and !is_null($albums)) : ?>
                    <input type="submit" class="btn btn-primary w-100" value="Save Changes">
                <?php endif; ?>
            </div>
            <div class="col-3"></div>
        </div>
    </form>
</div>