<?php
/**
 * Admin Courses Controller
 * 
 * This controller handles course management in the admin panel.
 */
class AdminCoursesController {
    /**
     * API endpoint for course search suggestions
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
            $query = "SELECT c.id, c.title, c.slug, c.is_free, c.price, a.name as instructor_name 
                      FROM courses c
                      LEFT JOIN admins a ON c.admin_id = a.id
                      WHERE c.title LIKE ? OR a.name LIKE ? 
                      LIMIT 10";
            
            $stmt = $conn->prepare($query);
            $searchParam = "%$search%";
            $stmt->execute([$searchParam, $searchParam]);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $result) {
                $suggestions[] = [
                    'id' => $result['id'],
                    'value' => $result['title'],
                    'label' => $result['title'],
                    'instructor' => $result['instructor_name'],
                    'is_free' => $result['is_free'],
                    'price' => $result['price'],
                    'slug' => $result['slug']
                ];
            }
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($suggestions);
        exit;
    }
    /**
     * Display a list of courses
     */
    public function index() {
        global $conn;
        
        // Get the current admin
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        $isAdmin = $currentAdmin['role'] === 'admin';
        
        // Pagination settings
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        // Search and filter parameters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $is_free = isset($_GET['is_free']) ? $_GET['is_free'] : '';
        
        // Get courses
        $courses = [];
        $totalCourses = 0;
        
        if ($conn) {
            // Build the query with filters
            $query = "
                SELECT c.*, a.username as instructor_username, a.name as instructor_name,
                (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as subscribers_count
                FROM courses c
                LEFT JOIN admins a ON c.admin_id = a.id
                WHERE 1=1
            ";
            
            $countQuery = "
                SELECT COUNT(*) as total
                FROM courses c
                LEFT JOIN admins a ON c.admin_id = a.id
                WHERE 1=1
            ";
            
            $params = [];
            
            // Add search condition if provided
            if (!empty($search)) {
                $query .= " AND (c.title LIKE ? OR c.description LIKE ? OR a.name LIKE ?)";
                $countQuery .= " AND (c.title LIKE ? OR c.description LIKE ? OR a.name LIKE ?)";
                // Parameters for main query
                $params[] = "%$search%"; // For title
                $params[] = "%$search%"; // For description
                $params[] = "%$search%"; // For instructor name
                
                // Parameters for count query (needs to match the count query conditions)
                $countParams = ["%$search%", "%$search%", "%$search%"];
            }
            
            // Add status filter if provided
            if (!empty($status)) {
                $query .= " AND c.status = ?";
                $countQuery .= " AND c.status = ?";
                $params[] = $status;
            }
            
            // Add free/paid filter if provided
            if ($is_free !== '') {
                $query .= " AND c.is_free = ?";
                $countQuery .= " AND c.is_free = ?";
                $params[] = $is_free;
            }
            
            // If not admin, show only their courses
            if (!$isAdmin) {
                $query .= " AND c.admin_id = ?";
                $countQuery .= " AND c.admin_id = ?";
                $params[] = $currentAdmin['id'];
            }
            
            // Add order and limit
            $query .= " ORDER BY c.created_at DESC LIMIT $offset, $perPage";
            
            // Execute count query
            $countStmt = $conn->prepare($countQuery);
            
            // Use countParams if search is provided, otherwise use params
            if (!empty($search)) {
                $countExecuteParams = $countParams;
                
                // Add additional parameters from filters
                if (!empty($status)) {
                    $countExecuteParams[] = $status;
                }
                if ($is_free !== '') {
                    $countExecuteParams[] = $is_free;
                }
                if (!$isAdmin) {
                    $countExecuteParams[] = $currentAdmin['id'];
                }
                
                $countStmt->execute($countExecuteParams);
            } else {
                $countStmt->execute($params);
            }
            
            $totalCourses = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Execute main query
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $courses = [
                [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'slug' => 'ccna-certification',
                    'description' => 'Complete CCNA certification course covering all exam topics.',
                    'price' => 99.99,
                    'is_free' => 0,
                    'status' => 'published',
                    'admin_id' => 1,
                    'instructor_username' => 'admin',
                    'instructor_name' => 'Admin User',
                    'created_at' => '2023-07-10 09:00:00'
                ],
                [
                    'id' => 2,
                    'title' => 'CCNP Enterprise',
                    'slug' => 'ccnp-enterprise',
                    'description' => 'Advanced Cisco networking certification course.',
                    'price' => 199.99,
                    'is_free' => 0,
                    'status' => 'published',
                    'admin_id' => 1,
                    'instructor_username' => 'admin',
                    'instructor_name' => 'Admin User',
                    'created_at' => '2023-07-11 10:30:00'
                ],
                [
                    'id' => 3,
                    'title' => 'Security+ Certification',
                    'slug' => 'security-plus-certification',
                    'description' => 'CompTIA Security+ certification preparation course.',
                    'price' => 79.99,
                    'is_free' => 0,
                    'status' => 'published',
                    'admin_id' => 2,
                    'instructor_username' => 'instructor',
                    'instructor_name' => 'Instructor User',
                    'created_at' => '2023-07-12 14:15:00'
                ],
                [
                    'id' => 4,
                    'title' => 'Network+ Basics',
                    'slug' => 'network-plus-basics',
                    'description' => 'Introduction to networking concepts for Network+ certification.',
                    'price' => 0,
                    'is_free' => 1,
                    'status' => 'published',
                    'admin_id' => 2,
                    'instructor_username' => 'instructor',
                    'instructor_name' => 'Instructor User',
                    'created_at' => '2023-07-13 11:45:00'
                ],
                [
                    'id' => 5,
                    'title' => 'Linux Essentials',
                    'slug' => 'linux-essentials',
                    'description' => 'Essential Linux skills for IT professionals.',
                    'price' => 49.99,
                    'is_free' => 0,
                    'status' => 'published',
                    'admin_id' => 2,
                    'instructor_username' => 'instructor',
                    'instructor_name' => 'Instructor User',
                    'created_at' => '2023-07-14 16:20:00'
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'إدارة الدورات';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/courses/index.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the form to create a new course
     */
    public function create() {
        global $conn;
        
        // Get the current admin
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        $isAdmin = $currentAdmin['role'] === 'admin';
        
        // Check if instructor has permission to create courses
        if (!$isAdmin && !$adminAuth->hasPermission('manage_own_courses')) {
            // Set error message
            setFlashMessage('ليس لديك صلاحية لإضافة دورات', 'danger');
            
            // Redirect to dashboard
            header('Location: /admin');
            exit;
        }
        
        // Get admins (only for admin users)
        $instructors = [];
        if ($isAdmin && $conn) {
            $stmt = $conn->prepare("
                SELECT id, username, name
                FROM admins
                ORDER BY name
            ");
            $stmt->execute();
            $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else if ($conn) {
            // For instructors, only include themselves
            $instructors = [
                [
                    'id' => $currentAdmin['id'],
                    'username' => $currentAdmin['username'],
                    'name' => $currentAdmin['name']
                ]
            ];
        } else {
            // Sample data
            $instructors = [
                [
                    'id' => 1,
                    'username' => 'admin',
                    'name' => 'Admin User'
                ],
                [
                    'id' => 2,
                    'username' => 'instructor',
                    'name' => 'Instructor User'
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'إضافة دورة جديدة';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/courses/create.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Store a new course
     */
    public function store() {
        global $conn;
        
        // Get the current admin
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        $isAdmin = $currentAdmin['role'] === 'admin';
        
        // Check if instructor has permission to create courses
        if (!$isAdmin && !$adminAuth->hasPermission('manage_own_courses')) {
            // Set error message
            setFlashMessage('ليس لديك صلاحية لإضافة دورات', 'danger');
            
            // Redirect to dashboard
            header('Location: /admin');
            exit;
        }
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $company_url = trim($_POST['company_url'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        // For instructors, enforce draft status and assign to themselves
        if (!$isAdmin) {
            $status = 'draft'; // Force draft status for instructors
            $admin_id = $currentAdmin['id']; // Assign to the instructor
            $price = 0; // Default price to 0, admin will set it later
            $is_free = 0; // Default to not free, admin will decide
        } else {
            $price = floatval($_POST['price'] ?? 0);
            $is_free = isset($_POST['is_free']) ? 1 : 0;
            $status = $_POST['status'] ?? 'draft';
            $admin_id = intval($_POST['admin_id'] ?? 0);
        }
        
        $errors = [];
        
        if (empty($title)) {
            $errors[] = 'عنوان الدورة مطلوب';
        }
        
        if (empty($slug)) {
            // Generate slug from title
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        } else {
            // Validate slug format
            if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
                $errors[] = 'الرابط المختصر يجب أن يحتوي على أحرف صغيرة وأرقام وشرطات فقط';
            }
        }
        
        if (empty($description)) {
            $errors[] = 'وصف الدورة مطلوب';
        }
        
        if ($is_free) {
            $price = 0;
        } else if ($price < 0) {
            $errors[] = 'السعر يجب أن يكون أكبر من أو يساوي صفر';
        }
        
        if ($admin_id <= 0) {
            $errors[] = 'يجب اختيار مدرب للدورة';
        }
        
        // Check if slug already exists
        if ($conn && empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM courses WHERE slug = ?");
            $stmt->execute([$slug]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = 'الرابط المختصر مستخدم بالفعل';
            }
        }
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            
            // Redirect back to the form
            header('Location: /admin/courses/create');
            exit;
        }
        
        // Upload course image if provided
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = APP_APP_ROOT . '/uploads/courses/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = '/uploads/courses/' . $file_name;
            } else {
                // Set error message
                setFlashMessage('حدث خطأ أثناء رفع صورة الدورة', 'danger');
                
                // Redirect back to the form
                header('Location: /admin/courses/create');
                exit;
            }
        }
        
        // Create course
        if ($conn) {
            try {
                // Begin transaction
                $conn->beginTransaction();
                
                // Get company_id
                $company_id = isset($_POST['company_id']) && !empty($_POST['company_id']) ? (int)$_POST['company_id'] : null;
                
                // Insert course
                $stmt = $conn->prepare("
                    INSERT INTO courses (title, slug, company_url, company_id, description, price, is_free, image, status, admin_id, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $title,
                    $slug,
                    $company_url,
                    $company_id,
                    $description,
                    $price,
                    $is_free,
                    $image_path,
                    $status,
                    $admin_id
                ]);
                
                // Get the course ID
                $course_id = $conn->lastInsertId();
                
                // Process attachments if any
                if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
                    $upload_dir = APP_APP_ROOT . '/uploads/course_attachments/';
                    
                    // Create directory if it doesn't exist
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Prepare statement for inserting attachments
                    $attachmentStmt = $conn->prepare("
                        INSERT INTO course_attachments (course_id, title, file_path, file_type, file_size)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    // Loop through each attachment
                    $file_count = count($_FILES['attachments']['name']);
                    
                    for ($i = 0; $i < $file_count; $i++) {
                        if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {
                            $file_name = time() . '_' . basename($_FILES['attachments']['name'][$i]);
                            $file_type = $_FILES['attachments']['type'][$i];
                            $file_size = $_FILES['attachments']['size'][$i];
                            $target_file = $upload_dir . $file_name;
                            
                            // Move uploaded file
                            if (move_uploaded_file($_FILES['attachments']['tmp_name'][$i], $target_file)) {
                                $file_path = '/uploads/course_attachments/' . $file_name;
                                
                                // Insert attachment record
                                $attachmentStmt->execute([
                                    $course_id,
                                    basename($_FILES['attachments']['name'][$i]),
                                    $file_path,
                                    $file_type,
                                    $file_size
                                ]);
                            }
                        }
                    }
                }
                
                // Log the activity
                require_once APP_APP_ROOT . '/classes/ActivityLogger.php';
                $logger = new ActivityLogger($conn);
                $logger->logAdmin($currentAdmin['id'], 'create_course', "إضافة دورة جديدة: {$title}");
                
                // Commit transaction
                $conn->commit();
                
                // Set success message
                setFlashMessage('تم إنشاء الدورة بنجاح', 'success');
                
                // Redirect to courses list
                header('Location: /admin/courses');
                exit;
            } catch (PDOException $e) {
                // Rollback transaction
                $conn->rollBack();
                
                // Log the error
                error_log("Error creating course: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء إنشاء الدورة: ' . $e->getMessage(), 'danger');
                
                // Redirect back to the form
                header('Location: /admin/courses/create');
                exit;
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم إنشاء الدورة بنجاح (وضع العرض)', 'success');
            
            // Redirect to courses list
            header('Location: /admin/courses');
            exit;
        }
    }
    
    /**
     * Display a course
     * 
     * @param int $id The course ID
     */
    public function view($id) {
        global $conn;
        
        // Get the current admin
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        $isAdmin = $currentAdmin['role'] === 'admin';
        
        // Get course
        $course = null;
        $lessons = [];
        $lessonStats = [
            'total' => 0,
            'published' => 0,
            'free' => 0,
            'totalDuration' => 0
        ];
        
        if ($conn) {
            // Prepare the query
            $query = "
                SELECT c.*, a.username as instructor_username, a.name as instructor_name,
                (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as subscribers_count
                FROM courses c
                LEFT JOIN admins a ON c.admin_id = a.id
                WHERE c.id = ?
            ";
            
            // Add condition for instructors to only view their own courses
            if (!$isAdmin) {
                $query .= " AND c.admin_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id, $currentAdmin['id']]);
            } else {
                $stmt = $conn->prepare($query);
                $stmt->execute([$id]);
            }
            
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            // Get lessons for this course
            if ($course) {
                $stmt = $conn->prepare("
                    SELECT id, title, description, video_url, duration, order_number, is_free, status, created_at
                    FROM lessons
                    WHERE course_id = ?
                    ORDER BY order_number ASC
                ");
                $stmt->execute([$id]);
                $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Calculate lesson statistics
                $lessonStats["total"] = count($lessons);
                foreach ($lessons as $lesson) {
                    if ($lesson["status"] === "published") {
                        $lessonStats["published"]++;
                    }
                    if ($lesson["is_free"]) {
                        $lessonStats["free"]++;
                    }
                    $lessonStats["totalDuration"] += $lesson["duration"];
                }
            }
        } else {
            // Sample data
            $courses = [
                1 => [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'slug' => 'ccna-certification',
                    'description' => 'Complete CCNA certification course covering all exam topics.',
                    'price' => 99.99,
                    'is_free' => 0,
                    'image' => '',
                    'status' => 'published',
                    'admin_id' => 1,
                    'instructor_username' => 'admin',
                    'instructor_name' => 'Admin User',
                    'created_at' => '2023-07-10 09:00:00'
                ],
                2 => [
                    'id' => 2,
                    'title' => 'Security+ Certification',
                    'slug' => 'security-plus-certification',
                    'description' => 'CompTIA Security+ certification preparation course.',
                    'price' => 79.99,
                    'is_free' => 0,
                    'image' => '',
                    'status' => 'published',
                    'admin_id' => 2,
                    'instructor_username' => 'instructor',
                    'instructor_name' => 'Instructor User',
                    'created_at' => '2023-07-12 14:15:00'
                ]
            ];
            
            $course = $courses[$id] ?? null;
        }
        
        if (!$course) {
            // Set error message
            setFlashMessage('الدورة غير موجودة', 'danger');
            
            // Redirect to courses list
            header('Location: /admin/courses');
            exit;
        }
        
        // Set page title
        $pageTitle = 'عرض الدورة: ' . $course['title'];
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/courses/view.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the form to edit a course
     * 
     * @param int $id The course ID
     */
    public function edit($id) {
        global $conn;
        
        // Get the current admin
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        $isAdmin = $currentAdmin['role'] === 'admin';
        
        // Get course
        $course = null;
        if ($conn) {
            // Prepare the query
            $query = "
                SELECT *
                FROM courses
                WHERE id = ?
            ";
            
            // Add condition for instructors to only edit their own courses
            if (!$isAdmin) {
                $query .= " AND admin_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id, $currentAdmin['id']]);
            } else {
                $stmt = $conn->prepare($query);
                $stmt->execute([$id]);
            }
            
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $courses = [
                1 => [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'slug' => 'ccna-certification',
                    'description' => 'Complete CCNA certification course covering all exam topics.',
                    'price' => 99.99,
                    'is_free' => 0,
                    'image' => '',
                    'status' => 'published',
                    'instructor_id' => 1
                ],
                2 => [
                    'id' => 2,
                    'title' => 'Security+ Certification',
                    'slug' => 'security-plus-certification',
                    'description' => 'CompTIA Security+ certification preparation course.',
                    'price' => 79.99,
                    'is_free' => 0,
                    'image' => '',
                    'status' => 'published',
                    'instructor_id' => 2
                ]
            ];
            
            $course = $courses[$id] ?? null;
        }
        
        if (!$course) {
            // Set error message
            setFlashMessage('الدورة غير موجودة', 'danger');
            
            // Redirect to courses list
            header('Location: /admin/courses');
            exit;
        }
        
        // Get admins
        $instructors = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT id, username, name
                FROM admins
                ORDER BY name
            ");
            $stmt->execute();
            $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $instructors = [
                [
                    'id' => 1,
                    'username' => 'admin',
                    'name' => 'Admin User'
                ],
                [
                    'id' => 2,
                    'username' => 'instructor',
                    'name' => 'Instructor User'
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'تعديل الدورة';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/courses/edit.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Update a course
     * 
     * @param int $id The course ID
     */
    public function update($id) {
        global $conn;
        
        // Get the current admin
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        $isAdmin = $currentAdmin['role'] === 'admin';
        
        // Check if the course exists and belongs to the instructor (if not admin)
        if ($conn) {
            $query = "SELECT * FROM courses WHERE id = ?";
            if (!$isAdmin) {
                $query .= " AND admin_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id, $currentAdmin['id']]);
            } else {
                $stmt = $conn->prepare($query);
                $stmt->execute([$id]);
            }
            
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$course) {
                // Set error message
                setFlashMessage('الدورة غير موجودة أو ليس لديك صلاحية تعديلها', 'danger');
                
                // Redirect to courses list
                header('Location: /admin/courses');
                exit;
            }
        }
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $company_url = trim($_POST['company_url'] ?? '');
        $company_id = isset($_POST['company_id']) && !empty($_POST['company_id']) ? (int)$_POST['company_id'] : null;
        $description = trim($_POST['description'] ?? '');
        
        // For instructors, preserve existing values for restricted fields
        if (!$isAdmin) {
            // Get the current course values for restricted fields
            if ($conn) {
                $stmt = $conn->prepare("SELECT price, is_free, status, admin_id FROM courses WHERE id = ?");
                $stmt->execute([$id]);
                $currentCourse = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Use existing values
                $price = $currentCourse['price'];
                $is_free = $currentCourse['is_free'];
                $status = $currentCourse['status']; // Keep the current status
                $admin_id = $currentCourse['admin_id']; // Keep the current owner
            } else {
                // Default values if no database
                $price = 0;
                $is_free = 0;
                $status = 'draft';
                $admin_id = $currentAdmin['id'];
            }
        } else {
            // Admin can change all fields
            $price = floatval($_POST['price'] ?? 0);
            $is_free = isset($_POST['is_free']) ? 1 : 0;
            $status = $_POST['status'] ?? 'draft';
            $admin_id = intval($_POST['admin_id'] ?? 0);
        }
        
        $errors = [];
        
        if (empty($title)) {
            $errors[] = 'عنوان الدورة مطلوب';
        }
        
        if (empty($slug)) {
            // Generate slug from title
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        } else {
            // Validate slug format
            if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
                $errors[] = 'الرابط المختصر يجب أن يحتوي على أحرف صغيرة وأرقام وشرطات فقط';
            }
        }
        
        if (empty($description)) {
            $errors[] = 'وصف الدورة مطلوب';
        }
        
        if ($is_free) {
            $price = 0;
        } else if ($price < 0) {
            $errors[] = 'السعر يجب أن يكون أكبر من أو يساوي صفر';
        }
        
        if ($admin_id <= 0) {
            $errors[] = 'يجب اختيار مدرب للدورة';
        }
        
        // Check if slug already exists (excluding current course)
        if ($conn && empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM courses WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $id]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = 'الرابط المختصر مستخدم بالفعل';
            }
        }
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            
            // Redirect back to the form
            header("Location: /admin/courses/edit/$id");
            exit;
        }
        
        // Get current course image
        $image_path = '';
        if ($conn) {
            $stmt = $conn->prepare("SELECT image FROM courses WHERE id = ?");
            $stmt->execute([$id]);
            $current_course = $stmt->fetch(PDO::FETCH_ASSOC);
            $image_path = $current_course['image'] ?? '';
        }
        
        // Check if delete image is checked
        if (isset($_POST['delete_image']) && $_POST['delete_image'] == 1) {
            // Delete old image if exists
            if (!empty($image_path) && file_exists(APP_ROOT . $image_path)) {
                unlink(APP_ROOT . $image_path);
            }
            
            // Set image path to empty
            $image_path = '';
        }
        // Upload course image if provided
        elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = APP_ROOT . '/uploads/courses/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Delete old image if exists
                if (!empty($image_path) && file_exists(APP_ROOT . $image_path)) {
                    unlink(APP_ROOT . $image_path);
                }
                
                $image_path = '/uploads/courses/' . $file_name;
            } else {
                // Set error message
                setFlashMessage('حدث خطأ أثناء رفع صورة الدورة', 'danger');
                
                // Redirect back to the form
                header("Location: /admin/courses/edit/$id");
                exit;
            }
        }
        
        // Update course
        if ($conn) {
            try {
                // Begin transaction
                $conn->beginTransaction();
                
                $stmt = $conn->prepare("
                    UPDATE courses
                    SET title = ?, slug = ?, company_url = ?, company_id = ?, description = ?, price = ?, is_free = ?, image = ?, status = ?, admin_id = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $title,
                    $slug,
                    $company_url,
                    $company_id,
                    $description,
                    $price,
                    $is_free,
                    $image_path,
                    $status,
                    $admin_id,
                    $id
                ]);
                
                // Process attachments if any
                if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
                    $upload_dir = APP_APP_ROOT . '/uploads/course_attachments/';
                    
                    // Create directory if it doesn't exist
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Prepare statement for inserting attachments
                    $attachmentStmt = $conn->prepare("
                        INSERT INTO course_attachments (course_id, title, file_path, file_type, file_size)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    // Loop through each attachment
                    $file_count = count($_FILES['attachments']['name']);
                    
                    for ($i = 0; $i < $file_count; $i++) {
                        if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {
                            $file_name = time() . '_' . basename($_FILES['attachments']['name'][$i]);
                            $file_type = $_FILES['attachments']['type'][$i];
                            $file_size = $_FILES['attachments']['size'][$i];
                            $target_file = $upload_dir . $file_name;
                            
                            // Move uploaded file
                            if (move_uploaded_file($_FILES['attachments']['tmp_name'][$i], $target_file)) {
                                $file_path = '/uploads/course_attachments/' . $file_name;
                                
                                // Insert attachment record
                                $attachmentStmt->execute([
                                    $id,
                                    basename($_FILES['attachments']['name'][$i]),
                                    $file_path,
                                    $file_type,
                                    $file_size
                                ]);
                            }
                        }
                    }
                }
                
                // Log the activity
                require_once APP_APP_ROOT . '/classes/ActivityLogger.php';
                $logger = new ActivityLogger($conn);
                $logger->logAdmin($currentAdmin['id'], 'update_course', "تعديل دورة: {$title}");
                
                // Commit transaction
                $conn->commit();
                
                // Set success message
                setFlashMessage('تم تحديث الدورة بنجاح', 'success');
                
                // Redirect to courses list
                header('Location: /admin/courses');
                exit;
            } catch (PDOException $e) {
                // Rollback transaction
                $conn->rollBack();
                
                // Log the error
                error_log("Error updating course: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء تحديث الدورة', 'danger');
                
                // Redirect back to the form
                header("Location: /admin/courses/edit/$id");
                exit;
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم تحديث الدورة بنجاح (وضع العرض)', 'success');
            
            // Redirect to courses list
            header('Location: /admin/courses');
            exit;
        }
    }
    
    /**
     * Delete a course
     * 
     * @param int $id The course ID
     */
    public function delete($id) {
        global $conn;
        
        if ($conn) {
            try {
                // Get course details
                $stmt = $conn->prepare("SELECT image, title FROM courses WHERE id = ?");
                $stmt->execute([$id]);
                $course = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Get the current admin
                $adminAuth = new AdminAuth();
                $currentAdmin = $adminAuth->getCurrentAdmin();
                
                // Soft delete course
                $stmt = $conn->prepare("UPDATE courses SET deleted_at = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                // Delete course image if exists
                if (!empty($course['image']) && file_exists(APP_ROOT . $course['image'])) {
                    unlink(APP_ROOT . $course['image']);
                }
                
                // Log the activity
                require_once APP_APP_ROOT . '/classes/ActivityLogger.php';
                $logger = new ActivityLogger($conn);
                $logger->logAdmin($currentAdmin['id'], 'delete_course', "حذف دورة: {$course['title']}");
                
                // Set success message
                setFlashMessage('تم حذف الدورة بنجاح', 'success');
            } catch (PDOException $e) {
                // Log the error
                error_log("Error deleting course: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء حذف الدورة', 'danger');
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم حذف الدورة بنجاح (وضع العرض)', 'success');
        }
        
        // Redirect to courses list
        header('Location: /admin/courses');
        exit;
    }
    
    /**
     * Delete a course attachment
     */
    public function deleteAttachment() {
        global $conn;
        
        // Log request data for debugging
        error_log("DELETE ATTACHMENT REQUEST: " . json_encode($_POST));
        error_log("REQUEST METHOD: " . $_SERVER['REQUEST_METHOD']);
        
        // Get the current admin
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        $isAdmin = $currentAdmin['role'] === 'admin';
        
        // Get attachment ID and course ID with detailed logging
        $raw_attachment_id = $_POST['attachment_id'] ?? 'not set';
        $raw_course_id = $_POST['course_id'] ?? 'not set';
        
        error_log("RAW VALUES - attachment_id: $raw_attachment_id, course_id: $raw_course_id");
        
        // Convert to integers
        $attachment_id = isset($_POST['attachment_id']) ? (int)$_POST['attachment_id'] : 0;
        $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
        
        error_log("PARSED VALUES - attachment_id: $attachment_id, course_id: $course_id");
        
        if ($attachment_id <= 0 || $course_id <= 0) {
            // Set error message
            setFlashMessage('خطأ: لم يتم تحديد المرفق بشكل صحيح - attachment_id: ' . $attachment_id . ', course_id: ' . $course_id, 'danger');
            
            // Redirect back to the course edit page if course_id is available
            if (!empty($course_id)) {
                header("Location: /admin/courses/edit/$course_id");
            } else {
                header('Location: /admin/courses');
            }
            exit;
        }
        
        // Check if the course exists and belongs to the instructor (if not admin)
        if ($conn) {
            try {
                $query = "SELECT * FROM courses WHERE id = ?";
                $params = [$course_id];
                
                if (!$isAdmin) {
                    $query .= " AND admin_id = ?";
                    $params[] = $currentAdmin['id'];
                }
                
                $stmt = $conn->prepare($query);
                $stmt->execute($params);
                
                $course = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$course) {
                    // Set error message
                    setFlashMessage('الدورة غير موجودة أو ليس لديك صلاحية تعديلها', 'danger');
                    
                    // Redirect to courses list
                    header('Location: /admin/courses');
                    exit;
                }
                
                // Get attachment
                $attachmentStmt = $conn->prepare("
                    SELECT * FROM course_attachments 
                    WHERE id = ? AND course_id = ?
                ");
                $attachmentStmt->execute([$attachment_id, $course_id]);
                $attachment = $attachmentStmt->fetch(PDO::FETCH_ASSOC);
                
                // Log attachment data for debugging
                error_log("ATTACHMENT DATA: " . json_encode($attachment));
                
                if (!$attachment) {
                    // Set error message
                    setFlashMessage('المرفق غير موجود', 'danger');
                    
                    // Redirect back
                    header("Location: /admin/courses/edit/$course_id");
                    exit;
                }
                
                // Delete attachment file
                $file_path = APP_APP_ROOT . $attachment['file_path'];
                error_log("Attempting to delete file: " . $file_path);
                if (file_exists($file_path)) {
                    unlink($file_path);
                    error_log("File deleted successfully");
                } else {
                    error_log("File does not exist: " . $file_path);
                }
                
                // Delete attachment record
                $deleteStmt = $conn->prepare("DELETE FROM course_attachments WHERE id = ?");
                $deleteStmt->execute([$attachment_id]);
                
                // Set success message
                setFlashMessage('تم حذف المرفق بنجاح', 'success');
            } catch (PDOException $e) {
                // Log the error
                error_log("Error deleting attachment: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء حذف المرفق', 'danger');
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم حذف المرفق بنجاح (وضع العرض)', 'success');
        }
        
        // Redirect back
        header("Location: /admin/courses/edit/$course_id");
        exit;
    }
}