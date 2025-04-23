<?php
/**
 * Database Class
 * 
 * This class handles database connections and operations.
 */
class Database {
    /**
     * @var PDO The database connection
     */
    private $conn;
    
    /**
     * @var Database The singleton instance
     */
    private static $instance;
    
    /**
     * Constructor
     * 
     * Establishes a connection to the database.
     */
    public function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE " . DB_COLLATION,
                    PDO::ATTR_TIMEOUT => 5, // 5 seconds timeout
                    PDO::ATTR_PERSISTENT => false // Don't use persistent connections
                ]
            );
        } catch (PDOException $e) {
            // Log the error
            error_log("Database connection error: " . $e->getMessage());
            
            // Set connection to null instead of dying
            $this->conn = null;
        }
    }
    
    /**
     * Get the database connection
     * 
     * @return PDO|null The database connection or null if connection failed
     */
    public function getConnection() {
        if ($this->conn === null) {
            // Try to reconnect
            try {
                $this->conn = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE " . DB_COLLATION,
                        PDO::ATTR_TIMEOUT => 5,
                        PDO::ATTR_PERSISTENT => false
                    ]
                );
            } catch (PDOException $e) {
                error_log("Database reconnection error: " . $e->getMessage());
            }
        }
        return $this->conn;
    }
    
    /**
     * Get the singleton instance
     * 
     * @return Database The singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Execute a query and return the result
     * 
     * @param string $query The SQL query
     * @param array $params The query parameters
     * @return array The query result
     */
    public function query($query, $params = []) {
        if ($this->conn === null) {
            error_log("Cannot execute query: Database connection is null");
            return [];
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Log the error
            error_log("Database query error: " . $e->getMessage() . " - Query: " . $query);
            
            // Return an empty array
            return [];
        }
    }
    
    /**
     * Execute a query and return a single row
     * 
     * @param string $query The SQL query
     * @param array $params The query parameters
     * @return array|null The query result or null if no result
     */
    public function queryOne($query, $params = []) {
        if ($this->conn === null) {
            error_log("Cannot execute queryOne: Database connection is null");
            return null;
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            $result = $stmt->fetch();
            
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            // Log the error
            error_log("Database queryOne error: " . $e->getMessage() . " - Query: " . $query);
            
            // Return null
            return null;
        }
    }
    
    /**
     * Execute a query and return the number of affected rows
     * 
     * @param string $query The SQL query
     * @param array $params The query parameters
     * @return int The number of affected rows
     */
    public function execute($query, $params = []) {
        if ($this->conn === null) {
            error_log("Cannot execute query: Database connection is null");
            return 0;
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            // Log the error
            error_log("Database execute error: " . $e->getMessage() . " - Query: " . $query);
            
            // Return 0
            return 0;
        }
    }
    
    /**
     * Insert a record into a table
     * 
     * @param string $table The table name
     * @param array $data The data to insert
     * @return int|null The last insert ID or null on failure
     */
    public function insert($table, $data) {
        if ($this->conn === null) {
            error_log("Cannot execute insert: Database connection is null");
            return null;
        }
        
        try {
            // Build the query
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            
            $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
            
            // Execute the query
            $stmt = $this->conn->prepare($query);
            $stmt->execute(array_values($data));
            
            // Return the last insert ID
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            // Log the error
            error_log("Database insert error: " . $e->getMessage() . " - Table: " . $table);
            
            // Return null
            return null;
        }
    }
    
    /**
     * Update a record in a table
     * 
     * @param string $table The table name
     * @param array $data The data to update
     * @param string $where The WHERE clause
     * @param array $whereParams The WHERE clause parameters
     * @return int The number of affected rows
     */
    public function update($table, $data, $where, $whereParams = []) {
        if ($this->conn === null) {
            error_log("Cannot execute update: Database connection is null");
            return 0;
        }
        
        try {
            // Build the SET clause
            $set = [];
            foreach (array_keys($data) as $column) {
                $set[] = "{$column} = ?";
            }
            $set = implode(', ', $set);
            
            // Build the query
            $query = "UPDATE {$table} SET {$set} WHERE {$where}";
            
            // Execute the query
            $stmt = $this->conn->prepare($query);
            $stmt->execute(array_merge(array_values($data), $whereParams));
            
            // Return the number of affected rows
            return $stmt->rowCount();
        } catch (PDOException $e) {
            // Log the error
            error_log("Database update error: " . $e->getMessage() . " - Table: " . $table);
            
            // Return 0
            return 0;
        }
    }
    
    /**
     * Delete a record from a table
     * 
     * @param string $table The table name
     * @param string $where The WHERE clause
     * @param array $params The WHERE clause parameters
     * @return int The number of affected rows
     */
    public function delete($table, $where, $params = []) {
        if ($this->conn === null) {
            error_log("Cannot execute delete: Database connection is null");
            return 0;
        }
        
        try {
            // Build the query
            $query = "DELETE FROM {$table} WHERE {$where}";
            
            // Execute the query
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            // Return the number of affected rows
            return $stmt->rowCount();
        } catch (PDOException $e) {
            // Log the error
            error_log("Database delete error: " . $e->getMessage() . " - Table: " . $table);
            
            // Return 0
            return 0;
        }
    }
    
    /**
     * Begin a transaction
     * 
     * @return bool True on success, false on failure
     */
    public function beginTransaction() {
        if ($this->conn === null) {
            error_log("Cannot begin transaction: Database connection is null");
            return false;
        }
        
        try {
            return $this->conn->beginTransaction();
        } catch (PDOException $e) {
            error_log("Database beginTransaction error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Commit a transaction
     * 
     * @return bool True on success, false on failure
     */
    public function commit() {
        if ($this->conn === null) {
            error_log("Cannot commit transaction: Database connection is null");
            return false;
        }
        
        try {
            return $this->conn->commit();
        } catch (PDOException $e) {
            error_log("Database commit error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Roll back a transaction
     * 
     * @return bool True on success, false on failure
     */
    public function rollBack() {
        if ($this->conn === null) {
            error_log("Cannot rollback transaction: Database connection is null");
            return false;
        }
        
        try {
            return $this->conn->rollBack();
        } catch (PDOException $e) {
            error_log("Database rollback error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get the last insert ID
     * 
     * @return string|false The last insert ID or false on failure
     */
    public function lastInsertId() {
        if ($this->conn === null) {
            error_log("Cannot get lastInsertId: Database connection is null");
            return false;
        }
        
        try {
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Database lastInsertId error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Quote a string for use in a query
     * 
     * @param string $string The string to quote
     * @return string|false The quoted string or false on failure
     */
    public function quote($string) {
        if ($this->conn === null) {
            error_log("Cannot quote string: Database connection is null");
            return false;
        }
        
        try {
            return $this->conn->quote($string);
        } catch (PDOException $e) {
            error_log("Database quote error: " . $e->getMessage());
            return false;
        }
    }
}
?>