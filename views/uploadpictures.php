<?php
// uploadpictures.php
// Handles picture uploads to albums

// reference: https://inspector.dev/ultimate-guide-to-php-file-upload-security/

// Array for multiple upload is IS confusing. Here's how it is for multiple file uploads
// [name] => Array
//   (
//       [0] => filename.ext
//       [1] => facepalm.jpg
//   )
// [type] => Array
//   (
//       [0] => image/jpeg
//       [1] => image/jpeg
//   )
// [tmp_name] => Array
//   (
//       [0] => /tmp/phpn3FmFr
//       [1] => /tmp/123456
//   )
// [error] => Array
//   (
//       [0] => 4
//       [1] => 
//   )
// [size] => Array
//   (
//       [0] => 15476
//       [1] => 0
//   )

require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/globals.php';

$userName = $_SESSION['loggedName'];
$userId = trim($_SESSION['loggedID']);

// Allowed image types
$allowedTypes = array(
    "jpg" => "image/jpg",
    "jpeg" => "image/jpeg",
    "gif" => "image/gif",
    "png" => "image/png"
);

// Limit to 5MB
$maxAllowedSize = 5 * 1024 * 1024;

// Get albums from the database.
$selectOptions = getAlbums($userId);

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Album name is required
    if (isset($_POST['album']))
    {
        $albumId = $_POST['album'];
        $albumFolder = UPLOADS_FOLDER . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR . $albumId;
    }
    else
    {
        $albumError = "Please select the album to save your picture/s into.";
    }

    // Get title
    if (isset($_POST['title']) and !empty($_POST['title']))
    {
        $cTitle = trim($_POST['title']);
        $cTitle = htmlspecialchars($cTitle);
    }

    // Get description
    if (isset($_POST['description']) and !empty($_POST['description']))
    {
        $cDescription = trim($_POST['description']);
        $cDescription = htmlspecialchars($cDescription);
    }
    // If no title was provided, set it blank.
    else
    {
        $cDescription = "";
    }


    // Check if at least one file is included
    $filesIncluded = false;
    foreach ($_FILES['picturefiles']['error'] as $key => $error)
    {
        if ($error === UPLOAD_ERR_OK and $_FILES['picturefiles']['size'][$key] > 0)
        {
            $filesIncluded = true;
            break;
        }
        else
        {
            $noFile = "No file(s) selected.";
        }
    }

    // Loop through chosen images
    if (isset($albumId) and $filesIncluded === true)
    {
        $files = $_FILES['picturefiles'];
        $fileNames = $files['name'];
        $fileTypes = $files['type'];
        $fileErrors = $files['error'];
        $fileSizes = $files['size'];
        $fileTempNames = $files['tmp_name'];

        $uploadCount = 0;
        $errorsFound = 0;
        $errorMessages = [];

        foreach ($fileNames as $key => $currentFile)
        {
            $msgFirstPart = "Unable to upload " . $currentFile . ". ";

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $currentFileMimeType = finfo_file($finfo, $fileTempNames[$key]);
            $fileExtension = strtolower(pathinfo($currentFile, PATHINFO_EXTENSION));

            // Check the file MIME type
            if ($currentFileMimeType === 'false')
            {
                array_push($errorMessages, $msgFirstPart . $currentFileMimeType . " Cannot validate file type.");
                continue;
            }
            elseif (!in_array($currentFileMimeType, array_values(($allowedTypes))))
            {
                array_push($errorMessages, $msgFirstPart . " Not a valid image file.");
                continue;
            }

            // Check if selected file type
            if (!array_key_exists($fileExtension, $allowedTypes))
            {
                array_push($errorMessages, $msgFirstPart . strtoupper($fileExtension) . " files are not allowed.");
                continue;
            }

            // Check filesize
            if (getimagesize($fileTempNames[$key]) === false)
            {
                array_push($errorMessages, $msgFirstPart . " Not a valid image file or file is corrupted.");
                continue;
            }
            elseif ($fileErrors[$key] === UPLOAD_ERR_FORM_SIZE or $fileErrors[$key] === UPLOAD_ERR_INI_SIZE)
            {
                array_push($errorMessages, $msgFirstPart . " File provided exceeds the maximum allowable upload size.");
                continue;
            }
            elseif ($fileSizes[$key] > $maxAllowedSize)
            {
                array_push($errorMessages, $msgFirstPart . " File size is greater than 5MB");
                continue;
            }

            // Check if the file is not found
            if ($fileErrors[$key] === UPLOAD_ERR_NO_FILE)
            {
                array_push($errorMessages, $msgFirstPart . " File not found.");
                continue;
            }
            // File is valid
            elseif ($fileErrors[$key] === UPLOAD_ERR_OK)
            {
                $file = $fileTempNames[$key];

                // Check if file already exists
                if (file_exists($albumFolder . DIRECTORY_SEPARATOR . $currentFile))
                {
                    array_push($errorMessages, $msgFirstPart . " File already exists.");
                    continue;
                }
                // All is good. Upload
                else
                {
                    // If title is not provided, use the first valid file's filename
                    if (!isset($cTitle) or empty($cTitle))
                    {
                        $cTitle = trim($currentFile);
                        $cTitle = htmlspecialchars($cTitle);
                    }

                    // Upload the actual file
                    move_uploaded_file($file, $albumFolder . DIRECTORY_SEPARATOR . $currentFile);

                    // Add a record to the database
                    addPictures($albumId, $currentFile, $cTitle, $cDescription);
                    // echo $cTitle;

                    $uploadCount += 1;
                }
            }
        }

        if (count($errorMessages) > 0) $errorsFound = count($errorMessages);
    }
}

