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
    
    // Debug the users array
    echo "Users array after mapping:\n";
    foreach ($users as $index => $user) {
        echo "Index: $index, ID: " . $user['id'] . 
             ", Username: " . $user['username'] . 
             ", Email: " . $user['email'] . 
             ", Status: " . $user['status'] . "\n";
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
    
    // Check if there's a bug in the foreach loop with the reference
    echo "\nChecking for reference bug:\n";
    $test_array = [
        ['id' => 1, 'name' => 'One'],
        ['id' => 2, 'name' => 'Two'],
        ['id' => 3, 'name' => 'Three']
    ];
    
    foreach ($test_array as &$item) {
        $item['extra'] = 'Added';
    }
    
    // This is important - unset the reference!
    unset($item);
    
    foreach ($test_array as $index => $item) {
        echo "Index: $index, ID: " . $item['id'] . ", Name: " . $item['name'] . ", Extra: " . $item['extra'] . "\n";
    }
}
?>