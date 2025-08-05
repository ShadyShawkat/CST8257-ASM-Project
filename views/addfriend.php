<?php
// views/addfriend.php
require_once './includes/functions.php';
require_once './config/database.php';

if (!isset($_SESSION['loggedID'])) {
    displayMessage("You must be logged in to send a friend request.");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['friendUserId'])) {
    $currentUserId = $_SESSION['loggedID'];
    $friendUserId = trim($_POST['friendUserId']);

    if ($currentUserId == $friendUserId) {
        $message = "You cannot send a friend request to yourself.";
    } else {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Check if the target user exists
        $stmt = $conn->prepare("SELECT * FROM user WHERE UserId = ?");
        $stmt->execute([$friendUserId]);
        $friendExists = $stmt->fetch();

        if (!$friendExists) {
            $message = "The entered user ID does not exist.";
        } else {
            // Check if they are already friends (in either direction with status 'accepted')
            $sql = "SELECT * FROM Friendship 
                        WHERE ((Friend_RequesterId = ? AND Friend_RequesteeId = ?) 
                        OR (Friend_RequesterId = ? AND Friend_RequesteeId = ?))
                        AND Status = 'accepted'";     
            $stmt = $conn->prepare($sql);
            $stmt->execute([$currentUserId, $friendUserId, $friendUserId, $currentUserId]);

            if ($stmt->fetch()) {
                $message = "You and {$friendUserId} are already friends.";
            } else {
                // Check if there's a pending request FROM the target user TO the current user
                $sql = "SELECT * FROM Friendship 
            WHERE Friend_RequesterId = ? AND Friend_RequesteeId = ? AND Status = 'pending'";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$friendUserId, $currentUserId]);

                if ($stmt->fetch()) {
                    // Accept the request: update the existing entry
                    $sql = "UPDATE Friendship 
                SET Status = 'accepted' 
                WHERE Friend_RequesterId = ? AND Friend_RequesteeId = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$friendUserId, $currentUserId]);

                    $message = "Friend request from {$friendUserId} accepted! You are now friends.";
                } else {
                    // Check if request already sent
                    $sql = "SELECT * FROM Friendship 
                WHERE Friend_RequesterId = ? AND Friend_RequesteeId = ? AND Status = 'pending'";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$currentUserId, $friendUserId]);

                    if ($stmt->fetch()) {
                        $message = "You already sent a friend request to {$friendUserId}.";
                    } else {
                        // Send a new friend request
                        $sql = "INSERT INTO Friendship (Friend_RequesterId, Friend_RequesteeId, Status)
                    VALUES (?, ?, 'pending')";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$currentUserId, $friendUserId]);

                        $message = "Friend request sent to {$friendUserId}.";
                    }
                }
            }

        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body bg-light">
                    <h3 class="card-title text-center">Add Friend</h3>
                    <p class="text-center">
                        Welcome <strong><?= htmlspecialchars($_SESSION['loggedName']) ?></strong>!
                        (not you? <a href="logout.php">change user here</a>)
                    </p>

                    <?php if ($message): ?>
                        <div class="mt-3 <?= $feedbackClass ?>">
                            <?= $message ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="mt-4">
                        <div class="form-group">
                            <label for="friendUserId">ID:</label>
                            <input type="text" class="form-control" id="friendUserId" name="friendUserId" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Send Friend Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>