?>

<div class="container mt-2">
    <h1 class="h1 text-center">Upload Pictures</h1>
    <p>Accepted picture types JPG (JPEG), GIF, and PNG.</p>
    <p>You can upload multiple pictures at a time by pressing the shift key while selecting pictures.</p>
    <p>When uploading multiple pictures, the title and description fields will be applied to all pictures.</p>

    <?php if (isset($errorsFound) and $errorsFound > 0) : ?>
        <div class="container" name="errorContainer">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="h5"><?php echo $errorsFound == 1 ? $errorsFound . " error" : $errorsFound . " errors"   ?> encountered:</h5>
                <hr>
                <ul>
                    <?php foreach ($errorMessages as $message): ?>
                        <li><?php echo $message ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($uploadCount) and $uploadCount > 0) : ?>
        <div class="container" name="successContainer">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <p style="margin:0;"><?php
                                        echo $uploadCount;
                                        echo $uploadCount == 1 ? " file " : " files ";
                                        echo "uploaded successfully."
                                        ?></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <form name="uploadpictures" id="uploadpictures" method="post" enctype="multipart/form-data">
        <div class="form-group row mb-2">
            <div class="col col-sm-2">
                <label class="form-label" for="album">Upload to Album : </label>
            </div>
            <div class="col col-sm">
                <select class="form-select col-sm" name="album" id="album">
                    <option selected disabled hidden>Choose Album...</option>
                    <?php
                    foreach ($selectOptions as $option)
                    {
                        echo '<option value="' . $option['Album_Id'] . '"> ' . $option['Title'] . '</option>';
                    }
                    ?>
                </select>
                <span class="text-danger"><?php echo isset($albumError) ? $albumError : '' ?></span>
            </div>
        </div>

        <div class="form-group row mb-2">
            <div class="col col-sm-2">
                <label class="form-label" for="picturefile">File(s) to Upload : </label>
            </div>
            <div class="col col-sm">
                <input type="file" class="form-control" name="picturefiles[]" id='picturefiles' multiple>
                <span class="text-danger"><?php echo isset($noFile) ? $noFile : '' ?></span>
            </div>
        </div>
        <div class="form-group row mb-2">
            <div class="col col-sm-2">
                <label class="form-label" for="title">Title : </label>
            </div>
            <div class="col col-sm">
                <input class="form-control" type="text" name="title" id="title">
            </div>
        </div>
        <div class="form-group row mb-2">
            <div class="col col-sm-2">
                <label class="form-label" for="description">Description : </label>
            </div>
            <div class="col col-sm">
                <textarea class="form-control col-sm" name="description" id="description" maxlength="3000"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <div class="col-sm">
                <input class="btn btn-primary col-sm-5" type="submit" value="Submit">
                <input class="btn btn-secondary col-sm-5" type="reset" value="Clear">
            </div>
        </div>
    </form>
</div>