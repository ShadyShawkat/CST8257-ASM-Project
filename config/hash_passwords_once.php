<?php
require_once __DIR__ . '/../config/database.php'; // adjust path as needed

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Check if the flag exists
    $stmt = $conn->prepare("SELECT Value FROM SetupFlags WHERE Flag = 'PasswordsHashed'");
    $stmt->execute();
    $flagSet = $stmt->fetchColumn();

    if ($flagSet) {
        // Passwords already hashed
        return;
    }

    // Get all users and their passwords
    $users = $db->run("SELECT UserId, Password FROM User")->fetchAll();

    // Prepare update statement
    $updateStmt = $conn->prepare("UPDATE User SET Password = ? WHERE UserId = ?");

    foreach ($users as $user) {
        $hashed = password_hash($user['Password'], PASSWORD_DEFAULT);
        $updateStmt->execute([$hashed, $user['UserId']]);
    }

    // Insert the flag so it won't run again
    $db->run("INSERT INTO SetupFlags (Flag, Value) VALUES (?, ?)", ['PasswordsHashed', true]);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
