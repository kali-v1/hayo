<?php
/**
 * Admin Class
 * 
 * This class represents an admin user and provides methods for admin-related operations.
 */
class Admin {
    /**
     * @var int The admin ID
     */
    public $id;
    
    /**
     * @var string The admin username
     */
    private $username;
    
    /**
     * @var string The admin email
     */
    private $email;
    
    /**
     * @var string The admin name
     */
    private $name;
    
    /**
     * @var string The admin role
     */
    private $role;
    
    /**
     * @var string The admin profile image
     */
    private $profileImage;
    
    /**
     * @var bool Whether the admin is active
     */
    private $isActive;
    
    /**
     * @var string The admin's last login time
     */
    private $lastLogin;
    
    /**
     * @var string The admin's creation time
     */
    private $createdAt;
    
    /**
     * @var string The admin's last update time
     */
    private $updatedAt;
    
    /**
     * @var Database The database connection
     */
    private $db;
    
    /**
     * Constructor
     * 
     * @param int $id The admin ID (optional)
     */
    public function __construct($id = null) {
        $this->db = new Database();
        
        if ($id) {
            $this->loadById($id);
        }
    }
    
    /**
     * Load an admin by ID
     * 
     * @param int $id The admin ID
     * @return bool True if successful, false otherwise
     */
    public function loadById($id) {
        $admin = $this->db->queryOne("SELECT * FROM admins WHERE id = ?", [$id]);
        
        if (!$admin) {
            return false;
        }
        
        $this->setProperties($admin);
        
        return true;
    }
    
    /**
     * Load an admin by username
     * 
     * @param string $username The admin username
     * @return bool True if successful, false otherwise
     */
    public function loadByUsername($username) {
        $admin = $this->db->queryOne("SELECT * FROM admins WHERE username = ?", [$username]);
        
        if (!$admin) {
            return false;
        }
        
        $this->setProperties($admin);
        
        return true;
    }
    
    /**
     * Load an admin by email
     * 
     * @param string $email The admin email
     * @return bool True if successful, false otherwise
     */
    public function loadByEmail($email) {
        $admin = $this->db->queryOne("SELECT * FROM admins WHERE email = ?", [$email]);
        
        if (!$admin) {
            return false;
        }
        
        $this->setProperties($admin);
        
        return true;
    }
    
    /**
     * Save the admin to the database
     * 
     * @return bool True if successful, false otherwise
     */
    public function save() {
        $data = [
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'role' => $this->role,
            'profile_image' => $this->profileImage,
            'is_active' => $this->isActive,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->id) {
            // Update existing admin
            return $this->db->update('admins', $data, 'id = ?', [$this->id]) > 0;
        } else {
            // Create new admin
            $data['created_at'] = date('Y-m-d H:i:s');
            
            $id = $this->db->insert('admins', $data);
            
            if ($id) {
                $this->id = $id;
                return true;
            }
            
            return false;
        }
    }
    
    /**
     * Delete the admin from the database
     * 
     * @return bool True if successful, false otherwise
     */
    public function delete() {
        if (!$this->id) {
            return false;
        }
        
        return $this->db->delete('admins', 'id = ?', [$this->id]) > 0;
    }
    
