<?php
/**
 * ActivityLogger Class
 * 
 * This class handles logging activities in the system.
 */
class ActivityLogger {
    /**
     * @var PDO The database connection
     */
    private $conn;
    
    /**
     * Constructor
     * 
     * @param PDO $conn The database connection
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Log an activity
     * 
     * @param int|null $userId The user ID (for front-end users)
     * @param int|null $adminId The admin ID (for admin users)
     * @param string $action The action performed (e.g., create_course, update_exam, delete_user)
     * @param string $description A description of the activity
     * @return bool True if successful, false otherwise
     */
    public function log($userId, $adminId, $action, $description) {
        try {
            // Get IP address and user agent
            $ipAddress = $this->getIpAddress();
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            // Prepare the SQL statement
            $stmt = $this->conn->prepare("
                INSERT INTO activity_logs 
                (user_id, admin_id, action, description, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            // Execute the statement
            $result = $stmt->execute([
                $userId,
                $adminId,
                $action,
                $description,
                $ipAddress,
                $userAgent
            ]);
            
            return $result;
        } catch (PDOException $e) {
            // Log the error
            error_log("Error logging activity: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log an admin activity
     * 
     * @param int $adminId The admin ID
     * @param string $action The action performed
     * @param string $description A description of the activity
     * @return bool True if successful, false otherwise
     */
    public function logAdmin($adminId, $action, $description) {
        return $this->log(null, $adminId, $action, $description);
    }
    
    /**
     * Log a user activity
     * 
     * @param int $userId The user ID
     * @param string $action The action performed
     * @param string $description A description of the activity
     * @return bool True if successful, false otherwise
     */
    public function logUser($userId, $action, $description) {
        return $this->log($userId, null, $action, $description);
    }
    
    /**
     * Get the client's IP address
     * 
     * @return string The IP address
     */
    private function getIpAddress() {
        // Check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        
        // Check for IPs passing through proxies
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Can include multiple IPs, first one is the client's
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ipList[0]);
        }
        
        // If no proxy, get the standard remote address
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    /**
     * Get recent activities
     * 
     * @param int|null $userId Filter by user ID
     * @param int|null $adminId Filter by admin ID
     * @param int $limit Maximum number of activities to return
     * @return array The activities
     */
    public function getRecentActivities($userId = null, $adminId = null, $limit = 10) {
        try {
            $params = [];
            $sql = "SELECT * FROM activity_logs WHERE 1=1";
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            if ($adminId !== null) {
                $sql .= " AND admin_id = ?";
                $params[] = $adminId;
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT ?";
            $params[] = $limit;
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log the error
            error_log("Error getting recent activities: " . $e->getMessage());
            return [];
        }
    }
}