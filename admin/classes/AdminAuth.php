<?php
/**
 * AdminAuth Class
 * 
 * This class handles authentication and authorization for the admin panel.
 */
class AdminAuth {
    /**
     * @var array|null The current admin user
     */
    private static $currentAdmin = null;
    
    /**
     * @var Database The database connection
     */
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Attempt to log in an admin
     * 
     * @param string $username The admin's username
     * @param string $password The admin's password
     * @param bool $remember Whether to remember the login
     * @return bool True if login successful, false otherwise
     */
    public function login($username, $password, $remember = false) {
        // Get the admin by username
        $admin = $this->getAdminByUsername($username);
        
        if (!$admin) {
            return false;
        }
        
        // Verify the password
        if (!password_verify($password, $admin['password'])) {
            return false;
        }
        
        // Check if the admin is active
        if (!$admin['is_active']) {
            return false;
        }
        
        // Set session variables
        $this->setAdminSession($admin);
        
        // Update last login time
        $this->updateLastLogin($admin['id']);
        
        // Log the login activity
        require_once __DIR__ . '/../../classes/ActivityLogger.php';
        $logger = new ActivityLogger($this->db->getConnection());
        $logger->logAdmin($admin['id'], 'login', "تسجيل دخول إلى لوحة التحكم");
        
        return true;
    }
    
    /**
     * Log out the current admin
     * 
     * @return void
     */
    public function logout() {
        // Get the current admin before unsetting session
        $adminId = $_SESSION['admin_id'] ?? null;
        $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
        
        // Unset admin session variables
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
        
        // Reset the current admin
        self::$currentAdmin = null;
        
        // Log the logout activity if we have an admin ID
        if ($adminId) {
            global $conn;
            if ($conn) {
                require_once __DIR__ . '/../../classes/ActivityLogger.php';
                $logger = new ActivityLogger($conn);
                $logger->logAdmin($adminId, 'logout', "تسجيل خروج المستخدم: {$adminUsername}");
            }
        }
    }
    
    /**
     * Check if an admin is logged in
     * 
     * @return bool True if an admin is logged in, false otherwise
     */
    public function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }
    
    /**
     * Get the current admin
     * 
     * @return array|null The current admin data or null if not logged in
     */
    public function getCurrentAdmin() {
        if (self::$currentAdmin !== null) {
            return self::$currentAdmin;
        }
        
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        // Get the admin from the database
        self::$currentAdmin = $this->getAdminById($_SESSION['admin_id']);
        
        return self::$currentAdmin;
    }
    
    /**
     * Check if the current admin has a specific permission
     * 
     * @param string $permission The permission to check
     * @return bool True if the admin has the permission, false otherwise
     */
    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $adminRole = $_SESSION['admin_role'];
        
        // Admin has all permissions
        if ($adminRole === 'admin') {
            return true;
        }
        
        // Define permissions for each role
        $permissions = [
            'admin' => ['manage_users', 'manage_courses', 'manage_exams', 'manage_questions', 'view_all_courses', 'view_all_stats', 'approve_courses'],
            'instructor' => ['manage_own_courses', 'view_own_stats'],
            'data_entry' => ['manage_questions']
        ];
        
        // Check if the admin's role has the requested permission
        return isset($permissions[$adminRole]) && in_array($permission, $permissions[$adminRole]);
    }
    
    /**
     * Get an admin by ID
     * 
     * @param int $id The admin ID
     * @return array|null The admin data or null if not found
     */
    public function getAdminById($id) {
        return $this->db->queryOne("SELECT * FROM admins WHERE id = ?", [$id]);
    }
    
    /**
     * Get an admin by username
     * 
     * @param string $username The admin username
     * @return array|null The admin data or null if not found
     */
    public function getAdminByUsername($username) {
        return $this->db->queryOne("SELECT * FROM admins WHERE username = ?", [$username]);
    }
    
    /**
     * Get an admin by email
     * 
     * @param string $email The admin email
     * @return array|null The admin data or null if not found
     */
    public function getAdminByEmail($email) {
        return $this->db->queryOne("SELECT * FROM admins WHERE email = ?", [$email]);
    }
    
    /**
     * Update an admin's password
     * 
     * @param int $adminId The admin ID
     * @param string $currentPassword The current password
     * @param string $newPassword The new password
     * @return bool True if successful, false otherwise
     */
    public function updatePassword($adminId, $currentPassword, $newPassword) {
        // Get the admin
        $admin = $this->getAdminById($adminId);
        
        if (!$admin) {
            return false;
        }
        
        // Verify the current password
        if (!password_verify($currentPassword, $admin['password'])) {
            return false;
        }
        
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
        
        // Update the password
        return $this->db->update('admins', ['password' => $hashedPassword, 'updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$adminId]) > 0;
    }
    
    /**
     * Create a new admin
     * 
     * @param array $data The admin data
     * @return int|bool The admin ID if successful, false otherwise
     */
    public function createAdmin($data) {
        // Check if the username already exists
        if ($this->getAdminByUsername($data['username'])) {
            return false;
        }
        
        // Check if the email already exists
        if ($this->getAdminByEmail($data['email'])) {
            return false;
        }
        
        // Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
        
        // Set default values
        $data['is_active'] = 1;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        // Insert the admin into the database
        return $this->db->insert('admins', $data);
    }
    
    /**
     * Update an admin's profile
     * 
     * @param int $adminId The admin ID
     * @param array $data The profile data
     * @return bool True if successful, false otherwise
     */
    public function updateProfile($adminId, $data) {
        // Set the updated_at timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        // Update the admin
        return $this->db->update('admins', $data, 'id = ?', [$adminId]) > 0;
    }
    
    /**
     * Delete an admin
     * 
     * @param int $adminId The admin ID
     * @return bool True if successful, false otherwise
     */
    public function deleteAdmin($adminId) {
        // Check if the admin exists
        $admin = $this->getAdminById($adminId);
        
        if (!$admin) {
            return false;
        }
        
        // Delete the admin
        return $this->db->delete('admins', 'id = ?', [$adminId]) > 0;
    }
    
    /**
     * Get all admins
     * 
     * @param string $role Filter by role (optional)
     * @return array The admins
     */
    public function getAllAdmins($role = null) {
        if ($role) {
            return $this->db->query("SELECT * FROM admins WHERE role = ? ORDER BY name ASC", [$role]);
        }
        
        return $this->db->query("SELECT * FROM admins ORDER BY name ASC");
    }
    
    /**
     * Set the admin session
     * 
     * @param array $admin The admin data
     * @return void
     */
    private function setAdminSession($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_role'] = $admin['role'];
    }
    
    /**
     * Update the last login time for an admin
     * 
     * @param int $adminId The admin ID
     * @return bool True if successful, false otherwise
     */
    private function updateLastLogin($adminId) {
        return $this->db->update('admins', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$adminId]) > 0;
    }
}
?>