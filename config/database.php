<?php
/**
 * Database Configuration
 * 
 * This file contains the database connection settings.
 */

// Database settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'huuter_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');
define('DB_PREFIX', '');

/**
 * DatabaseConnection Class
 * 
 * This class handles the database connection.
 */
class DatabaseConnection {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    /**
     * Constructor
     */
    public function __construct() {
        $this->host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
    }

    /**
     * Get the database connection
     * 
     * @return PDO|null The database connection or null on failure
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE " . DB_COLLATION
                ]
            );
        } catch(PDOException $e) {
            // Log the error instead of displaying it
            error_log("Database connection error: " . $e->getMessage());
            
            // Return null instead of dying, so the application can continue without a database
            return null;
        }

        return $this->conn;
    }
}
?>