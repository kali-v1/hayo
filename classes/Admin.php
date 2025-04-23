<?php
/**
 * Admin Class
 * 
 * Handles admin-related operations.
 */
class Admin {
    private $conn;
    private $table = "admins";
    
    // Admin properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $name;
    public $role; // admin, data_entry, instructor
    public $created_at;
    public $updated_at;
    
    /**
     * Constructor
     * 
     * @param PDO $db Database connection
     */
    public function __construct($db = null) {
        if ($db) {
            $this->conn = $db;
        } else {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    }
    
    /**
     * Get all admins
     * 
     * @param int $limit Limit the number of results
     * @param int $offset Offset for pagination
     * @return array Array of admins
     */
    public function getAll($limit = 10, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get admin by ID
     * 
     * @param int $id Admin ID
     * @return bool True if admin found, false otherwise
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->password = $row['password'];
            $this->name = $row['name'];
            $this->role = $row['role'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get admin by email
     * 
     * @param string $email Admin email
     * @return bool True if admin found, false otherwise
     */
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->password = $row['password'];
            $this->name = $row['name'];
            $this->role = $row['role'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get admin by username
     * 
     * @param string $username Admin username
     * @return bool True if admin found, false otherwise
     */
    public function getByUsername($username) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->password = $row['password'];
            $this->name = $row['name'];
            $this->role = $row['role'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Create a new admin
     * 
     * @return bool True if created successfully, false otherwise
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (username, email, password, name, role, created_at, updated_at) 
                  VALUES 
                  (:username, :email, :password, :name, :role, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->role = htmlspecialchars(strip_tags($this->role));
        
        // Hash password
        $password_hash = password_hash($this->password, Config::PASSWORD_HASH_ALGO, Config::PASSWORD_HASH_OPTIONS);
        
        // Bind parameters
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':role', $this->role);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Update admin
     * 
     * @return bool True if updated successfully, false otherwise
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET 
                  username = :username, 
                  email = :email, 
                  name = :name, 
                  role = :role, 
                  updated_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->role = htmlspecialchars(strip_tags($this->role));
        
        // Bind parameters
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Update admin password
     * 
     * @param string $new_password New password
     * @return bool True if updated successfully, false otherwise
     */
    public function updatePassword($new_password) {
        $query = "UPDATE " . $this->table . " 
                  SET 
                  password = :password, 
                  updated_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password
        $password_hash = password_hash($new_password, Config::PASSWORD_HASH_ALGO, Config::PASSWORD_HASH_OPTIONS);
        
        // Bind parameters
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Delete admin
     * 
     * @return bool True if deleted successfully, false otherwise
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Count total admins
     * 
     * @return int Total number of admins
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $row['total'];
    }
    
    /**
     * Verify password
     * 
     * @param string $password Password to verify
     * @return bool True if password is correct, false otherwise
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }
    
    /**
     * Check if admin has permission
     * 
     * @param string $permission Permission to check
     * @return bool True if admin has permission, false otherwise
     */
    public function hasPermission($permission) {
        switch ($this->role) {
            case 'admin':
                // Admin has all permissions
                return true;
                
            case 'data_entry':
                // Data entry can only manage questions
                $data_entry_permissions = ['manage_questions'];
                return in_array($permission, $data_entry_permissions);
                
            case 'instructor':
                // Instructor can only manage their courses
                $instructor_permissions = ['manage_courses', 'view_courses'];
                return in_array($permission, $instructor_permissions);
                
            default:
                return false;
        }
    }
    
    /**
     * Login admin
     * 
     * @param string $username Username
     * @param string $password Password
     * @return bool True if login successful, false otherwise
     */
    public static function login($username, $password) {
        $admin = new Admin();
        
        // Try to find admin by username
        if ($admin->getByUsername($username)) {
            // Verify password
            if ($admin->verifyPassword($password)) {
                // Set session variables
                $_SESSION['admin_id'] = $admin->id;
                $_SESSION['admin_username'] = $admin->username;
                $_SESSION['admin_email'] = $admin->email;
                $_SESSION['admin_name'] = $admin->name;
                $_SESSION['admin_role'] = $admin->role;
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Logout admin
     * 
     * @return void
     */
    public static function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session
        session_destroy();
    }
    
    /**
     * Check if admin is logged in
     * 
     * @return bool True if admin is logged in, false otherwise
     */
    public static function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }
    
    /**
     * Get current admin
     * 
     * @return Admin|null Admin object if logged in, null otherwise
     */
    public static function getCurrentAdmin() {
        if (self::isLoggedIn()) {
            $admin = new Admin();
            $admin->getById($_SESSION['admin_id']);
            return $admin;
        }
        
        return null;
    }
}
?>