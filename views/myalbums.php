<?php
// myalbums.php
// Handles the display of albums

require_once './includes/functions.php';
require_once './config/database.php';

$userName = $_SESSION['loggedName'];
$userId = trim($_SESSION['loggedID']);

$albums = getAlbumZZ($userId);

var_dump($albums);

$accessibilityOptions = getAccessibilityOptions();
foreach ($accessibilityOptions as $option)
{
    $selectOptions[$option['Accessibility_Code']] = $option['Description'];
}

?>

<div class="container mt-2">
    <h1 class="h1 text-center">My Albums</h1>

    <?php
    // Display message to logged in user
    if (isset($userName))
    {
        loggedInMsg($userName);
    }

    ?>

    <a href="/addalbum">Create a New Album</a>
    <form method="post">
        <table class="table">
            <thead>
                <th>Title</th>
                <th>Date Updated</th>
                <th>Number of Pictures</th>
                <th>Accessibility</th>
                <th></th>
            </thead>
            <tbody>
                <?php
                $cols = count($albums[0]);

                for ($row = 0; $row < count($albums); $row++)
                {
                    echo "<tr>";

                    foreach ($albums[$row] as $key => $value)
                    {
                        if ($key == 'Album_Id')
                        {
                            continue;
                        }

                        if ($key == 'Title') {
                            echo "<td><a href='mypictures?albumId=" . $albums[$row]['Album_Id'] . "'>";
                            echo htmlspecialchars($value);
                            echo "</a></td>";
                        }

                        // /echo "<td>$value</td>";
                        if ($key == 'Accessibility_Code')
                        {
                            echo "<td><select class='form-select'>";
                            foreach ($selectOptions as $code => $description)
                            {
                                $selected = ($code == $value) ? 'selected' : '';
                                echo "<option value='$code' $selected>$description</option>";
                            }
                            echo "</select></td>";
                        }
                        else
                        {
                            echo "<td>$value</td>";
                        }
                    }

                    echo "<td>Delete</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Save Changes">
        </div>
    </form>
</div>