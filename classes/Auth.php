<?php
/**
 * Auth Class
 * 
 * This class handles user authentication and authorization.
 */
class Auth {
    /**
     * @var User|null The current user
     */
    private static $currentUser = null;
    
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
     * Attempt to log in a user
     * 
     * @param string $email The user's email
     * @param string $password The user's password
     * @param bool $remember Whether to remember the user
     * @return bool True if login successful, false otherwise
     */
    public function login($email, $password, $remember = false) {
        // Get the user by email
        $user = $this->getUserByEmail($email);
        
        error_log("Login attempt for email: $email");
        
        if (!$user) {
            error_log("User not found with email: $email");
            return false;
        }
        
        error_log("User found: " . json_encode($user));
        
        // Verify the password
        $passwordVerified = password_verify($password, $user['password']);
        error_log("Password verification result: " . ($passwordVerified ? "true" : "false"));
        
        if (!$passwordVerified) {
            error_log("Password verification failed for user: " . $user['email']);
            return false;
        }
        
        // Check if the user is active
        if (!$user['is_active']) {
            error_log("User is not active: " . $user['email']);
            return false;
        }
        
        // Set session variables
        $this->setUserSession($user);
        
        // Update last login time
        $this->updateLastLogin($user['id']);
        
        // Set remember me cookie if requested
        if ($remember) {
            $this->setRememberMeCookie($user['id']);
        }
        
        error_log("Login successful for user: " . $user['email']);
        return true;
    }
    
    /**
     * Log out the current user
     * 
     * @return void
     */
    public function logout() {
        // Clear the remember me cookie if it exists
        if (isset($_COOKIE['remember_me'])) {
            $this->clearRememberMeCookie();
        }
        
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session
        session_destroy();
        
        // Reset the current user
        self::$currentUser = null;
    }
    
    /**
     * Register a new user
     * 
     * @param array $data The user data
     * @return int|bool The user ID if successful, false otherwise
     */
    public function register($data) {
        // Check if the email already exists
        if ($this->getUserByEmail($data['email'])) {
            return false;
        }
        
        // Check if the username already exists
        if ($this->getUserByUsername($data['username'])) {
            return false;
        }
        
        // Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
        
        // Set default values
        $data['is_active'] = 1;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        // Insert the user into the database
        return $this->db->insert('users', $data);
    }
    
    /**
     * Check if a user is logged in
     * 
     * @return bool True if a user is logged in, false otherwise
     */
    public function isLoggedIn() {
        // Check if the user is already logged in via session
        if (isset($_SESSION['user_id'])) {
            return true;
        }
        
        // Check if the user has a remember me cookie
        if (isset($_COOKIE['remember_me'])) {
            // Validate the remember me cookie
            $userId = $this->validateRememberMeCookie($_COOKIE['remember_me']);
            
            if ($userId) {
                // Get the user
                $user = $this->getUserById($userId);
                
                if ($user) {
                    // Set session variables
                    $this->setUserSession($user);
                    
                    // Update last login time
                    $this->updateLastLogin($user['id']);
                    
                    return true;
                }
            }
            
            // Invalid cookie, clear it
            $this->clearRememberMeCookie();
        }
        
        return false;
    }
    
    /**
     * Get the current user
     * 
     * @return array|null The current user data or null if not logged in
     */
    public function getCurrentUser() {
        if (self::$currentUser !== null) {
            return self::$currentUser;
        }
        
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        // Get the user from the database
        self::$currentUser = $this->getUserById($_SESSION['user_id']);
        
        return self::$currentUser;
    }
    
    /**
     * Get a user by ID
     * 
     * @param int $id The user ID
     * @return array|null The user data or null if not found
     */
    public function getUserById($id) {
        return $this->db->queryOne("SELECT * FROM users WHERE id = ?", [$id]);
    }
    
    /**
     * Get a user by email
     * 
     * @param string $email The user email
     * @return array|null The user data or null if not found
     */
    public function getUserByEmail($email) {
        return $this->db->queryOne("SELECT * FROM users WHERE email = ?", [$email]);
    }
    
    /**
     * Get a user by username
     * 
     * @param string $username The username
     * @return array|null The user data or null if not found
     */
    public function getUserByUsername($username) {
        return $this->db->queryOne("SELECT * FROM users WHERE username = ?", [$username]);
    }
    
