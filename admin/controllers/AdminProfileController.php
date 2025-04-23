<?php
/**
 * Admin Profile Controller
 * 
 * This controller handles admin profile management.
 */
class AdminProfileController {
    /**
     * Display the admin profile page
     */
    public function index() {
        global $conn;
        
        // Get admin ID from session
        $admin_id = $_SESSION['admin_id'] ?? 0;
        
        // Get admin details
        $admin = null;
        if ($conn && $admin_id > 0) {
            $stmt = $conn->prepare("
                SELECT *
                FROM admins
                WHERE id = ?
            ");
            $stmt->execute([$admin_id]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $admin = [
                'id' => 1,
                'username' => 'admin',
                'email' => 'admin@example.com',
                'first_name' => 'مدير',
                'last_name' => 'النظام',
                'role' => 'admin',
                'created_at' => '2023-07-01 10:00:00'
            ];
        }
        
        if (!$admin) {
            // Redirect to login page
            header('Location: /admin/logout');
            exit;
        }
        
        // Set page title
        $pageTitle = 'الملف الشخصي';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/profile/index.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Update admin profile
     */
    public function update() {
        global $conn;
        
        // Get admin ID from session
        $admin_id = $_SESSION['admin_id'] ?? 0;
        
        if (!$admin_id) {
            // Redirect to login page
            header('Location: /admin/logout');
            exit;
        }
        
        // Validate input
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        $errors = [];
        
        if (empty($first_name)) {
            $errors[] = 'الاسم الأول مطلوب';
        }
        
        if (empty($last_name)) {
            $errors[] = 'الاسم الأخير مطلوب';
        }
        
        if (empty($email)) {
            $errors[] = 'البريد الإلكتروني مطلوب';
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'البريد الإلكتروني غير صالح';
        }
        
        // Check if email already exists (excluding current admin)
        if ($conn && empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
            $stmt->execute([$email, $admin_id]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = 'البريد الإلكتروني مستخدم بالفعل';
            }
        }
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            
            // Redirect back to the profile page
            header('Location: /admin/profile');
            exit;
        }
        
        // Update admin profile
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    UPDATE admins
                    SET first_name = ?, last_name = ?, email = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $first_name,
                    $last_name,
                    $email,
                    $admin_id
                ]);
                
                // Set success message
                setFlashMessage('تم تحديث الملف الشخصي بنجاح', 'success');
            } catch (PDOException $e) {
                // Log the error
                error_log("Error updating admin profile: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء تحديث الملف الشخصي', 'danger');
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم تحديث الملف الشخصي بنجاح (وضع العرض)', 'success');
        }
        
        // Redirect back to the profile page
        header('Location: /admin/profile');
        exit;
    }
    
    /**
     * Update admin password
     */
    public function updatePassword() {
        global $conn;
        
        // Get admin ID from session
        $admin_id = $_SESSION['admin_id'] ?? 0;
        
        if (!$admin_id) {
            // Redirect to login page
            header('Location: /admin/logout');
            exit;
        }
        
        // Validate input
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (empty($current_password)) {
            $errors[] = 'كلمة المرور الحالية مطلوبة';
        }
        
        if (empty($new_password)) {
            $errors[] = 'كلمة المرور الجديدة مطلوبة';
        } else if (strlen($new_password) < 8) {
            $errors[] = 'كلمة المرور الجديدة يجب أن تكون على الأقل 8 أحرف';
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = 'كلمة المرور الجديدة وتأكيدها غير متطابقين';
        }
        
        // Verify current password
        if ($conn && empty($errors)) {
            $stmt = $conn->prepare("SELECT password FROM admins WHERE id = ?");
            $stmt->execute([$admin_id]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$admin || !password_verify($current_password, $admin['password'])) {
                $errors[] = 'كلمة المرور الحالية غير صحيحة';
            }
        }
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['password_errors'] = $errors;
            
            // Redirect back to the profile page
            header('Location: /admin/profile');
            exit;
        }
        
        // Update admin password
        if ($conn) {
            try {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("
                    UPDATE admins
                    SET password = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $hashed_password,
                    $admin_id
                ]);
                
                // Set success message
                setFlashMessage('تم تحديث كلمة المرور بنجاح', 'success');
            } catch (PDOException $e) {
                // Log the error
                error_log("Error updating admin password: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء تحديث كلمة المرور', 'danger');
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم تحديث كلمة المرور بنجاح (وضع العرض)', 'success');
        }
        
        // Redirect back to the profile page
        header('Location: /admin/profile');
        exit;
    }
}