<?php
/**
 * Admin Employees Controller
 * 
 * This controller handles the management of employees (admin, data entry, trainer).
 */

class AdminEmployeesController {
    /**
     * Display a list of employees
     */
    public function index() {
        global $conn;
        
        // Check if the current user has admin/manager role
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        
        if (!$currentAdmin || $currentAdmin['role'] !== 'admin') {
            // Set error message
            setFlashMessage('ليس لديك صلاحية للوصول إلى هذه الصفحة', 'danger');
            
            // Redirect to dashboard
            header('Location: /admin');
            exit;
        }
        
        // Get employees
        $employees = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT a.*, 
                       CASE 
                           WHEN a.role = 'admin' THEN 'مدير'
                           WHEN a.role = 'data_entry' THEN 'مدخل بيانات'
                           WHEN a.role = 'instructor' THEN 'مدرب'
                           ELSE a.role
                       END as role_name
                FROM admins a
                ORDER BY a.created_at DESC
            ");
            $stmt->execute();
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Set page title
        $pageTitle = 'إدارة الموظفين';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/employees/index.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the form to create a new employee
     */
    public function create() {
        // Check if the current user has admin/manager role
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        
        if (!$currentAdmin || $currentAdmin['role'] !== 'admin') {
            // Set error message
            setFlashMessage('ليس لديك صلاحية للوصول إلى هذه الصفحة', 'danger');
            
            // Redirect to dashboard
            header('Location: /admin');
            exit;
        }
        
        // Set page title
        $pageTitle = 'إضافة موظف جديد';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/employees/create.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Store a new employee
     */
    public function store() {
        global $conn;
        
        // Check if the current user has admin/manager role
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        
        if (!$currentAdmin || $currentAdmin['role'] !== 'admin') {
            // Set error message
            setFlashMessage('ليس لديك صلاحية للوصول إلى هذه الصفحة', 'danger');
            
            // Redirect to dashboard
            header('Location: /admin');
            exit;
        }
        
        // Validate input
        $errors = [];
        
        // Name is required
        if (empty($_POST['name'])) {
            $errors[] = 'الاسم مطلوب';
        }
        
        // Username is required
        if (empty($_POST['username'])) {
            $errors[] = 'اسم المستخدم مطلوب';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $_POST['username'])) {
            $errors[] = 'اسم المستخدم يجب أن يحتوي على أحرف وأرقام فقط';
        }
        
        // Email is required and must be valid
        if (empty($_POST['email'])) {
            $errors[] = 'البريد الإلكتروني مطلوب';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'البريد الإلكتروني غير صالح';
        }
        
        // Password is required and must be at least 8 characters
        if (empty($_POST['password'])) {
            $errors[] = 'كلمة المرور مطلوبة';
        } elseif (strlen($_POST['password']) < 8) {
            $errors[] = 'كلمة المرور يجب أن تكون على الأقل 8 أحرف';
        }
        
        // Role is required
        if (empty($_POST['role'])) {
            $errors[] = 'الدور مطلوب';
        }
        
        // If there are errors, redirect back with errors and input
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header('Location: /admin/employees/create');
            exit;
        }
        
        // Hash password
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Insert employee
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO admins (name, username, email, password, role, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['username'],
                    $_POST['email'],
                    $password,
                    $_POST['role']
                ]);
                
                // Set success message
                setFlashMessage('تم إضافة الموظف بنجاح', 'success');
                
