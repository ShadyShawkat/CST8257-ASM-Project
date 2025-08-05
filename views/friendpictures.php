<?php
// friendpictures.php
require_once './includes/functions.php';
require_once BASE_PATH . '/config/database.php';

$loggedUserId = $_SESSION['loggedID'];
$loggedUserName = $_SESSION['loggedName'];

$commentError = "";
$commentSuccess = "";
$placeholder = BASE_URL . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'Placeholder.svg';

// Validate `user` param from URL
if (!isset($_GET['user']) || empty($_GET['user'])) {
    displayMessage("No friend specified.");
    exit;
}
$friendId = trim($_GET['user']);

// Get the friend's albums that are shared
$albums = getSharedAlbums($friendId);

if ($albums) {
    foreach ($albums as $option) {
        $albumId = $option['Album_Id'];
        $albumTitle = $option['Title'];
        $dateUpdated = $option['Date_Updated'];
        $selectOptions[$albumId] = $albumTitle . ' - updated on ' . $dateUpdated;
    }

    if (isset($_GET['albumId'])) {
        $getAlbum = trim($_GET['albumId']);
        foreach ($albums as $album) {
            if ($album['Album_Id'] == $getAlbum) {
                $albumDisplay = $album;
                break;
            }
        }
    } else {
        $albumDisplay = $albums[0];
    }

    $picturesList = getPictures($albumDisplay['Album_Id']);

    if (is_array($picturesList) && count($picturesList) > 0) {
        $pictureId = $picturesList[0]['Picture_Id'];
        $pictureTitle = $picturesList[0]['Title'];
        $pictureDescription = $picturesList[0]['Description'];
        $mainImage = '..' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $friendId . DIRECTORY_SEPARATOR . $picturesList[0]['Album_Id'] . DIRECTORY_SEPARATOR . $picturesList[0]['FileName'];
        $commentsList = getComments($pictureId);
    }
} else {
    $albums = [];
    displayMessage("This friend has no shared albums.", "NOTICE", "info");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['pictureId'])) {
        $commentError = "You cannot comment on a non-existing picture.";
    } elseif (empty($_POST['commentArea'])) {
        $commentError = "Comment is blank.";
    } else {
        $pictureId = $_POST['pictureId'];
        $comment = $_POST['commentArea'];
        addComment($loggedUserId, $pictureId, $comment);
        $commentSuccess = "Comment added.";
        header("Location: friendpictures?user={$friendId}&albumId={$albumDisplay['Album_Id']}");
        exit;
    }
}
?>

<div class="container mt-2">
    <h1 class="h1 text-center"><?php echo htmlspecialchars(getUser($friendId)['Name']) . "'s Shared Pictures"; ?></h1>

    <div class="row">
        <div class="col-8">
            <form name="picturedropdown" id="picturedropdown" method="get">
                <input type="hidden" name="user" value="<?php echo $friendId; ?>">
                <select class="form-select col-sm" name="albumId" id="albumselect" onchange="this.form.submit()">
                    <?php
                    if (!empty($selectOptions)) {
                        foreach ($selectOptions as $value => $option) {
                            $selected = (isset($_GET['albumId']) && $_GET['albumId'] == $value) ? 'selected' : '';
                            echo '<option value="' . $value . '" ' . $selected . '>' . $option . '</option>';
                        }
                    } else {
                        echo "<option hidden disabled selected>No shared albums available</option>";
                    }
                    ?>
                </select>
            </form>
            <h3 class="h3 text-center my-2"><?php echo isset($pictureTitle) ? $pictureTitle : " "; ?></h3>
        </div>
    </div>

    <div class="row">
        <div class="col-8">
            <?php if (!empty($albums)): ?>
                <div class="row">
                    <img id="mainImage" src="<?php echo isset($mainImage) ? $mainImage : $placeholder; ?>" data-id="<?php echo $pictureId ?? ''; ?>">
                </div>
                <div class="row">
                    <ul>
                        <?php foreach ($picturesList as $picture): 
                            $thumbPath = '..' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $friendId . DIRECTORY_SEPARATOR . $picture['Album_Id'] . DIRECTORY_SEPARATOR . $picture['FileName'];
                        ?>
                            <li>
                                <img class="thumbnails" id="<?php echo $picture['Picture_Id'] ?>" src="<?php echo $thumbPath ?>" data-fullsize="<?php echo $thumbPath ?>">
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-4">
            <?php if (!empty($albums)): ?>
                <div class="mb-2">
                    <p class="fw-bold">Description: </p>
                    <p><?php echo $pictureDescription ?? "No description set."; ?></p>
                </div>
                <div class="mb-2">
                    <p class="fw-bold">Comments: </p>
                    <ul class="comment-list">
                        <?php if (!empty($commentsList) && is_array($commentsList)): 
                            foreach ($commentsList as $comment): ?>
                                <li class="mb-1">
                                    <span class="fst-italic"><?php echo $comment['Author_Name'] . ' (' . $comment['Comment_Date'] . '):'; ?></span>
                                    <?php echo ' ' . $comment['Comment_Text']; ?>
                                </li>
                        <?php endforeach;
                        else: echo "<li>No comments yet.</li>"; endif; ?>
                    </ul>
                </div>

                <form method="post">
                    <textarea name="commentArea" rows="5" class="form-control mb-2" placeholder="Leave a comment..."></textarea>
                    <span class="text-danger"><?php echo $commentError; ?></span>
                    <span class="text-success"><?php echo $commentSuccess; ?></span>
                    <input type="hidden" name="pictureId" value="<?php echo $pictureId ?? ''; ?>">
                    <input class="btn btn-primary mt-2 w-100" type="submit" value="Add Comment">
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
