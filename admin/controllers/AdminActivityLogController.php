<?php
/**
 * Admin Activity Log Controller
 * 
 * This controller handles the viewing of admin activity logs
 */
class AdminActivityLogController {
    /**
     * Display a list of activity logs
     */
    public function index() {
        global $conn;
        
        // Check if the current user has admin role
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        
        if (!$currentAdmin || $currentAdmin['role'] !== 'admin') {
            // Set error message
            setFlashMessage('ليس لديك صلاحية للوصول إلى هذه الصفحة', 'danger');
            
            // Redirect to dashboard
            header('Location: /admin');
            exit;
        }
        
        // Pagination settings
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        
        // Get filters
        $filters = [
            'admin_username' => isset($_GET['admin_username']) ? trim($_GET['admin_username']) : '',
            'action' => isset($_GET['action']) ? trim($_GET['action']) : '',
            'section' => isset($_GET['section']) ? trim($_GET['section']) : '',
            'date_from' => isset($_GET['date_from']) ? trim($_GET['date_from']) : '',
            'date_to' => isset($_GET['date_to']) ? trim($_GET['date_to']) : ''
        ];
        
        // Get logs
        $logger = new AdminLogger($conn);
        $result = $logger->getLogs($page, $perPage, $filters);
        
        $logs = $result['logs'];
        $pagination = $result['pagination'];
        
        // Get unique actions and sections for filter dropdowns
        $actions = [];
        $sections = [];
        
        if ($conn) {
            $actionsStmt = $conn->query("SELECT DISTINCT action FROM admin_activity_logs ORDER BY action");
            $actions = $actionsStmt->fetchAll(PDO::FETCH_COLUMN);
            
            $sectionsStmt = $conn->query("SELECT DISTINCT section FROM admin_activity_logs ORDER BY section");
            $sections = $sectionsStmt->fetchAll(PDO::FETCH_COLUMN);
        }
        
        // Set page title
        $pageTitle = 'سجل نشاطات المشرفين';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/activity_logs/index.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
}