                // Redirect to employees list
                header('Location: /admin/employees');
                exit;
            } catch (PDOException $e) {
                // Check if error is due to duplicate email
                if ($e->getCode() == 23000) {
                    $_SESSION['errors'] = ['البريد الإلكتروني مستخدم بالفعل'];
                } else {
                    $_SESSION['errors'] = ['حدث خطأ أثناء إضافة الموظف'];
                }
                
                $_SESSION['form_data'] = $_POST;
                header('Location: /admin/employees/create');
                exit;
            }
        } else {
            // Set error message
            setFlashMessage('حدث خطأ أثناء إضافة الموظف', 'danger');
            
            // Redirect to employees list
            header('Location: /admin/employees');
            exit;
        }
    }
    
    /**
     * Display an employee
     * 
     * @param int $id The employee ID
     */
    public function view($id) {
        global $conn;
        
        // Get employee
        $employee = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT a.*, 
                       CASE 
                           WHEN a.role = 'admin' THEN 'مدير'
                           WHEN a.role = 'data_entry' THEN 'مدخل بيانات'
                           WHEN a.role = 'instructor' THEN 'مدرب'
                           ELSE a.role
                       END as role_name
                FROM admins a
                WHERE a.id = ?
            ");
            $stmt->execute([$id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        if (!$employee) {
            // Set error message
            setFlashMessage('الموظف غير موجود', 'danger');
            
            // Redirect to employees list
            header('Location: /admin/employees');
            exit;
        }
        
        // Set page title
        $pageTitle = 'عرض الموظف: ' . $employee['name'];
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/employees/view.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the form to edit an employee
     * 
     * @param int $id The employee ID
     */
    public function edit($id) {
        global $conn;
        
        // Check if the current user has admin/manager role
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        
        if (!$currentAdmin || $currentAdmin['role'] !== 'admin') {
            // Set error message
            setFlashMessage('ليس لديك صلاحية للوصول إلى هذه الصفحة', 'danger');
            
            // Redirect to dashboard
            header('Location: /admin');
            exit;
        }
        
        // Get employee
        $employee = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT *
                FROM admins
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        if (!$employee) {
            // Set error message
            setFlashMessage('الموظف غير موجود', 'danger');
            
            // Redirect to employees list
            header('Location: /admin/employees');
            exit;
        }
        
        // Set page title
        $pageTitle = 'تعديل الموظف';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/employees/edit.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Update an employee
     * 
     * @param int $id The employee ID
     */
    public function update($id) {
        global $conn;
        
        // Check if the current user has admin/manager role
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        
        if (!$currentAdmin || $currentAdmin['role'] !== 'admin') {
            // Set error message
            setFlashMessage('ليس لديك صلاحية للوصول إلى هذه الصفحة', 'danger');
            
            // Redirect to dashboard
            header('Location: /admin');
            exit;
        }
        
        // Get employee
        $employee = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT *
                FROM admins
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        if (!$employee) {
            // Set error message
            setFlashMessage('الموظف غير موجود', 'danger');
            
            // Redirect to employees list
            header('Location: /admin/employees');
            exit;
        }
        
        // Validate input
        $errors = [];
        
        // Name is required
        if (empty($_POST['name'])) {
            $errors[] = 'الاسم مطلوب';
        }
        
        // Username is required
        if (empty($_POST['username'])) {
            $errors[] = 'اسم المستخدم مطلوب';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $_POST['username'])) {
            $errors[] = 'اسم المستخدم يجب أن يحتوي على أحرف وأرقام فقط';
        }
        
        // Email is required and must be valid
        if (empty($_POST['email'])) {
            $errors[] = 'البريد الإلكتروني مطلوب';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'البريد الإلكتروني غير صالح';
        }
        
        // Password must be at least 8 characters if provided
        if (!empty($_POST['password']) && strlen($_POST['password']) < 8) {
            $errors[] = 'كلمة المرور يجب أن تكون على الأقل 8 أحرف';
        }
        
        // Role is required
        if (empty($_POST['role'])) {
            $errors[] = 'الدور مطلوب';
        }
        
        // If there are errors, redirect back with errors and input
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header('Location: /admin/employees/edit/' . $id);
            exit;
        }
        
        // Update employee
        if ($conn) {
            try {
                // If password is provided, update it
                if (!empty($_POST['password'])) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    
                    $stmt = $conn->prepare("
                        UPDATE admins
                        SET name = ?, username = ?, email = ?, password = ?, role = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $_POST['name'],
                        $_POST['username'],
                        $_POST['email'],
                        $password,
                        $_POST['role'],
                        $id
                    ]);
                } else {
                    $stmt = $conn->prepare("
                        UPDATE admins
                        SET name = ?, username = ?, email = ?, role = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $_POST['name'],
                        $_POST['username'],
                        $_POST['email'],
                        $_POST['role'],
                        $id
                    ]);
                }
                
                // Set success message
                setFlashMessage('تم تحديث الموظف بنجاح', 'success');
                
                // Redirect to employees list
                header('Location: /admin/employees');
                exit;
            } catch (PDOException $e) {
                // Check if error is due to duplicate email
                if ($e->getCode() == 23000) {
                    $_SESSION['errors'] = ['البريد الإلكتروني مستخدم بالفعل'];
                } else {
                    $_SESSION['errors'] = ['حدث خطأ أثناء تحديث الموظف'];
                }
                
                $_SESSION['form_data'] = $_POST;
                header('Location: /admin/employees/edit/' . $id);
                exit;
            }
        } else {
            // Set error message
            setFlashMessage('حدث خطأ أثناء تحديث الموظف', 'danger');
            
            // Redirect to employees list
            header('Location: /admin/employees');
            exit;
        }
    }
    
    /**
     * Delete an employee
     * 
     * @param int $id The employee ID
     */
    public function delete($id) {
        global $conn;
        
        // Check if the current user has admin/manager role
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        
        if (!$currentAdmin || $currentAdmin['role'] !== 'admin') {
            // Set error message
            setFlashMessage('ليس لديك صلاحية للوصول إلى هذه الصفحة', 'danger');
            
            // Redirect to dashboard
            header('Location: /admin');
            exit;
        }
        
        // Get employee
        $employee = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT *
                FROM admins
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        if (!$employee) {
            // Set error message
            setFlashMessage('الموظف غير موجود', 'danger');
            
            // Redirect to employees list
            header('Location: /admin/employees');
            exit;
        }
        
        // Delete employee
        if ($conn) {
            $stmt = $conn->prepare("
                DELETE FROM admins
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            // Set success message
            setFlashMessage('تم حذف الموظف بنجاح', 'success');
        } else {
            // Set error message
            setFlashMessage('حدث خطأ أثناء حذف الموظف', 'danger');
        }
        
        // Redirect to employees list
        header('Location: /admin/employees');
        exit;
    }
}