    /**
     * Update a user's password
     * 
     * @param int $userId The user ID
     * @param string $currentPassword The current password
     * @param string $newPassword The new password
     * @return bool True if successful, false otherwise
     */
    public function updatePassword($userId, $currentPassword, $newPassword) {
        // Get the user
        $user = $this->getUserById($userId);
        
        if (!$user) {
            return false;
        }
        
        // Verify the current password
        if (!password_verify($currentPassword, $user['password'])) {
            return false;
        }
        
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
        
        // Update the password
        return $this->db->update('users', ['password' => $hashedPassword, 'updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$userId]) > 0;
    }
    
    /**
     * Reset a user's password
     * 
     * @param string $email The user's email
     * @param string $token The reset token (selector:token)
     * @param string $newPassword The new password
     * @return bool True if successful, false otherwise
     */
    public function resetPassword($email, $token, $newPassword) {
        // Get the user
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        // Verify the token
        $parts = explode(':', $token);
        
        if (count($parts) !== 2) {
            return false;
        }
        
        $selector = $parts[0];
        $tokenValue = $parts[1];
        
        // Hash the token for comparison
        $hashedToken = hash('sha256', $tokenValue);
        
        // Get the token from the database
        $tokenData = $this->db->queryOne(
            "SELECT * FROM user_tokens WHERE selector = ? AND user_id = ? AND type = 'password_reset' AND expires > NOW()",
            [$selector, $user['id']]
        );
        
        if (!$tokenData) {
            return false;
        }
        
        // Verify the token
        if (!hash_equals($tokenData['token'], $hashedToken)) {
            return false;
        }
        
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
        
        // Start a transaction
        $this->db->beginTransaction();
        
        try {
            // Update the password
            $updated = $this->db->update('users', 
                ['password' => $hashedPassword, 'updated_at' => date('Y-m-d H:i:s')], 
                'id = ?', 
                [$user['id']]
            ) > 0;
            
            // Delete the token
            $this->db->execute("DELETE FROM user_tokens WHERE id = ?", [$tokenData['id']]);
            
            // Commit the transaction
            $this->db->commit();
            
            return $updated;
        } catch (Exception $e) {
            // Rollback the transaction
            $this->db->rollBack();
            error_log("Password reset error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update a user's profile
     * 
     * @param int $userId The user ID
     * @param array $data The profile data
     * @return bool True if successful, false otherwise
     */
    public function updateProfile($userId, $data) {
        // Set the updated_at timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        // Update the user
        return $this->db->update('users', $data, 'id = ?', [$userId]) > 0;
    }
    
    /**
     * Generate a password reset token
     * 
     * @param string $email The user's email
     * @return string|bool The reset token (selector:token) or false on failure
     */
    public function generatePasswordResetToken($email) {
        // Get the user
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        // Generate a random token
        $token = bin2hex(random_bytes(32));
        $selector = bin2hex(random_bytes(16));
        
        // Hash the token for storage
        $hashedToken = hash('sha256', $token);
        
        // Set expiry date (24 hours)
        $expiry = time() + (24 * 60 * 60);
        $expiryDate = date('Y-m-d H:i:s', $expiry);
        
        // Delete any existing tokens for this user
        $this->db->execute("DELETE FROM user_tokens WHERE user_id = ? AND type = 'password_reset'", [$user['id']]);
        
        // Store the token in the database
        $inserted = $this->db->insert('user_tokens', [
            'user_id' => $user['id'],
            'selector' => $selector,
            'token' => $hashedToken,
            'expires' => $expiryDate,
            'type' => 'password_reset',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        if (!$inserted) {
            return false;
        }
        
        // Return the token
        return $selector . ':' . $token;
    }
    
    /**
     * Set the user session
     * 
     * @param array $user The user data
     * @return void
     */
    private function setUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
    }
    
    /**
     * Update the last login time for a user
     * 
     * @param int $userId The user ID
     * @return bool True if successful, false otherwise
     */
    private function updateLastLogin($userId) {
        return $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$userId]) > 0;
    }
    
    /**
     * Set the remember me cookie
     * 
     * @param int $userId The user ID
     * @return void
     */
    private function setRememberMeCookie($userId) {
        // Generate a random token
        $token = bin2hex(random_bytes(32));
        $selector = bin2hex(random_bytes(16));
        
        // Hash the token for storage
        $hashedToken = hash('sha256', $token);
        
        // Set expiry date
        $expiry = time() + (30 * 24 * 60 * 60); // 30 days
        $expiryDate = date('Y-m-d H:i:s', $expiry);
        
        // Delete any existing tokens for this user
        $this->db->execute("DELETE FROM user_tokens WHERE user_id = ? AND type = 'remember_me'", [$userId]);
        
        // Store the token in the database
        $this->db->insert('user_tokens', [
            'user_id' => $userId,
            'selector' => $selector,
            'token' => $hashedToken,
            'expires' => $expiryDate,
            'type' => 'remember_me',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Set the cookie with selector:token
        $cookieValue = $selector . ':' . $token;
        setcookie('remember_me', $cookieValue, $expiry, '/', '', false, true);
    }
    
    /**
     * Validate the remember me cookie
     * 
     * @param string $cookieValue The token from the cookie (selector:token)
     * @return int|bool The user ID if valid, false otherwise
     */
    private function validateRememberMeCookie($cookieValue) {
        // Split the cookie value
        $parts = explode(':', $cookieValue);
        
        if (count($parts) !== 2) {
            return false;
        }
        
        $selector = $parts[0];
        $token = $parts[1];
        
        // Hash the token for comparison
        $hashedToken = hash('sha256', $token);
        
        // Get the token from the database
        $tokenData = $this->db->queryOne(
            "SELECT * FROM user_tokens WHERE selector = ? AND type = 'remember_me' AND expires > NOW()",
            [$selector]
        );
        
        if (!$tokenData) {
            return false;
        }
        
        // Verify the token
        if (!hash_equals($tokenData['token'], $hashedToken)) {
            return false;
        }
        
        // Return the user ID
        return $tokenData['user_id'];
    }
    
    /**
     * Clear the remember me cookie
     * 
     * @return void
     */
    private function clearRememberMeCookie() {
        // Get the current user ID
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        if ($userId) {
            // Remove the token from the database
            $this->db->execute("DELETE FROM user_tokens WHERE user_id = ? AND type = 'remember_me'", [$userId]);
        } else if (isset($_COOKIE['remember_me'])) {
            // If no session but cookie exists, try to get the selector
            $parts = explode(':', $_COOKIE['remember_me']);
            if (count($parts) === 2) {
                $selector = $parts[0];
                // Remove the token from the database using selector
                $this->db->execute("DELETE FROM user_tokens WHERE selector = ? AND type = 'remember_me'", [$selector]);
            }
        }
        
        // Clear the cookie
        setcookie('remember_me', '', time() - 3600, '/', '', false, true);
    }
}
?>