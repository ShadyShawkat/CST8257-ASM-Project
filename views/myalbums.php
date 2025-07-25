<?php
// myalbums.php
// Handles the display of albums

require_once './includes/functions.php';
require_once './config/database.php';

$userName = $_SESSION['loggedName'];
$userId = trim($_SESSION['loggedID']);

// Get all albums
$albums = getAlbums($userId);

foreach ($albums as $album)
{
    $currentAlbumsInfo[$album['Album_Id']] = $album['Accessibility_Code'];
}

$accessibilityOptions = getAccessibilityOptions();
foreach ($accessibilityOptions as $option) $selectOptions[$option['Accessibility_Code']] = $option['Description'];

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
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
                changeAccessibilityOptions($albumId, $newAlbumsInfo[$albumId]);
            }
        }
    }



    // foreach($newAlbumInfo as $albumInfo) {
    //     var_dump($albumInfo);
    // }

    // foreach ($albumIds as $key => $value)
    // {
    //     if(array_key_exists($key, $currentAlbumsInfo)) {
    //         if ($currentAlbumsInfo[$value] !== $albumAccessCodes[$key]) {
    //             changeAccessibilityOptions($value, $albumAccessCodes[$key]);
    //         }
    //     }   
    // }
}

?>

<div class="container mt-2">
    <h1 class="h1 text-center">My Albums</h1>

    <?php
    // Display message to logged in user
    if (isset($userName)) loggedInMsg($userName);
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
                            $albumId = $albums[$row]['Album_Id'];
                            continue;
                        }

                        if ($key == 'Title')
                        {
                            echo "<td><a href='mypictures?albumId=" . $albumId . "'>";
                            echo $value;
                            echo "</a>";
                            echo "<input type='hidden' name='albumId[]' value='$albumId'";
                            echo "</td>";
                        }

                        if ($key == 'Date_Updated')
                        {
                            echo "<td>$value</td>";
                        }

                        if ($key == 'Number_Of_Pictures')
                        {
                            echo "<td>$value</td>";
                        }

                        // // /echo "<td>$value</td>";
                        if ($key == 'Accessibility_Code')
                        {
                            echo "<td><select name='accessibilityCode[]' class='form-select'>";
                            foreach ($selectOptions as $code => $description)
                            {
                                $selected = ($code == $value) ? 'selected' : '';
                                echo "<option value='$code' $selected>$description</option>";
                            }
                            echo "</select></td>";
                        }
                    }

                    echo "<td>Delete</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="row">
            <div class="col">
                <span class="text-">Message here</span>
            </div>
            <div class="form-group col">
                <input type="submit" class="btn btn-primary w-75" value="Save Changes">
            </div>
        </div>
    </form>
</div>