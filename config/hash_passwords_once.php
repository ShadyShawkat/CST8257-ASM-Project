<?php
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

try {

    $stmt = $conn->prepare("SELECT Value FROM SetupFlags WHERE Flag = 'PasswordsHashed'");
    $stmt->execute();
    $flagSet = $stmt->fetchColumn();

    if ($flagSet) {
        return;
    }
    $users = $db->run("SELECT UserId, Password FROM User")->fetchAll();
    $updateStmt = $conn->prepare("UPDATE User SET Password = ? WHERE UserId = ?");

    foreach ($users as $user) {
        $hashed = password_hash($user['Password'], PASSWORD_DEFAULT);
        $updateStmt->execute([$hashed, $user['UserId']]);
    }

    $db->run("INSERT INTO SetupFlags (Flag, Value) VALUES (?, ?)", ['PasswordsHashed', true]);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
