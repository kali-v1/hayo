<?php
// Include necessary files
require_once __DIR__ . '/../../config/database.php';

// Create a new database connection
$db = new DatabaseConnection();
$conn = $db->getConnection();

// Get users
$users = [];
if ($conn) {
    $stmt = $conn->prepare("
        SELECT DISTINCT id, username, email, first_name, last_name, 
               is_active as status, created_at 
        FROM users 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Map is_active to status
    foreach ($users as &$user) {
        $user['role'] = 'user'; // Default role for all users
        $user['status'] = $user['status'] ? 'active' : 'inactive';
    }
    
    // Debug output
    echo "Users from controller:\n";
    foreach ($users as $user) {
        echo "ID: " . $user['id'] . 
             ", Username: " . $user['username'] . 
             ", Email: " . $user['email'] . 
             ", Name: " . $user['first_name'] . " " . $user['last_name'] . 
             ", Status: " . $user['status'] . 
             ", Created: " . $user['created_at'] . "\n";
    }
}
?>