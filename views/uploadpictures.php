<?php
$userName = $_SESSION['userName'];
$userId = trim($_SESSION['userID']);

$db = Database::getInstance();
$conn = $db->getConnection();

try
{
    $sql = "
        SELECT Album_Id, Title
        FROM Album
        WHERE Owner_Id = ?;
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $userId);
    $stmt->execute();

    $result = $stmt->get_result();

    foreach ($result as $row)
    {
        $selectOptions[$row['Album_Id']] = $row['Title'];
    }
}
catch (PDOException $e)
{
    echo "ERROR: " . $e->getMessage();
}
?>
<div class="container mt-2">
    <h1 class="h1 text-center">Upload Pictures</h1>
    <p>Accepted picture types JPG (JPEG), GIF, and PNG.</p>
    <p>You can upload multiple pictures at a time by pressing the shift key while selecting pictures.</p>
    <p>When uploading multiple pictures, the title and description fields will be applied to all pictures.</p>

    <form name="uploadpictures" id="uploadpictures" method="post">
        <div class="form-group row mb-2">
            <label class="form-label col-sm-3" for="albumname">Upload to Album : </label>
            <select class="form-select col-sm" name="albumname" id="albumname">
                <option selected disabled hidden>Choose Album...</option>
                <?php
                    foreach($selectOptions as $value => $option) {
                        echo '<option value="'. $value. '"> '. $option . '</option>';
                    }
                ?>
            </select>
        </div>
        <div class="form-group row mb-2">
            <label class="form-label col-sm-3" for="picturefile">File to Upload : </label>
            <input type="file" class="form-control col-sm" name="picturefile" id="picturefile">
        </div>
        <div class="form-group row mb-2">
            <label class="form-label col-sm-3" for="title">Title : </label>
            <input class="form-control col-sm" type="text" name="title" id="title">
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