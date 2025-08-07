<?php
require_once './includes/functions.php';
require_once './config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();
$currentUserId = $_SESSION['loggedID'] ?? null;

if (!$currentUserId)
{
    displayMessage("You must be logged in to view this page.");
    exit;
}

// Handle Defriend
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $message = "";

    if (isset($_POST['defriend']) && is_array($_POST['defriend']))
    {
        foreach ($_POST['defriend'] as $friendId)
        {
            $stmt = $conn->prepare("DELETE FROM Friendship 
                                    WHERE (Friend_RequesterId = ? AND Friend_RequesteeId = ?)
                                       OR (Friend_RequesterId = ? AND Friend_RequesteeId = ?)");
            $stmt->execute([$currentUserId, $friendId, $friendId, $currentUserId]);
        }
        $message = "Selected friend(s) were successfully defriended.";
    }

    // var_dump($_POST);

    // Handle Accept
    if (isset($_POST['accept']) && is_array($_POST['friendrequests']))
    {
        foreach ($_POST['accept'] as $fromId)
        {
            // Update existing to accepted
            $conn->prepare("UPDATE Friendship SET Status = 'accepted' WHERE Friend_RequesterId = ? AND Friend_RequesteeId = ?")
                ->execute([$fromId, $currentUserId]);

            // Insert reverse direction
            $conn->prepare("INSERT INTO Friendship (Friend_RequesterId, Friend_RequesteeId, Status) VALUES (?, ?, 'accepted')")
                ->execute([$currentUserId, $fromId]);
        }
        $message = "Selected request(s) accepted.";
    }

    // Handle Deny
    if (isset($_POST['denyTrigger']) && isset($_POST['friendrequests']) and is_array($_POST['friendrequests']))
    {
        foreach ($_POST['deny'] as $fromId)
        {
            $conn->prepare("DELETE FROM Friendship WHERE Friend_RequesterId = ? AND Friend_RequesteeId = ? AND Status = 'pending'")
                ->execute([$fromId, $currentUserId]);
        }
        $message = "Selected request(s) denied.";
    }
}

// Get Friends
// $friendsStmt = $conn->prepare("
//     SELECT u.UserId, u.Name,
//         (SELECT COUNT(*) 
//          FROM Album a 
//          WHERE a.Owner_Id = u.UserId AND a.Accessibility_Code = 'shared') AS SharedAlbums
//     FROM User u
//     JOIN Friendship f ON u.UserId = f.Friend_RequesteeId
//     WHERE f.Friend_RequesterId = ? AND f.Status = 'accepted'
// ");
$friendsStmt = $conn->prepare("
    SELECT u.UserId, u.Name,
        (SELECT COUNT(*)  
            FROM Album a  
            WHERE a.Owner_Id = u.UserId AND a.Accessibility_Code = 'shared') AS SharedAlbums
    FROM User u
    JOIN Friendship f 
        ON (u.UserId = f.Friend_RequesteeId AND f.Friend_RequesterId = ?) 
        OR (u.UserId = f.Friend_RequesterId AND f.Friend_RequesteeId = ?)
    WHERE f.Status = 'accepted'
");
$friendsStmt->execute([$currentUserId, $currentUserId]);
$friends = $friendsStmt->fetchAll();

// Get Friend Requests
$requestsStmt = $conn->prepare("
    SELECT u.UserId, u.Name
    FROM User u
    JOIN Friendship f ON u.UserId = f.Friend_RequesterId
    WHERE f.Friend_RequesteeId = ? AND f.Status = 'pending'
");
$requestsStmt->execute([$currentUserId]);
$requests = $requestsStmt->fetchAll();
?>

<div class="container mt-3">
    <h1 class="text-center">My Friends</h1>
    <?php
    if (isset($_SESSION['loggedName']))
    {
        loggedInMsg($_SESSION['loggedName']);
    }
    if (!empty($message))
    {
        echo "<div class='alert alert-success'>$message</div>";
    }
    ?>
    <form method="post">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h5>Friends:</h5>
            <a href="addfriend" class="btn btn-link">Add Friends</a>
        </div>
        <?php if (count($friends) > 0): ?>
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Shared Albums</th>
                        <th>Defriend</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($friends as $friend): ?>
                        <tr>
                            <td>
                                <?php if ($friend['SharedAlbums'] > 0): ?>
                                    <a href="friendpictures?user=<?php echo $friend['UserId']; ?>">
                                        <?php echo htmlspecialchars($friend['Name']); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($friend['Name']); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $friend['SharedAlbums']; ?></td>
                            <td><input type="checkbox" name="defriend[]" value="<?php echo $friend['UserId']; ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-danger" onclick="return confirm('The selected friend(s) will be defriended. Continue?')">
                Defriend Selected
            </button>
        <?php else: ?>
            <p>No friends yet.</p>
        <?php endif; ?>

        <hr class="my-4">

        <h5>Friend Requests:</h5>
        <?php if (count($requests) > 0): ?>
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Accept or Deny</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['Name']); ?></td>
                            <td><input type="checkbox" name="friendrequests[]" value="<?php echo $request['UserId']; ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Accept Selected</button>
                <button type="submit" name="denyTrigger" value="1" class="btn btn-secondary" onclick="return confirm('The selected friend requests will be denied. Continue?')">Deny Selected</button>
            </div>
        <?php else: ?>
            <p>No pending friend requests.</p>
        <?php endif; ?>
    </form>
</div>