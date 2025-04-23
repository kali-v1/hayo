<?php
require_once 'config/database.php';

// Create a new database connection
$db = new DatabaseConnection();
$conn = $db->getConnection();

if ($conn) {
    echo "Database connection successful!\n";
    
    // Test query to check if we can access the admins table
    try {
        $stmt = $conn->query("SELECT id, username, email FROM admins");
        $admins = $stmt->fetchAll();
        
        echo "Admins in the database:\n";
        foreach ($admins as $admin) {
            echo "ID: " . $admin['id'] . ", Username: " . $admin['username'] . ", Email: " . $admin['email'] . "\n";
        }
    } catch (PDOException $e) {
        echo "Query error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Database connection failed!\n";
}
?>