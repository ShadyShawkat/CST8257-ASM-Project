<?php
// mypictures.php
// Displays pictures from the user's albums

require_once './includes/functions.php';
require_once './config/database.php';

$userName = $_SESSION['loggedName'];
$userId = trim($_SESSION['loggedID']);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['albumId']))
{
    $albumId = $_GET['albumId'];
    $result = getPictures($albumId);
}

// Experiment
$db = Database::getInstance();
$sql = "SELECT
    Album.Title,
    Album.Description,
    Album.Date_Updated,
    Picture.FileName,
    Picture.Title AS Picture_Title,
    Picture.Description AS Picture_Description,
    Picture.Date_Added,
    Comment.Comment_Text,
    Comment.Date,
    User.Name
FROM
    Album
JOIN
    Picture ON Album.Album_Id = Picture.Album_Id
LEFT JOIN
    Comment ON Picture.Picture_Id = Comment.Picture_Id
LEFT JOIN
    User ON Comment.Author_Id = User.UserId
WHERE
    Album.Owner_Id = ?
ORDER BY
    Album.Title ASC,
    Picture.Title ASC,
    Comment.Date ASC;";

$pictures = $db->run($sql, [$userId])->fetchAll();
// End experiment

$albums = getAlbums($userId);
foreach ($albums as $option)
{
    $albumId = $option['Album_Id'];
    $albumTitle = $option['Title'];
    $dateUpdated = $option['Date_Updated'];
    $selectOptions[$albumId] = $albumTitle . ' - updated on ' . $dateUpdated;
}

?>

<div class="container mt-2">
    <h1 class="h1 text-center">My Pictures</h1>

    <div class="row">
        <div class="col">
            <span name="picturetitle" id="picturetitle">Picture Title Goes Here</span>
            <form method="get" id="picturelist">
                <select class="form-select col-sm" name="albumselect" id="albumselect">
                    <?php
                    foreach ($selectOptions as $value => $option)
                    {
                        echo '<option value="' . $value . '"> ' . $option . '</option>';
                    }
                    ?>
                </select>
            </form>
            <div id="albumContent"></div>
            <div>
                <?php
                foreach ($result as $picture)
                {
                    echo "<p>{$picture['Title']} - {$picture['FileName']}</p>";
                }
                ?>
            </div>
        </div>
        <div class="col">
            <div id="imageDescription">
                Description goes here.
            </div>
            <div style="overflow-y:scroll;">
                Comments area. List of comments go here with a comment box at the bottom
            </div>
            <form method="post">
                <textarea placeholder="Leave a comment..."></textarea>
                <input type="submit" value="Add Comment">
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('albumselect').addEventListener('change', function() {
        const albumId = this.value;

        console.log(albumId);

        fetch('get_album_data.php?album_id=123') // Example for your PHP scenario
            .then(response => {
                if (!response.ok) {
                    console.error('Network response was not ok:', response.status, response.statusText);
                    throw new Error('Server responded with an error.');
                }
                return response.text(); // Parse the response body as plain text
            })
            .then(textData => {
                console.log('--- Text Data Result ---');
                console.log(textData); // Dumps the plain text string
                console.log('------------------------');

                // If it's HTML, you might log it as HTML in the console
                // console.log('HTML content:', textData);
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
    });
</script>