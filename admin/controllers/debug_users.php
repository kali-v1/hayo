<?php
// Include necessary files
require_once __DIR__ . '/../../config/database.php';

// Create a new database connection
$db = new DatabaseConnection();
$conn = $db->getConnection();

// Get users directly from database
$users_db = [];
if ($conn) {
    $stmt = $conn->prepare("
        SELECT DISTINCT id, username, email, first_name, last_name, 
               is_active as status, created_at 
        FROM users 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $users_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Users from database query:\n";
    foreach ($users_db as $user) {
        echo "ID: " . $user['id'] . 
             ", Username: " . $user['username'] . 
             ", Email: " . $user['email'] . 
             ", Created: " . $user['created_at'] . "\n";
    }
}

// Now let's manually add a user with ID 4 to see if that's causing the issue
$users_db[] = [
    'id' => 4,
    'username' => 'alicesmith',
    'email' => 'alice@example.com',
    'first_name' => 'Alice',
    'last_name' => 'Smith',
    'status' => 1,
    'created_at' => '2025-04-17 21:20:35'
];

echo "\nUsers after manually adding ID 4:\n";
foreach ($users_db as $user) {
    echo "ID: " . $user['id'] . 
         ", Username: " . $user['username'] . 
         ", Email: " . $user['email'] . 
         ", Created: " . $user['created_at'] . "\n";
}
?>