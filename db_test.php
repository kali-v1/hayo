<?php
require_once 'config/database.php';

// Create a new database connection
$database = new DatabaseConnection();
$conn = $database->getConnection();

// Check if connection was successful
if ($conn) {
    echo "Database connection successful!";
    
    // Test query to verify database content
    $query = "SHOW TABLES";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    echo "<h2>Tables in the database:</h2>";
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>" . $row['Tables_in_huuter_db'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Database connection failed!";
}
?>