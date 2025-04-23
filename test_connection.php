<?php
require_once 'config/database.php';

// Create a new database connection
$database = new DatabaseConnection();
$conn = $database->getConnection();

// Check if connection was successful
if ($conn) {
    echo "Database connection successful!";
    
    // Test query to verify we can access the database
    try {
        $stmt = $conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        
        echo "<h3>Tables in the database:</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table[0] . "</li>";
        }
        echo "</ul>";
    } catch (PDOException $e) {
        echo "Query error: " . $e->getMessage();
    }
} else {
    echo "Database connection failed!";
}
?>