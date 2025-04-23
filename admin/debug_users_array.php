<?php
// Include necessary files
define('ADMIN_ROOT', __DIR__);
require_once __DIR__ . '/../config/database.php';

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
}

// Debug output
echo "Users array before template:\n";
echo "Total users: " . count($users) . "\n\n";

foreach ($users as $index => $user) {
    echo "Index: $index, ID: " . $user['id'] . 
         ", Username: " . $user['username'] . 
         ", Email: " . $user['email'] . 
         ", Name: " . $user['first_name'] . " " . $user['last_name'] . 
         ", Status: " . $user['status'] . 
         ", Created: " . $user['created_at'] . "\n";
}

// Check for duplicate IDs
$ids = [];
$duplicates = [];
foreach ($users as $user) {
    if (in_array($user['id'], $ids)) {
        $duplicates[] = $user['id'];
    } else {
        $ids[] = $user['id'];
    }
}

echo "\nDuplicate IDs: " . (empty($duplicates) ? "None" : implode(", ", array_unique($duplicates))) . "\n";

// Add a hardcoded user with ID 4 to see if that's what's happening
$users[] = [
    'id' => 4,
    'username' => 'alicesmith',
    'email' => 'alice@example.com',
    'first_name' => 'Alice',
    'last_name' => 'Smith',
    'role' => 'user',
    'status' => 'active',
    'created_at' => '2025-04-17 21:20:35'
];

echo "\nUsers array after adding hardcoded user:\n";
echo "Total users: " . count($users) . "\n\n";

foreach ($users as $index => $user) {
    echo "Index: $index, ID: " . $user['id'] . 
         ", Username: " . $user['username'] . 
         ", Email: " . $user['email'] . 
         ", Name: " . $user['first_name'] . " " . $user['last_name'] . 
         ", Status: " . $user['status'] . 
         ", Created: " . $user['created_at'] . "\n";
}
?>