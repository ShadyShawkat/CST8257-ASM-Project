<?php
// mypictures.php
// Displays pictures from the user's albums

require_once './includes/functions.php';
require_once BASE_PATH . '/config/database.php';

$userName = $_SESSION['loggedName'];
$userId = trim($_SESSION['loggedID']);

$commentError = "";
$commentSuccess = "";
$commentList = "";

// placeholder image
$placeholder = BASE_URL . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'Placeholder.svg';

// Get all the current user's albums for the dropdown
$albums = getAlbums($userId);

if (isset($albums) and !is_null($albums))
{
    // Load the select
    foreach ($albums as $option)
    {
        $albumId = $option['Album_Id'];
        $albumTitle = $option['Title'];
        $dateUpdated = $option['Date_Updated'];
        $selectOptions[$albumId] = $albumTitle . ' - updated on ' . $dateUpdated;
    }

    // If there is a albumId from url get it
    if (isset($_GET['albumId']))
    {
        $getAlbum = trim($_GET['albumId']);

        foreach ($albums as $album)
        {
            if ($album['Album_Id'] == $getAlbum)
            {
                $albumDisplay = $album;
                break;
            }
        }
    }
    // Otherwise just get use  first album
    else
    {
        $albumDisplay = $albums[0];
    }

    // Load the album and first picture
    $picturesList = getPictures($albumDisplay['Album_Id']);

    if (is_array($picturesList))
    {
        // var_dump($picturesList);
        $pictureId = $picturesList[0]['Picture_Id'];
        $pictureTitle = $picturesList[0]['Title'];
        $pictureDescription = $picturesList[0]['Description'];
        $mainImage = '.' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR . $picturesList[0]['Album_Id'] . DIRECTORY_SEPARATOR . $picturesList[0]['FileName'];

        $commentsList = getComments($pictureId);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (empty($_POST['pictureId']))
    {
        $commentError = "You cannot comment on a non-existing picture.";
    }
    elseif (empty($_POST['commentArea']))
    {
        $commentError = "Comment is blank.";
    }
    else
    {
        $pictureId = $_POST['pictureId'];
        $comment = $_POST['commentArea'];

        // $addedComment = addComment($userId, $pictureId, $comment);
        $commentSuccess = "Comment added.";

        header('Location: mypictures');
    }
}

?>

<div class="container mt-2">
    <h1 class="h1 text-center">My Pictures</h1>

    <div class="row">
        <div class="col-8">
            <form name="picturedropdown" id="picturedropdown" method="get">
                <?php if (isset($albums) and !is_null($albums)): ?>
                    <select class="form-select col-sm" name="albumselect" id="albumselect">
                        <?php
                        $firstOption = true;
                        foreach ($selectOptions as $value => $option)
                        {
                            if ((isset($_GET['albumId']) and $_GET['albumId'] == $value) or $firstOption === true)
                            {
                                $firstOption = false;
                                $selected = 'selected';
                            }
                            else
                            {
                                $selected = '';
                            }
                            echo '<option value="' . $value . '" ' . $selected . '> ' . $option . '</option>';
                        }
                        ?>
                    </select>
                <?php else: ?>
                    <div>No albums found. To create an album go <a href="addalbum">here</a>.</div>
                <?php endif; ?>
            </form>
            <h3 class="h3 text-center my-2" name="picturetitle" id="picturetitle"><?php echo isset($pictureTitle) ? $pictureTitle : " "; ?></h3>
        </div>
    </div>
    <div class="row">
        <div class="col col-8" id="pictureGallery" name="pictureGallery">
            <?php if (isset($albums) and !is_null($albums)) : ?>
                <div class="row" id="pictureMain" name="pictureMain">
                    <img id="mainImage" src="<?php echo isset($mainImage) ? $mainImage : $placeholder; ?>" data-id="<?php echo isset($pictureId) ? $pictureId : ''; ?>">
                </div>
                <div class="row thumbnail-container" id="picturethumbs" name="picturethumbs">
                    <ul>
                        <?php if (isset($picturesList) and is_array($picturesList)) :
                            foreach ($picturesList as $picture) :
                                $pictureSource = '.' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR . $picture['Album_Id'] . DIRECTORY_SEPARATOR . $picture['FileName'];
                        ?>
                                <li><img class="thumbnails" id="<?php echo $picture['Picture_Id'] ?>" src="<?php echo $pictureSource ?>" data-fullsize="<?php echo $pictureSource ?>"></li>
                        <?php endforeach;
                        endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-4" id="rCol" name="rCol">
            <?php if (isset($albums) and !is_null($albums)): ?>
                <div class="mb-2">
                    <p class="fw-bold">Description: </p>
                    <div id="pictureDescription">
                        <p><?php echo (isset($pictureDescription) and !empty($pictureDescription)) ? $pictureDescription : "No description set."; ?></p>
                    </div>
                </div>
                <div class="mb-2">
                    <p class="fw-bold">Comments: </p>
                    <div id="comments">
                        <ul class="comment-list">
                            <?php
                            // var_dump($commentsList);
                            if (isset($commentsList) and is_array($commentsList) and !empty($commentsList)) :
                                foreach ($commentsList as $comment) : ?>
                                    <li class="mb-1"><?php echo "<span class='fst-italic'>" . $comment['Author_Name'] . " (" . $comment['Comment_Date'] . ")</span>: " . $comment['Comment_Text']; ?></li>
                            <?php endforeach;
                            else: echo "<li>No comment found.</li>";
                            endif; ?>
                        </ul>
                    </div>
                </div>

                <form id="pictureComment" name="pictureComment" method="post">
                    <div class="form-group">
                        <textarea name="commentArea" id="commentArea" rows="5" class="form-control mb-2" placeholder="Leave a comment..."></textarea>
                        <span id="statusMsg" name="statusMsg" class="text-danger"><?php echo isset($commentError) ? $commentError : '' ?></span>
                        <span class="text-success"><?php echo isset($commentSuccess) ? $commentSuccess : '' ?></span>
                    </div>
                    <input type="hidden" name="pictureId" id="pictureId" value="<?php echo isset($pictureId) ? $pictureId : ''; ?>">
                    <input class="btn btn-primary mt-2 w-100" type="submit" value="Add Comment">
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>