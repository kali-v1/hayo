<?php
// Include necessary files
require_once __DIR__ . '/../config/database.php';

// Create a new database connection
$db = new DatabaseConnection();
$conn = $db->getConnection();

// Get users directly from database
$users_db = [];
if ($conn) {
    // Test with different fetch modes
    echo "Testing with PDO::FETCH_ASSOC:\n";
    $stmt = $conn->prepare("
        SELECT DISTINCT id, username, email, first_name, last_name, 
               is_active as status, created_at 
        FROM users 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $users_assoc = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users_assoc as $index => $user) {
        echo "Index: $index, ID: " . $user['id'] . 
             ", Username: " . $user['username'] . 
             ", Email: " . $user['email'] . "\n";
    }
    
    echo "\nTesting with PDO::FETCH_UNIQUE:\n";
    $stmt = $conn->prepare("
        SELECT DISTINCT id, username, email, first_name, last_name, 
               is_active as status, created_at 
        FROM users 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $users_unique = $stmt->fetchAll(PDO::FETCH_UNIQUE);
    
    foreach ($users_unique as $id => $user) {
        echo "ID: $id, Username: " . $user['username'] . 
             ", Email: " . $user['email'] . "\n";
    }
    
    echo "\nTesting with PDO::FETCH_GROUP:\n";
    $stmt = $conn->prepare("
        SELECT DISTINCT id, username, email, first_name, last_name, 
               is_active as status, created_at 
        FROM users 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $users_group = $stmt->fetchAll(PDO::FETCH_GROUP);
    
    foreach ($users_group as $id => $user_group) {
        echo "ID: $id, Count: " . count($user_group) . "\n";
        foreach ($user_group as $index => $user) {
            echo "  Index: $index, Username: " . $user['username'] . 
                 ", Email: " . $user['email'] . "\n";
        }
    }
}
?>