    /**
     * Set the admin's password
     * 
     * @param string $password The new password
     * @return bool True if successful, false otherwise
     */
    public function setPassword($password) {
        if (!$this->id) {
            return false;
        }
        
        $hashedPassword = password_hash($password, PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
        
        return $this->db->update('admins', ['password' => $hashedPassword, 'updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$this->id]) > 0;
    }
    
    /**
     * Verify a password
     * 
     * @param string $password The password to verify
     * @return bool True if the password is correct, false otherwise
     */
    public function verifyPassword($password) {
        $admin = $this->db->queryOne("SELECT password FROM admins WHERE id = ?", [$this->id]);
        
        if (!$admin) {
            return false;
        }
        
        return password_verify($password, $admin['password']);
    }
    
    /**
     * Get all admins
     * 
     * @param string $role Filter by role (optional)
     * @param int $limit The maximum number of admins to return (optional)
     * @param int $offset The offset for pagination (optional)
     * @return array The admins
     */
    public function getAll($role = null, $limit = null, $offset = null) {
        $query = "SELECT * FROM admins";
        $params = [];
        
        if ($role) {
            $query .= " WHERE role = ?";
            $params[] = $role;
        }
        
        $query .= " ORDER BY name ASC";
        
        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = (int) $limit;
            
            if ($offset) {
                $query .= " OFFSET ?";
                $params[] = (int) $offset;
            }
        }
        
        return $this->db->query($query, $params);
    }
    
    /**
     * Count all admins
     * 
     * @param string $role Filter by role (optional)
     * @return int The number of admins
     */
    public function countAll($role = null) {
        $query = "SELECT COUNT(*) as count FROM admins";
        $params = [];
        
        if ($role) {
            $query .= " WHERE role = ?";
            $params[] = $role;
        }
        
        $result = $this->db->queryOne($query, $params);
        
        return $result ? (int) $result['count'] : 0;
    }
    
    /**
     * Get the admin's courses
     * 
     * @param int $limit The maximum number of courses to return (optional)
     * @param int $offset The offset for pagination (optional)
     * @return array The courses
     */
    public function getCourses($limit = null, $offset = null) {
        if (!$this->id) {
            return [];
        }
        
        $query = "SELECT * FROM courses WHERE admin_id = ? ORDER BY title ASC";
        $params = [$this->id];
        
        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = (int) $limit;
            
            if ($offset) {
                $query .= " OFFSET ?";
                $params[] = (int) $offset;
            }
        }
        
        return $this->db->query($query, $params);
    }
    
    /**
     * Count the admin's courses
     * 
     * @return int The number of courses
     */
    public function countCourses() {
        if (!$this->id) {
            return 0;
        }
        
        $result = $this->db->queryOne("SELECT COUNT(*) as count FROM courses WHERE admin_id = ?", [$this->id]);
        
        return $result ? (int) $result['count'] : 0;
    }
    
    /**
     * Get the admin's activity log
     * 
     * @param int $limit The maximum number of log entries to return (optional)
     * @param int $offset The offset for pagination (optional)
     * @return array The activity log
     */
    public function getActivityLog($limit = null, $offset = null) {
        if (!$this->id) {
            return [];
        }
        
        $query = "SELECT * FROM activity_logs WHERE admin_id = ? ORDER BY created_at DESC";
        $params = [$this->id];
        
        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = (int) $limit;
            
            if ($offset) {
                $query .= " OFFSET ?";
                $params[] = (int) $offset;
            }
        }
        
        return $this->db->query($query, $params);
    }
    
    /**
     * Log an activity
     * 
     * @param string $action The action performed
     * @param string $description The description of the action (optional)
     * @return bool True if successful, false otherwise
     */
    public function logActivity($action, $description = null) {
        if (!$this->id) {
            return false;
        }
        
        $data = [
            'admin_id' => $this->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('activity_logs', $data) > 0;
    }
    
    /**
     * Set the admin's properties from an array
     * 
     * @param array $data The admin data
     * @return void
     */
    private function setProperties($data) {
        $this->id = (int) $data['id'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->name = $data['name'];
        $this->role = $data['role'];
        $this->profileImage = $data['profile_image'];
        $this->isActive = (bool) $data['is_active'];
        $this->lastLogin = $data['last_login'];
        $this->createdAt = $data['created_at'];
        $this->updatedAt = $data['updated_at'];
    }
    
    /**
     * Get the admin ID
     * 
     * @return int The admin ID
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Get the admin username
     * 
     * @return string The admin username
     */
    public function getUsername() {
        return $this->username;
    }
    
    /**
     * Set the admin username
     * 
     * @param string $username The admin username
     * @return void
     */
    public function setUsername($username) {
        $this->username = $username;
    }
    
    /**
     * Get the admin email
     * 
     * @return string The admin email
     */
    public function getEmail() {
        return $this->email;
    }
    
    /**
     * Set the admin email
     * 
     * @param string $email The admin email
     * @return void
     */
    public function setEmail($email) {
        $this->email = $email;
    }
    
    /**
     * Get the admin name
     * 
     * @return string The admin name
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Set the admin name
     * 
     * @param string $name The admin name
     * @return void
     */
    public function setName($name) {
        $this->name = $name;
    }
    
    /**
     * Get the admin role
     * 
     * @return string The admin role
     */
    public function getRole() {
        return $this->role;
    }
    
    /**
     * Set the admin role
     * 
     * @param string $role The admin role
     * @return void
     */
    public function setRole($role) {
        $this->role = $role;
    }
    
    /**
     * Get the admin profile image
     * 
     * @return string The admin profile image
     */
    public function getProfileImage() {
        return $this->profileImage;
    }
    
    /**
     * Set the admin profile image
     * 
     * @param string $profileImage The admin profile image
     * @return void
     */
    public function setProfileImage($profileImage) {
        $this->profileImage = $profileImage;
    }
    
    /**
     * Check if the admin is active
     * 
     * @return bool True if the admin is active, false otherwise
     */
    public function isActive() {
        return $this->isActive;
    }
    
    /**
     * Set whether the admin is active
     * 
     * @param bool $isActive Whether the admin is active
     * @return void
     */
    public function setActive($isActive) {
        $this->isActive = (bool) $isActive;
    }
    
    /**
     * Get the admin's last login time
     * 
     * @return string The admin's last login time
     */
    public function getLastLogin() {
        return $this->lastLogin;
    }
    
    /**
     * Get the admin's creation time
     * 
     * @return string The admin's creation time
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    /**
     * Get the admin's last update time
     * 
     * @return string The admin's last update time
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
}
?>