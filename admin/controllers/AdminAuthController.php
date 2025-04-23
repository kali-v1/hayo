<?php
/**
 * Admin Auth Controller
 * 
 * This controller handles admin authentication.
 */
class AdminAuthController {
    /**
     * Display the login form
     */
    public function showLogin() {
        // Include the login template
        include ADMIN_ROOT . '/templates/login.php';
    }
    
    /**
     * Process the login form
     */
    public function login() {
        global $conn;
        
        // Create a debug log
        $logFile = fopen(APP_ROOT . '/logs/auth_debug.log', 'a');
        fwrite($logFile, "Login attempt at " . date('Y-m-d H:i:s') . "\n");
        
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = sanitizeInput($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);
            
            fwrite($logFile, "Username: $username\n");
            fwrite($logFile, "Remember: " . ($remember ? 'Yes' : 'No') . "\n");
            
            // Validate input
            if (empty($username) || empty($password)) {
                $error = 'يرجى إدخال اسم المستخدم وكلمة المرور';
                fwrite($logFile, "Error: Empty username or password\n");
                
                // Include login template with error message
                include ADMIN_ROOT . '/templates/login.php';
                fclose($logFile);
                return;
            }
            
            // Attempt to authenticate the admin
            $auth = new AdminAuth();
            
            // Get admin from database to check if exists
            $adminData = $auth->getAdminByUsername($username);
            if ($adminData) {
                fwrite($logFile, "Admin found in database: " . print_r($adminData, true) . "\n");
                
                // Check if password_verify works with the stored hash
                $passwordVerified = password_verify($password, $adminData['password']);
                fwrite($logFile, "Password verification result: " . ($passwordVerified ? 'Success' : 'Failed') . "\n");
            } else {
                fwrite($logFile, "Admin not found in database\n");
            }
            
            $loginResult = $auth->login($username, $password, $remember);
            fwrite($logFile, "Login result: " . ($loginResult ? 'Success' : 'Failed') . "\n");
            
            if ($loginResult) {
                // Set flash message
                setFlashMessage('تم تسجيل الدخول بنجاح', 'success');
                
                // Log the login activity
                $adminId = $_SESSION['admin_id'];
                $adminUsername = $_SESSION['admin_username'];
                $logger = new AdminLogger($conn);
                $logger->log($adminId, $adminUsername, 'login', 'auth', 'تم تسجيل الدخول بنجاح');
                fwrite($logFile, "Flash message set, redirecting to dashboard\n");
                
                // Check if session was set correctly
                fwrite($logFile, "Session data: " . print_r($_SESSION, true) . "\n");
                
                // Redirect to the dashboard
                header('Location: /admin');
                fclose($logFile);
                exit;
            } else {
                // Set error message
                $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
                fwrite($logFile, "Login failed, showing error\n");
                
                // Include login template with error message
                include ADMIN_ROOT . '/templates/login.php';
                fclose($logFile);
                return;
            }
        }
        
        fclose($logFile);
    }
    
    /**
     * Log the admin out
     */
    public function logout() {
        global $conn;
        
        // Log the logout activity if user is logged in
        if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_username'])) {
            $adminId = $_SESSION['admin_id'];
            $adminUsername = $_SESSION['admin_username'];
            $logger = new AdminLogger($conn);
            $logger->log($adminId, $adminUsername, 'logout', 'auth', 'تم تسجيل الخروج');
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to the login page
        header('Location: /admin/login');
        exit;
    }
}