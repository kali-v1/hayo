<?php
require_once 'config/database.php';

// Create a new database connection
$db = new DatabaseConnection();
$conn = $db->getConnection();

if ($conn) {
    echo "Database connection successful!\n";
    
    // Test query to check users
    try {
        $stmt = $conn->prepare("
            SELECT DISTINCT id, username, email, first_name, last_name, 
                   is_active as status, created_at 
            FROM users 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Users in the database:\n";
        foreach ($users as $user) {
            echo "ID: " . $user['id'] . 
                 ", Username: " . $user['username'] . 
                 ", Email: " . $user['email'] . 
                 ", Name: " . $user['first_name'] . " " . $user['last_name'] . 
                 ", Status: " . $user['status'] . 
                 ", Created: " . $user['created_at'] . "\n";
        }
    } catch (PDOException $e) {
        echo "Query error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Database connection failed!\n";
}
?>