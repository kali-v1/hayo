<?php
/**
 * Admin Users Controller
 * 
 * This controller handles user management in the admin panel.
 */
class AdminUsersController {
    /**
     * API endpoint for user search suggestions
     */
    public function searchSuggestions() {
        global $conn;
        
        // Get search term
        $search = isset($_GET['term']) ? trim($_GET['term']) : '';
        
        // Return empty array if search term is empty or too short
        if (empty($search) || strlen($search) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
        
        $suggestions = [];
        
        if ($conn) {
            // Query for suggestions
            $query = "SELECT id, username, email, CONCAT(first_name, ' ', last_name) as full_name 
                      FROM users 
                      WHERE username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ? 
                      LIMIT 10";
            
            $stmt = $conn->prepare($query);
            $searchParam = "%$search%";
            $stmt->execute([$searchParam, $searchParam, $searchParam, $searchParam]);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $result) {
                $suggestions[] = [
                    'id' => $result['id'],
                    'value' => $result['username'],
                    'label' => $result['username'] . ' (' . $result['email'] . ')',
                    'email' => $result['email'],
                    'full_name' => $result['full_name']
                ];
            }
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($suggestions);
        exit;
    }
    /**
     * Display a list of users with pagination and search/filter
     */
    public function index() {
        global $conn;
        
        // Set page title
        $pageTitle = "إدارة المستخدمين";
        
        // Pagination settings
        $itemsPerPage = 20;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        $offset = ($currentPage - 1) * $itemsPerPage;
        
        // Search and filter parameters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
        
        // Get users with pagination and filtering
        $users = [];
        $totalUsers = 0;
        
        if ($conn) {
            // Build the query with filters
            $query = "SELECT DISTINCT id, username, email, first_name, last_name, 
                      is_active as status, created_at 
                      FROM users WHERE 1=1";
            $countQuery = "SELECT COUNT(DISTINCT id) as total FROM users WHERE 1=1";
            $params = [];
            
            // Add search condition if search term is provided
            if (!empty($search)) {
                $searchCondition = " AND (username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
                $query .= $searchCondition;
                $countQuery .= $searchCondition;
                $searchParam = "%$search%";
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
            }
            
            // Add status filter if provided
            if ($statusFilter !== '') {
                $statusValue = ($statusFilter === 'active') ? 1 : 0;
                $query .= " AND is_active = ?";
                $countQuery .= " AND is_active = ?";
                $params[] = $statusValue;
            }
            
            // Get total count for pagination
            $countStmt = $conn->prepare($countQuery);
            $countStmt->execute($params);
            $totalUsers = $countStmt->fetchColumn();
            
            // Add sorting and pagination
            $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $itemsPerPage;
            $params[] = $offset;
            
            // Execute the main query
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Map is_active to status
            foreach ($users as &$user) {
                $user['role'] = 'user'; // Default role for all users
                $user['status'] = $user['status'] ? 'active' : 'inactive';
            }
            // Unset the reference to avoid issues with subsequent loops
            unset($user);
        } else {
            // Sample data for testing without database
            $allUsers = [
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
            
            // Apply search filter to sample data
            if (!empty($search)) {
                $allUsers = array_filter($allUsers, function($user) use ($search) {
                    $search = strtolower($search);
                    return strpos(strtolower($user['username']), $search) !== false ||
                           strpos(strtolower($user['email']), $search) !== false ||
                           strpos(strtolower($user['first_name']), $search) !== false ||
                           strpos(strtolower($user['last_name']), $search) !== false;
                });
            }
            
            // Apply status filter to sample data
            if ($statusFilter !== '') {
                $allUsers = array_filter($allUsers, function($user) use ($statusFilter) {
                    return $user['status'] === $statusFilter;
                });
            }
            
            // Count total users for pagination
            $totalUsers = count($allUsers);
            
            // Apply pagination to sample data
            $users = array_slice($allUsers, $offset, $itemsPerPage);
        }
        
        // Calculate pagination data
        $totalPages = ceil($totalUsers / $itemsPerPage);
        if ($currentPage > $totalPages && $totalPages > 0) {
            $currentPage = $totalPages;
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
    
    /**
     * Display the form to create a new user
     */
    public function create() {
        // Set page title
        $pageTitle = 'إضافة مستخدم جديد';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/users/create.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Store a new user
     */
    public function store() {
        global $conn;
        
        // Validate input
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $mobile_number = trim($_POST['mobile_number'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $status = $_POST['status'] ?? 'active';
        
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'اسم المستخدم مطلوب';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'اسم المستخدم يجب أن يكون بين 3 و 50 حرفًا';
        }
        
        if (empty($email)) {
            $errors[] = 'البريد الإلكتروني مطلوب';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'البريد الإلكتروني غير صالح';
        }
        
        if (empty($password)) {
            $errors[] = 'كلمة المرور مطلوبة';
        } elseif (strlen($password) < 8) {
            $errors[] = 'كلمة المرور يجب أن تكون على الأقل 8 أحرف';
        }
        
        if ($password !== $confirm_password) {
            $errors[] = 'كلمة المرور وتأكيد كلمة المرور غير متطابقين';
        }
        
        if (empty($first_name)) {
            $errors[] = 'الاسم الأول مطلوب';
        }
        
        if (empty($last_name)) {
            $errors[] = 'الاسم الأخير مطلوب';
        }
        
        // Check if username or email already exists
        if ($conn && empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = 'اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل';
            }
        }
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            
            // Redirect back to the form
            header('Location: /admin/users/create');
            exit;
        }
        
        // Create user
        if ($conn) {
            try {
                // Convert status to is_active
                $is_active = ($status === 'active') ? 1 : 0;
                
                $stmt = $conn->prepare("
                    INSERT INTO users (username, email, password, first_name, last_name, mobile_number, is_active, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt->execute([
                    $username,
                    $email,
                    $hashed_password,
                    $first_name,
                    $last_name,
                    $mobile_number,
                    $is_active
                ]);
                
                // Get the new user ID
                $userId = $conn->lastInsertId();
                
                // Log the user creation
                $adminId = $_SESSION['admin_id'];
                $adminUsername = $_SESSION['admin_username'];
                $logger = new AdminLogger($conn);
                $logger->log($adminId, $adminUsername, 'add', 'users', [
                    'user_id' => $userId,
                    'username' => $username,
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'status' => $is_active ? 'active' : 'inactive'
                ]);
                
                // Set success message
                setFlashMessage('تم إنشاء المستخدم بنجاح', 'success');
                
                // Redirect to users list
                header('Location: /admin/users');
                exit;
            } catch (PDOException $e) {
                // Log the error
                error_log("Error creating user: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء إنشاء المستخدم', 'danger');
                
                // Redirect back to the form
                header('Location: /admin/users/create');
                exit;
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم إنشاء المستخدم بنجاح (وضع العرض)', 'success');
            
            // Redirect to users list
            header('Location: /admin/users');
            exit;
        }
    }
    
    /**
     * Display a user
     * 
     * @param int $id The user ID
     */
    public function view($id) {
        global $conn;
        
        // Get user
        $user = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT id, username, email, first_name, last_name, profile_image, is_active, bio, last_login, created_at, updated_at, mobile_number
                FROM users
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Add role and status fields
            if ($user) {
                $user['role'] = 'user'; // Default role for all users
                $user['status'] = $user['is_active'] ? 'active' : 'inactive';
            }
        } else {
            // Sample data
            $users = [
                1 => [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'username' => 'johndoe',
                    'status' => 'active',
                    'created_at' => '2023-07-01 10:00:00'
                ],
                2 => [
                    'id' => 2,
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'username' => 'janesmith',
                    'status' => 'active',
                    'created_at' => '2023-07-02 11:30:00'
                ]
            ];
            
            $user = $users[$id] ?? null;
        }
        
        if (!$user) {
            // Set error message
            setFlashMessage('المستخدم غير موجود', 'danger');
            
            // Redirect to users list
            header('Location: /admin/users');
            exit;
        }
        
        // Get user enrollments
        $enrollments = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT e.*, c.title as course_title
                FROM enrollments e
                LEFT JOIN courses c ON e.course_id = c.id
                WHERE e.user_id = ?
                ORDER BY e.created_at DESC
            ");
            $stmt->execute([$id]);
            $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            if ($id == 1) {
                $enrollments = [
                    [
                        'id' => 1,
                        'user_id' => 1,
                        'course_id' => 1,
                        'course_title' => 'CCNA Certification',
                        'status' => 'active',
                        'created_at' => '2023-07-05 09:15:00'
                    ],
                    [
                        'id' => 2,
                        'user_id' => 1,
                        'course_id' => 2,
                        'course_title' => 'Security+ Certification',
                        'status' => 'active',
                        'created_at' => '2023-07-07 14:30:00'
                    ]
                ];
            } else if ($id == 2) {
                $enrollments = [
                    [
                        'id' => 3,
                        'user_id' => 2,
                        'course_id' => 1,
                        'course_title' => 'CCNA Certification',
                        'status' => 'active',
                        'created_at' => '2023-07-06 10:45:00'
                    ]
                ];
            }
        }
        
        // Set page title
        $pageTitle = 'عرض المستخدم: ';
        if (isset($user['first_name']) && isset($user['last_name'])) {
            $pageTitle .= $user['first_name'] . ' ' . $user['last_name'];
        } elseif (isset($user['name'])) {
            $pageTitle .= $user['name'];
        } else {
            $pageTitle .= $user['username'] ?? '';
        }
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/users/view.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the form to edit a user
     * 
     * @param int $id The user ID
     */
    public function edit($id) {
        global $conn;
        
        // Get user
        $user = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT id, username, email, first_name, last_name, is_active, mobile_number
                FROM users
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Add role and status fields
            if ($user) {
                $user['role'] = 'user'; // Default role for all users
                $user['status'] = $user['is_active'] ? 'active' : 'inactive';
            }
        } else {
            // Sample data
            $users = [
                1 => [
                    'id' => 1,
                    'username' => 'john_doe',
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'role' => 'user',
                    'status' => 'active'
                ],
                2 => [
                    'id' => 2,
                    'username' => 'jane_smith',
                    'email' => 'jane@example.com',
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'role' => 'user',
                    'status' => 'active'
                ]
            ];
            
            $user = $users[$id] ?? null;
        }
        
        if (!$user) {
            // Set error message
            setFlashMessage('المستخدم غير موجود', 'danger');
            
            // Redirect to users list
            header('Location: /admin/users');
            exit;
        }
        
        // Set page title
        $pageTitle = 'تعديل المستخدم';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/users/edit.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Update a user
     * 
     * @param int $id The user ID
     */
    public function update($id) {
        global $conn;
        
        // Validate input
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $mobile_number = trim($_POST['mobile_number'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $status = $_POST['status'] ?? 'active';
        
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'اسم المستخدم مطلوب';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'اسم المستخدم يجب أن يكون بين 3 و 50 حرفًا';
        }
        
        if (empty($email)) {
            $errors[] = 'البريد الإلكتروني مطلوب';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'البريد الإلكتروني غير صالح';
        }
        
        if (!empty($password) && strlen($password) < 8) {
            $errors[] = 'كلمة المرور يجب أن تكون على الأقل 8 أحرف';
        }
        
        if (!empty($password) && $password !== $confirm_password) {
            $errors[] = 'كلمة المرور وتأكيد كلمة المرور غير متطابقين';
        }
        
        if (empty($first_name)) {
            $errors[] = 'الاسم الأول مطلوب';
        }
        
        if (empty($last_name)) {
            $errors[] = 'الاسم الأخير مطلوب';
        }
        
        // Check if username or email already exists (excluding current user)
        if ($conn && empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->execute([$username, $email, $id]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = 'اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل';
            }
        }
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            
            // Redirect back to the form
            header("Location: /admin/users/edit/$id");
            exit;
        }
        
        // Update user
        if ($conn) {
            try {
                // Convert status to is_active
                $is_active = ($status === 'active') ? 1 : 0;
                
                if (!empty($password)) {
                    // Update with password
                    $stmt = $conn->prepare("
                        UPDATE users
                        SET username = ?, email = ?, password = ?, first_name = ?, last_name = ?, mobile_number = ?, is_active = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt->execute([
                        $username,
                        $email,
                        $hashed_password,
                        $first_name,
                        $last_name,
                        $mobile_number,
                        $is_active,
                        $id
                    ]);
                } else {
                    // Update without password
                    $stmt = $conn->prepare("
                        UPDATE users
                        SET username = ?, email = ?, first_name = ?, last_name = ?, mobile_number = ?, is_active = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    
                    $stmt->execute([
                        $username,
                        $email,
                        $first_name,
                        $last_name,
                        $mobile_number,
                        $is_active,
                        $id
                    ]);
                }
                
                // Log the user update
                $adminId = $_SESSION['admin_id'];
                $adminUsername = $_SESSION['admin_username'];
                $logger = new AdminLogger($conn);
                $logger->log($adminId, $adminUsername, 'update', 'users', [
                    'user_id' => $id,
                    'username' => $username,
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'status' => $is_active ? 'active' : 'inactive',
                    'password_changed' => !empty($password)
                ]);
                
                // Set success message
                setFlashMessage('تم تحديث المستخدم بنجاح', 'success');
                
                // Redirect to users list
                header('Location: /admin/users');
                exit;
            } catch (PDOException $e) {
                // Log the error
                error_log("Error updating user: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء تحديث المستخدم', 'danger');
                
                // Redirect back to the form
                header("Location: /admin/users/edit/$id");
                exit;
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم تحديث المستخدم بنجاح (وضع العرض)', 'success');
            
            // Redirect to users list
            header('Location: /admin/users');
            exit;
        }
    }
    
    /**
     * Delete a user
     * 
     * @param int $id The user ID
     */
    public function delete($id) {
        global $conn;
        
        if ($conn) {
            try {
                // Get user info before deletion for logging
                $userStmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
                $userStmt->execute([$id]);
                $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                
                // Soft delete the user
                $stmt = $conn->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                // Log the user deletion
                $adminId = $_SESSION['admin_id'];
                $adminUsername = $_SESSION['admin_username'];
                $logger = new AdminLogger($conn);
                $logger->log($adminId, $adminUsername, 'delete', 'users', [
                    'user_id' => $id,
                    'username' => $user['username'] ?? 'unknown',
                    'email' => $user['email'] ?? 'unknown'
                ]);
                
                // Set success message
                setFlashMessage('تم حذف المستخدم بنجاح', 'success');
            } catch (PDOException $e) {
                // Log the error
                error_log("Error deleting user: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء حذف المستخدم', 'danger');
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم حذف المستخدم بنجاح (وضع العرض)', 'success');
        }
        
        // Redirect to users list
        header('Location: /admin/users');
        exit;
    }
}