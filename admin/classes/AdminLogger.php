<?php
/**
 * Admin Logger Class
 * 
 * This class handles logging of admin user activities
 */
class AdminLogger {
    private $conn;
    
    /**
     * Constructor
     * 
     * @param PDO $conn Database connection
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Log an admin activity
     * 
     * @param int $adminId The admin ID
     * @param string $adminUsername The admin username
     * @param string $action The action performed (login, logout, add, update, delete, etc.)
     * @param string $section The section where the action was performed (users, courses, exams, etc.)
     * @param string|array $details Additional details about the action
     * @return bool Whether the logging was successful
     */
    public function log($adminId, $adminUsername, $action, $section, $details = null) {
        // Convert details to JSON if it's an array
        if (is_array($details)) {
            $details = json_encode($details, JSON_UNESCAPED_UNICODE);
        }
        
        // Get IP address
        $ipAddress = $this->getIpAddress();
        
        // Get user agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO admin_activity_logs 
                (admin_id, admin_username, action, section, details, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $adminId,
                $adminUsername,
                $action,
                $section,
                $details,
                $ipAddress,
                $userAgent
            ]);
        } catch (PDOException $e) {
            // Log the error to the PHP error log
            error_log("Error logging admin activity: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get the client's IP address
     * 
     * @return string The IP address
     */
    private function getIpAddress() {
        // Check for proxy forwarded IP
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddresses = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ipAddresses[0]);
        }
        
        // Check for other common IP headers
        $ipHeaders = ['HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        foreach ($ipHeaders as $header) {
            if (!empty($_SERVER[$header])) {
                return $_SERVER[$header];
            }
        }
        
        return 'Unknown';
    }
    
    /**
     * Get activity logs with pagination and filtering
     * 
     * @param int $page The current page number
     * @param int $perPage The number of items per page
     * @param array $filters Optional filters (admin_id, action, section, date_from, date_to)
     * @return array The logs and pagination data
     */
    public function getLogs($page = 1, $perPage = 20, $filters = []) {
        $offset = ($page - 1) * $perPage;
        $params = [];
        
        // Build the query with filters
        $query = "
            SELECT * FROM admin_activity_logs
            WHERE 1=1
        ";
        
        $countQuery = "
            SELECT COUNT(*) as total FROM admin_activity_logs
            WHERE 1=1
        ";
        
        // Add filters
        if (!empty($filters['admin_id'])) {
            $query .= " AND admin_id = ?";
            $countQuery .= " AND admin_id = ?";
            $params[] = $filters['admin_id'];
        }
        
        if (!empty($filters['admin_username'])) {
            $query .= " AND admin_username LIKE ?";
            $countQuery .= " AND admin_username LIKE ?";
            $params[] = "%" . $filters['admin_username'] . "%";
        }
        
        if (!empty($filters['action'])) {
            $query .= " AND action = ?";
            $countQuery .= " AND action = ?";
            $params[] = $filters['action'];
        }
        
        if (!empty($filters['section'])) {
            $query .= " AND section = ?";
            $countQuery .= " AND section = ?";
            $params[] = $filters['section'];
        }
        
        if (!empty($filters['date_from'])) {
            $query .= " AND created_at >= ?";
            $countQuery .= " AND created_at >= ?";
            $params[] = $filters['date_from'] . " 00:00:00";
        }
        
        if (!empty($filters['date_to'])) {
            $query .= " AND created_at <= ?";
            $countQuery .= " AND created_at <= ?";
            $params[] = $filters['date_to'] . " 23:59:59";
        }
        
        // Add order and limit
        $query .= " ORDER BY created_at DESC LIMIT $offset, $perPage";
        
        try {
            // Get total count
            $countStmt = $this->conn->prepare($countQuery);
            $countStmt->execute($params);
            $totalLogs = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Get logs
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate pagination data
            $totalPages = ceil($totalLogs / $perPage);
            
            return [
                'logs' => $logs,
                'pagination' => [
                    'total' => $totalLogs,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'total_pages' => $totalPages
                ]
            ];
        } catch (PDOException $e) {
            error_log("Error getting admin activity logs: " . $e->getMessage());
            return [
                'logs' => [],
                'pagination' => [
                    'total' => 0,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'total_pages' => 0
                ]
            ];
        }
    }
}