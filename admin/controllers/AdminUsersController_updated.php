<?php
/**
 * Admin Users Controller
 * 
 * This controller handles all user-related operations in the admin panel.
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
            $query = "SELECT id, username, email, CONCAT(first_name, ' ', last_name) as full_name, mobile_number
                      FROM users 
                      WHERE (username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR mobile_number LIKE ?)
                      AND deleted_at IS NULL
                      LIMIT 10";
            
            $stmt = $conn->prepare($query);
            $searchParam = "%$search%";
            $stmt->execute([$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $result) {
                $suggestions[] = [
                    'id' => $result['id'],
                    'value' => $result['username'],
                    'label' => $result['username'] . ' (' . $result['email'] . ')',
                    'email' => $result['email'],
                    'full_name' => $result['full_name'],
                    'mobile_number' => $result['mobile_number']
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
        $offset = ($currentPage - 1) * $itemsPerPage;

        // Get search and filter parameters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

        // Get users with pagination and filtering
        $users = [];
        $totalUsers = 0;

        if ($conn) {
            // Build the query with filters
            $query = "SELECT DISTINCT id, username, email, first_name, last_name,
                      is_active as status, created_at, mobile_number
                      FROM users
                      WHERE deleted_at IS NULL";
            
            $countQuery = "SELECT COUNT(DISTINCT id) FROM users WHERE deleted_at IS NULL";
            
            $params = [];
            
            // Add search condition if search term is provided
            if (!empty($search)) {
                $searchCondition = " AND (username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR mobile_number LIKE ?)";
                $query .= $searchCondition;
                $countQuery .= $searchCondition;
                $searchParam = "%$search%";
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
            }

            // Add status filter if provided
            if ($statusFilter !== '') {
                $statusValue = ($statusFilter === 'active') ? 1 : 0;
                $query .= " AND is_active = ?";
                $countQuery .= " AND is_active = ?";
                $params[] = $statusValue;
            }
            
            // Add order by and limit
            $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $itemsPerPage;
            $params[] = $offset;
            
            // Get total count
            $countStmt = $conn->prepare($countQuery);
            $countStmt->execute($params);
            $totalUsers = $countStmt->fetchColumn();
            
            // Get users
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format user data
            foreach ($users as &$user) {
                // Convert is_active to status text
                $user['status'] = $user['status'] ? 'active' : 'inactive';
                
                // Get course count for each user
                $courseCountQuery = "SELECT COUNT(*) FROM enrollments WHERE user_id = ? AND deleted_at IS NULL";
                $courseCountStmt = $conn->prepare($courseCountQuery);
                $courseCountStmt->execute([$user['id']]);
                $user['course_count'] = $courseCountStmt->fetchColumn();
                
                // Get exam count for each user
                $examCountQuery = "SELECT COUNT(*) FROM exam_attempts WHERE user_id = ? AND deleted_at IS NULL";
                $examCountStmt = $conn->prepare($examCountQuery);
                $examCountStmt->execute([$user['id']]);
                $user['exam_count'] = $examCountStmt->fetchColumn();
            }
        } else {
            // Sample data for development/testing
            $allUsers = [
                [
                    'id' => 1,
                    'username' => 'johndoe',
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'status' => 'active',
                    'created_at' => '2023-01-15 10:30:00',
                    'mobile_number' => '+966501234567',
                    'course_count' => 3,
                    'exam_count' => 5
                ],
                [
                    'id' => 2,
                    'username' => 'janedoe',
                    'email' => 'jane@example.com',
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'status' => 'active',
                    'created_at' => '2023-01-20 14:45:00',
                    'mobile_number' => '+966501234568',
                    'course_count' => 2,
                    'exam_count' => 3
                ],
                [
                    'id' => 3,
                    'username' => 'bobsmith',
                    'email' => 'bob@example.com',
                    'first_name' => 'Bob',
                    'last_name' => 'Smith',
                    'status' => 'active',
                    'created_at' => '2023-02-05 09:15:00',
                    'mobile_number' => '+966501234569',
                    'course_count' => 1,
                    'exam_count' => 2
                ],
                [
                    'id' => 4,
                    'username' => 'alicesmith',
                    'email' => 'alice@example.com',
                    'first_name' => 'Alice',
                    'last_name' => 'Smith',
                    'status' => 'active',
                    'created_at' => '2023-02-10 16:20:00',
                    'mobile_number' => '+966501234570',
                    'course_count' => 4,
                    'exam_count' => 6
                ],
                [
                    'id' => 5,
                    'username' => 'mohammedali',
                    'email' => 'mohammed@example.com',
                    'first_name' => 'Mohammed',
                    'last_name' => 'Ali',
                    'status' => 'active',
                    'created_at' => '2023-03-01 11:10:00',
                    'mobile_number' => '+966501234571',
                    'course_count' => 2,
                    'exam_count' => 4
                ]
            ];
            
            // Apply search filter to sample data
            if (!empty($search)) {
                $allUsers = array_filter($allUsers, function($user) use ($search) {
                    $search = strtolower($search);
                    return strpos(strtolower($user['username']), $search) !== false ||
                           strpos(strtolower($user['email']), $search) !== false ||
                           strpos(strtolower($user['first_name']), $search) !== false ||
                           strpos(strtolower($user['last_name']), $search) !== false ||
                           strpos(strtolower($user['mobile_number'] ?? ''), $search) !== false;
                });
            }
            
            // Apply status filter to sample data
            if ($statusFilter !== '') {
                $allUsers = array_filter($allUsers, function($user) use ($statusFilter) {
                    return $user['status'] === $statusFilter;
                });
            }
            
            // Get total count
            $totalUsers = count($allUsers);
            
            // Apply pagination
            $users = array_slice($allUsers, $offset, $itemsPerPage);
        }
        
        // Calculate total pages
        $totalPages = ceil($totalUsers / $itemsPerPage);
        
        // Start output buffering
        ob_start();
        
        // Include the view
        include ADMIN_ROOT . '/templates/users/index.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }

    /**
     * Show the form for creating a new user
     */
    public function create() {
        // Set page title
        $pageTitle = "إضافة مستخدم جديد";
        
        // Start output buffering
        ob_start();
        
        // Include the view
        include ADMIN_ROOT . '/templates/users/create.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }

    /**
     * Store a newly created user in the database
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
        
        // Format mobile number with + prefix
        $mobile_number = trim($_POST['mobile_number'] ?? '');
        if (!empty($mobile_number) && $mobile_number[0] !== '+') {
            $mobile_number = '+' . $mobile_number;
        }
        
        $role = $_POST['role'] ?? 'user';
        $status = $_POST['status'] ?? 'active';
        
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'اسم المستخدم مطلوب';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'اسم المستخدم يجب أن يحتوي على أحرف وأرقام فقط';
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
            $checkQuery = "SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND deleted_at IS NULL";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->execute([$username, $email]);
            
            if ($checkStmt->fetchColumn() > 0) {
                $errors[] = 'اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل';
            }
        }
        
        // If there are errors, redirect back with errors and form data
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = [
                'username' => $username,
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'mobile_number' => $mobile_number,
                'role' => $role,
                'status' => $status
            ];
            
            header('Location: /admin/users/create');
            exit;
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Convert status to boolean
        $is_active = ($status === 'active') ? 1 : 0;
        
        // Insert user into database
        if ($conn) {
            try {
                $query = "
                    INSERT INTO users (username, email, password, first_name, last_name, mobile_number, is_active, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ";
                
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    $username,
                    $email,
                    $hashed_password,
                    $first_name,
                    $last_name,
                    $mobile_number,
                    $is_active
                ]);
                
                // Log activity
                $userId = $conn->lastInsertId();
                $this->logActivity('add', 'users', "المستخدم: $username");
                
                // Set success message
                $_SESSION['success'] = 'تم إضافة المستخدم بنجاح';
                
                // Redirect to users list
                header('Location: /admin/users');
                exit;
            } catch (PDOException $e) {
                // Log the error
                error_log("Error creating user: " . $e->getMessage());
                
                // Set error message
                $_SESSION['errors'] = ['حدث خطأ أثناء إضافة المستخدم. يرجى المحاولة مرة أخرى.'];
                
                // Redirect back with form data
                $_SESSION['form_data'] = [
                    'username' => $username,
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'mobile_number' => $mobile_number,
                    'role' => $role,
                    'status' => $status
                ];
                
                header('Location: /admin/users/create');
                exit;
            }
        } else {
            // Set success message (for development/testing)
            $_SESSION['success'] = 'تم إضافة المستخدم بنجاح (وضع التطوير)';
            
            // Redirect to users list
            header('Location: /admin/users');
            exit;
        }
    }

    /**
     * Display the specified user
     */
    public function view($id) {
        global $conn;
        
        // Get user data
        $user = null;
        $enrollments = [];
        
        if ($conn) {
            // Get user
            $query = "
                SELECT id, username, email, first_name, last_name, profile_image, is_active, bio, last_login, created_at, updated_at, mobile_number
                FROM users
                WHERE id = ? AND deleted_at IS NULL
            ";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Convert is_active to status text
                $user['status'] = $user['is_active'] ? 'active' : 'inactive';
                
                // Get user enrollments
                $enrollmentsQuery = "
                    SELECT e.id, e.course_id, e.created_at, e.status, c.title as course_title
                    FROM enrollments e
                    JOIN courses c ON e.course_id = c.id
                    WHERE e.user_id = ? AND e.deleted_at IS NULL
                    ORDER BY e.created_at DESC
                ";
                
                $enrollmentsStmt = $conn->prepare($enrollmentsQuery);
                $enrollmentsStmt->execute([$id]);
                $enrollments = $enrollmentsStmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get course count
                $courseCountQuery = "SELECT COUNT(*) FROM enrollments WHERE user_id = ? AND deleted_at IS NULL";
                $courseCountStmt = $conn->prepare($courseCountQuery);
                $courseCountStmt->execute([$id]);
                $user['course_count'] = $courseCountStmt->fetchColumn();
                
                // Get exam count
                $examCountQuery = "SELECT COUNT(*) FROM exam_attempts WHERE user_id = ? AND deleted_at IS NULL";
                $examCountStmt = $conn->prepare($examCountQuery);
                $examCountStmt->execute([$id]);
                $user['exam_count'] = $examCountStmt->fetchColumn();
            }
        } else {
            // Sample data for development/testing
            $user = [
                'id' => $id,
                'username' => 'johndoe',
                'email' => 'john@example.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'profile_image' => null,
                'is_active' => 1,
                'status' => 'active',
                'bio' => 'This is a sample bio for John Doe.',
                'last_login' => '2023-04-15 10:30:00',
                'created_at' => '2023-01-15 10:30:00',
                'updated_at' => '2023-04-15 10:30:00',
                'mobile_number' => '+966501234567',
                'course_count' => 3,
                'exam_count' => 5
            ];
            
            $enrollments = [
                [
                    'id' => 1,
                    'course_id' => 1,
                    'course_title' => 'CCNA Certification',
                    'created_at' => '2023-02-01 14:30:00',
                    'status' => 'active'
                ],
                [
                    'id' => 2,
                    'course_id' => 3,
                    'course_title' => 'Security+ Certification',
                    'created_at' => '2023-03-15 09:45:00',
                    'status' => 'active'
                ],
                [
                    'id' => 3,
                    'course_id' => 4,
                    'course_title' => 'Network+ Basics',
                    'created_at' => '2023-04-10 16:20:00',
                    'status' => 'active'
                ]
            ];
        }
        
        // If user not found, redirect to users list
        if (!$user) {
            $_SESSION['errors'] = ['المستخدم غير موجود'];
            header('Location: /admin/users');
            exit;
        }
        
        // Set page title
        $pageTitle = "عرض المستخدم: " . $user['username'];
        
        // Set form data for view
        $form_data = $user;
        
        // Start output buffering
        ob_start();
        
        // Include the view
        include ADMIN_ROOT . '/templates/users/view.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id) {
        global $conn;
        
        // Get user data
        $user = null;
        
        if ($conn) {
            $query = "
                SELECT id, username, email, first_name, last_name, is_active, mobile_number
                FROM users
                WHERE id = ? AND deleted_at IS NULL
            ";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Convert is_active to status text
                $user['status'] = $user['is_active'] ? 'active' : 'inactive';
            }
        } else {
            // Sample data for development/testing
            $user = [
                'id' => $id,
                'username' => 'johndoe',
                'email' => 'john@example.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'is_active' => 1,
                'status' => 'active',
                'mobile_number' => '+966501234567'
            ];
        }
        
        // If user not found, redirect to users list
        if (!$user) {
            $_SESSION['errors'] = ['المستخدم غير موجود'];
            header('Location: /admin/users');
            exit;
        }
        
        // Set page title
        $pageTitle = "تعديل المستخدم: " . $user['username'];
        
        // Set form data (from session if available, otherwise from user data)
        $form_data = $_SESSION['form_data'] ?? $user;
        unset($_SESSION['form_data']);
        
        // Start output buffering
        ob_start();
        
        // Include the view
        include ADMIN_ROOT . '/templates/users/edit.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }

    /**
     * Update the specified user in the database
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
        
        // Format mobile number with + prefix
        $mobile_number = trim($_POST['mobile_number'] ?? '');
        if (!empty($mobile_number) && $mobile_number[0] !== '+') {
            $mobile_number = '+' . $mobile_number;
        }
        
        $role = $_POST['role'] ?? 'user';
        $status = $_POST['status'] ?? 'active';
        
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'اسم المستخدم مطلوب';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'اسم المستخدم يجب أن يحتوي على أحرف وأرقام فقط';
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
            $checkQuery = "SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND id != ? AND deleted_at IS NULL";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->execute([$username, $email, $id]);
            
            if ($checkStmt->fetchColumn() > 0) {
                $errors[] = 'اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل';
            }
        }
        
        // If there are errors, redirect back with errors and form data
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = [
                'username' => $username,
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'mobile_number' => $mobile_number,
                'role' => $role,
                'status' => $status
            ];
            
            header("Location: /admin/users/edit/$id");
            exit;
        }
        
        // Convert status to boolean
        $is_active = ($status === 'active') ? 1 : 0;
        
        // Update user in database
        if ($conn) {
            try {
                // Prepare query based on whether password is being updated
                if (!empty($password)) {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    $query = "
                        UPDATE users
                        SET username = ?, email = ?, password = ?, first_name = ?, last_name = ?, mobile_number = ?, is_active = ?, updated_at = NOW()
                        WHERE id = ?
                    ";
                    
                    $stmt = $conn->prepare($query);
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
                    $query = "
                        UPDATE users
                        SET username = ?, email = ?, first_name = ?, last_name = ?, mobile_number = ?, is_active = ?, updated_at = NOW()
                        WHERE id = ?
                    ";
                    
                    $stmt = $conn->prepare($query);
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
                
                // Log activity
                $this->logActivity('update', 'users', "المستخدم: $username");
                
                // Set success message
                $_SESSION['success'] = 'تم تحديث المستخدم بنجاح';
                
                // Redirect to users list
                header('Location: /admin/users');
                exit;
            } catch (PDOException $e) {
                // Log the error
                error_log("Error updating user: " . $e->getMessage());
                
                // Set error message
                $_SESSION['errors'] = ['حدث خطأ أثناء تحديث المستخدم. يرجى المحاولة مرة أخرى.'];
                
                // Redirect back with form data
                $_SESSION['form_data'] = [
                    'username' => $username,
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'mobile_number' => $mobile_number,
                    'role' => $role,
                    'status' => $status
                ];
                
                header("Location: /admin/users/edit/$id");
                exit;
            }
        } else {
            // Set success message (for development/testing)
            $_SESSION['success'] = 'تم تحديث المستخدم بنجاح (وضع التطوير)';
            
            // Redirect to users list
            header('Location: /admin/users');
            exit;
        }
    }

    /**
     * Delete the specified user from the database (soft delete)
     */
    public function delete($id) {
        global $conn;
        
        if ($conn) {
            try {
                // Get user info for logging
                $query = "SELECT username FROM users WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    // Soft delete the user
                    $deleteQuery = "UPDATE users SET deleted_at = NOW() WHERE id = ?";
                    $deleteStmt = $conn->prepare($deleteQuery);
                    $deleteStmt->execute([$id]);
                    
                    // Log activity
                    $this->logActivity('delete', 'users', "المستخدم: " . $user['username']);
                    
                    // Set success message
                    $_SESSION['success'] = 'تم حذف المستخدم بنجاح';
                } else {
                    // Set error message
                    $_SESSION['errors'] = ['المستخدم غير موجود'];
                }
            } catch (PDOException $e) {
                // Log the error
                error_log("Error deleting user: " . $e->getMessage());
                
                // Set error message
                $_SESSION['errors'] = ['حدث خطأ أثناء حذف المستخدم. يرجى المحاولة مرة أخرى.'];
            }
        } else {
            // Set success message (for development/testing)
            $_SESSION['success'] = 'تم حذف المستخدم بنجاح (وضع التطوير)';
        }
        
        // Redirect to users list
        header('Location: /admin/users');
        exit;
    }

    /**
     * Log admin activity
     */
    private function logActivity($action, $section, $details = '') {
        global $conn;
        
        if (!$conn) {
            return;
        }
        
        // Get current admin
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        
        if (!$currentAdmin) {
            return;
        }
        
        $adminId = $currentAdmin['id'];
        $adminUsername = $currentAdmin['username'];
        
        try {
            $query = "
                INSERT INTO admin_activity_logs (admin_id, admin_username, action, section, details, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([$adminId, $adminUsername, $action, $section, $details]);
        } catch (PDOException $e) {
            // Log the error
            error_log("Error logging activity: " . $e->getMessage());
        }
    }
}