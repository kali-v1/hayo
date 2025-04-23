<?php
/**
 * Admin Users Controller
 * 
 * This controller handles user management in the admin panel.
 */
class AdminUsersController {
    /**
     * Display a list of users
     */
    public function index() {
        global $conn;
        
        // Set page title
        $pageTitle = "إدارة المستخدمين";
        
        // Get users
        $users = [];
        if ($conn) {
            // Debug the query
            error_log("Executing users query");
            
            $stmt = $conn->prepare("
                SELECT DISTINCT id, username, email, first_name, last_name, 
                       is_active as status, created_at 
                FROM users 
                ORDER BY created_at DESC
            ");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug the results
            error_log("Found " . count($users) . " users");
            
            // Map is_active to status
            foreach ($users as &$user) {
                $user['role'] = 'user'; // Default role for all users
                $user['status'] = $user['status'] ? 'active' : 'inactive';
            }
            
            // Debug the final users array
            error_log("Final users array has " . count($users) . " items");
        } else {
            // Sample data - only used when no database connection
            $users = [
                [
                    'id' => 1,
                    'username' => 'john_doe',
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'role' => 'user',
                    'status' => 'active',
                    'created_at' => '2023-07-15 10:30:00'
                ],
                [
                    'id' => 2,
                    'username' => 'jane_smith',
                    'email' => 'jane@example.com',
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'role' => 'user',
                    'status' => 'active',
                    'created_at' => '2023-07-14 14:45:00'
                ],
                [
                    'id' => 3,
                    'username' => 'mohammed_ali',
                    'email' => 'mohammed@example.com',
                    'first_name' => 'Mohammed',
                    'last_name' => 'Ali',
                    'role' => 'user',
                    'status' => 'active',
                    'created_at' => '2023-07-13 09:15:00'
                ],
                [
                    'id' => 4,
                    'username' => 'sarah_johnson',
                    'email' => 'sarah@example.com',
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'role' => 'user',
                    'status' => 'inactive',
                    'created_at' => '2023-07-12 16:20:00'
                ],
                [
                    'id' => 5,
                    'username' => 'david_brown',
                    'email' => 'david@example.com',
                    'first_name' => 'David',
                    'last_name' => 'Brown',
                    'role' => 'user',
                    'status' => 'active',
                    'created_at' => '2023-07-11 11:10:00'
                ]
            ];
        }
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/users/index.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    // Rest of the controller methods...
}
?>