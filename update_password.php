<?php
// Include database configuration
require_once 'config/database.php';
require_once 'classes/Database.php';

// Create a database connection
$db = new Database();
$conn = $db->getConnection();

if ($conn === null) {
    die("Database connection failed");
}

// Generate a password hash for 'user123'
$password = 'user123';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "Generated hash: " . $hashedPassword . "\n";

// Update all users with the new password hash
$stmt = $conn->prepare("UPDATE users SET password = ?");
$stmt->execute([$hashedPassword]);

echo "Updated " . $stmt->rowCount() . " users with the new password hash\n";

// Verify the update
$stmt = $conn->prepare("SELECT id, username, email, password FROM users LIMIT 1");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "User: " . json_encode($user) . "\n";

// Verify the password
$verified = password_verify($password, $user['password']);
echo "Password verification: " . ($verified ? "Success" : "Failed") . "\n";
?>