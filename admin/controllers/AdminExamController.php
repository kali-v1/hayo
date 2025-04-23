<?php
/**
 * Admin Exam Controller
 * 
 * This controller handles exam management in the admin panel.
 */
class AdminExamController {
    /**
     * API endpoint for exam search suggestions
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
            // Query for suggestions - only search by title as requested
            $query = "SELECT id, title, description, is_free, price, passing_score, pass_criteria_type 
                      FROM exams 
                      WHERE title LIKE ? 
                      LIMIT 10";
            
            $stmt = $conn->prepare($query);
            $searchParam = "%$search%";
            $stmt->execute([$searchParam]);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $result) {
                $suggestions[] = [
                    'id' => $result['id'],
                    'value' => $result['title'],
                    'label' => $result['title'],
                    'description' => $result['description'],
                    'is_free' => $result['is_free'],
                    'price' => $result['price'],
                    'passing_score' => $result['passing_score'],
                    'pass_criteria_type' => $result['pass_criteria_type']
                ];
            }
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($suggestions);
        exit;
    }
    /**
     * Display a list of exams
     */
    public function index() {
        global $conn;
        
        // Pagination settings
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        // Search and filter parameters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $is_free = isset($_GET['is_free']) ? $_GET['is_free'] : '';
        
        // Get exams
        $exams = [];
        $totalExams = 0;
        
        if ($conn) {
            // Build the query with filters
            $query = "
                SELECT e.*, a.name as creator_name, a.username as creator_username,
                (SELECT COUNT(*) FROM exam_attempts WHERE exam_id = e.id) as attempts_count
                FROM exams e
                LEFT JOIN admins a ON e.admin_id = a.id
                WHERE 1=1
            ";
            
            $countQuery = "
                SELECT COUNT(*) as total
                FROM exams e
                WHERE 1=1
            ";
            
            $params = [];
            
            // Add search condition if provided - only search by title as requested
            if (!empty($search)) {
                $query .= " AND e.title LIKE ?";
                $countQuery .= " AND e.title LIKE ?";
                $params[] = "%$search%";
            }
            
            // Add status filter if provided
            if ($status === 'published') {
                $query .= " AND e.is_published = 1";
                $countQuery .= " AND e.is_published = 1";
            } else if ($status === 'draft') {
                $query .= " AND e.is_published = 0";
                $countQuery .= " AND e.is_published = 0";
            }
            
            // Add free/paid filter if provided
            if ($is_free !== '') {
                $query .= " AND e.is_free = ?";
                $countQuery .= " AND e.is_free = ?";
                $params[] = $is_free;
            }
            
            // Add order and limit
            $query .= " ORDER BY e.created_at DESC LIMIT $offset, $perPage";
            
            // Execute count query
            $countStmt = $conn->prepare($countQuery);
            $countStmt->execute($params);
            $totalExams = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Execute main query
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get recommended courses for each exam and convert is_published to status
            foreach ($exams as &$exam) {
                // Convert is_published to status
                $exam['status'] = $exam['is_published'] ? 'published' : 'draft';
                
                $stmt = $conn->prepare("
                    SELECT c.id, c.title
                    FROM exam_course_recommendations ecr
                    JOIN courses c ON ecr.course_id = c.id
                    WHERE ecr.exam_id = ?
                    ORDER BY ecr.priority
                ");
                $stmt->execute([$exam['id']]);
                $exam['recommended_courses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            // Sample data
            $exams = [];
        }
        
        // Set page title
        $pageTitle = 'إدارة الاختبارات';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/exams/index.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the form to create a new exam
     */
    public function create() {
        global $conn;
        
        // Set page title
        $pageTitle = 'إضافة اختبار جديد';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/exams/create.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Store a new exam
     */
    public function store() {
        global $conn, $currentAdmin;
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $company_url = trim($_POST['company_url'] ?? '');
        $company_id = isset($_POST['company_id']) && !empty($_POST['company_id']) ? (int)$_POST['company_id'] : null;
        $course_id = !empty($_POST['course_id']) ? intval($_POST['course_id']) : null;
        $duration = intval($_POST['duration'] ?? 0);
        $pass_criteria_type = $_POST['pass_criteria_type'] ?? 'percentage';
        $passing_score = intval($_POST['passing_score'] ?? 0);
        $is_free = isset($_POST['is_free']) ? 1 : 0;
        $price = $is_free ? 0 : floatval($_POST['price'] ?? 0);
        
        // Force draft status for data_entry users
        $adminRole = $currentAdmin['role'] ?? '';
        if ($adminRole === 'data_entry') {
            $status = 'draft'; // Always draft for data entry users
        } else {
            $status = $_POST['status'] ?? 'draft';
        }
        
        $errors = [];
        
        if (empty($title)) {
            $errors[] = 'عنوان الاختبار مطلوب';
        }
        
        if (empty($description)) {
            $errors[] = 'وصف الاختبار مطلوب';
        }
        
        if ($duration <= 0) {
            $errors[] = 'مدة الاختبار يجب أن تكون أكبر من صفر';
        }
        
        if ($pass_criteria_type === 'percentage' && ($passing_score < 0 || $passing_score > 100)) {
            $errors[] = 'درجة النجاح يجب أن تكون بين 0 و 100 في حالة النسبة المئوية';
        } else if ($pass_criteria_type === 'fixed_score' && $passing_score < 0) {
            $errors[] = 'درجة النجاح يجب أن تكون أكبر من أو تساوي صفر في حالة الدرجة الثابتة';
        }
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            
            // Redirect back to the form
            header('Location: /admin/exams/create');
            exit;
        }
        
        // Create exam
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO exams (title, slug, description, company_url, company_id, course_id, duration_minutes, passing_score, pass_criteria_type, price, is_free, is_published, admin_id, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                // Generate a slug from the title
                $slug = strtolower(str_replace(' ', '-', $title));
                
                // Get current admin ID
                $admin_id = $currentAdmin['id'] ?? 0;
                
                // Convert status to is_published
                $is_published = ($status === 'published') ? 1 : 0;
                
                $stmt->execute([
                    $title,
                    $slug,
                    $description,
                    $company_url,
                    $company_id,
                    $course_id,
                    $duration,
                    $passing_score,
                    $pass_criteria_type,
                    $price,
                    $is_free,
                    $is_published,
                    $admin_id
                ]);
                
                // Get the exam ID
                $exam_id = $conn->lastInsertId();
                
                // Process recommended courses if any
                if (isset($_POST['recommended_courses']) && is_array($_POST['recommended_courses'])) {
                    $recommendedCourses = $_POST['recommended_courses'];
                    
                    if (!empty($recommendedCourses)) {
                        $stmt = $conn->prepare("
                            INSERT INTO exam_course_recommendations (exam_id, course_id, priority)
                            VALUES (?, ?, ?)
                        ");
                        
                        foreach ($recommendedCourses as $index => $course_id) {
                            $stmt->execute([
                                $exam_id,
                                $course_id,
                                $index + 1
                            ]);
                        }
                    }
                }
                
                // Log the activity
                $examId = $conn->lastInsertId();
                $logger = new AdminLogger($conn);
                $logger->log(
                    $currentAdmin['id'],
                    $currentAdmin['username'],
                    'add',
                    'exams',
                    [
                        'exam_id' => $examId,
                        'title' => $title,
                        'is_free' => $is_free ? 'Yes' : 'No',
                        'price' => $price,
                        'status' => $status
                    ]
                );
                
                // Set success message
                setFlashMessage('تم إنشاء الاختبار بنجاح', 'success');
                
                // Redirect to exams list
                header('Location: /admin/exams');
                exit;
            } catch (PDOException $e) {
                // Log the error
                error_log("Error creating exam: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء إنشاء الاختبار: ' . $e->getMessage(), 'danger');
                
                // Redirect back to the form
                header('Location: /admin/exams/create');
                exit;
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم إنشاء الاختبار بنجاح (وضع العرض)', 'success');
            
            // Redirect to exams list
            header('Location: /admin/exams');
            exit;
        }
    }
    
    /**
     * Display the form to edit an exam
     * 
     * @param int $id The exam ID
     */
    public function edit($id) {
        global $conn;
        
        // Get exam
        $exam = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT e.*, a.name as creator_name, a.username as creator_username
                FROM exams e
                LEFT JOIN admins a ON e.admin_id = a.id
                WHERE e.id = ?
            ");
            $stmt->execute([$id]);
            $exam = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($exam) {
                // Convert is_published to status
                $exam['status'] = $exam['is_published'] ? 'published' : 'draft';
                
                // Get recommended courses
                $stmt = $conn->prepare("
                    SELECT course_id
                    FROM exam_course_recommendations
                    WHERE exam_id = ?
                    ORDER BY priority
                ");
                $stmt->execute([$id]);
                $recommendedCourses = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $exam['recommended_courses'] = $recommendedCourses;
            }
        }
        
        if (!$exam) {
            // Set error message
            setFlashMessage('الاختبار غير موجود', 'danger');
            
            // Redirect to exams list
            header('Location: /admin/exams');
            exit;
        }
        
        // Set page title
        $pageTitle = 'تعديل الاختبار: ' . $exam['title'];
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/exams/edit.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Update an exam
     * 
     * @param int $id The exam ID
     */
    public function update($id) {
        global $conn, $currentAdmin;
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $company_url = trim($_POST['company_url'] ?? '');
        $duration = intval($_POST['duration'] ?? 0);
        $pass_criteria_type = $_POST['pass_criteria_type'] ?? 'percentage';
        $passing_score = intval($_POST['passing_score'] ?? 0);
        $is_free = isset($_POST['is_free']) ? 1 : 0;
        $price = $is_free ? 0 : floatval($_POST['price'] ?? 0);
        
        // Force draft status for data_entry users
        $adminRole = $currentAdmin['role'] ?? '';
        if ($adminRole === 'data_entry') {
            $status = 'draft'; // Always draft for data entry users
        } else {
            $status = $_POST['status'] ?? 'draft';
        }
        
        // Convert status to is_published
        $is_published = ($status === 'published') ? 1 : 0;
        
        if ($conn) {
            try {
                // Get exam info before update for logging
                $examStmt = $conn->prepare("SELECT title FROM exams WHERE id = ?");
                $examStmt->execute([$id]);
                $exam = $examStmt->fetch(PDO::FETCH_ASSOC);
                
                // Update the exam
                $stmt = $conn->prepare("
                    UPDATE exams 
                    SET title = ?, description = ?, company_url = ?, duration_minutes = ?, 
                        passing_score = ?, pass_criteria_type = ?, price = ?, is_free = ?, 
                        is_published = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $title,
                    $description,
                    $company_url,
                    $duration,
                    $passing_score,
                    $pass_criteria_type,
                    $price,
                    $is_free,
                    $is_published,
                    $id
                ]);
                
                // Log the exam update
                $logger = new AdminLogger($conn);
                $logger->log(
                    $currentAdmin['id'],
                    $currentAdmin['username'],
                    'update',
                    'exams',
                    [
                        'exam_id' => $id,
                        'old_title' => $exam['title'] ?? 'unknown',
                        'new_title' => $title,
                        'is_free' => $is_free ? 'Yes' : 'No',
                        'price' => $price,
                        'status' => $status
                    ]
                );
                
                // Set success message
                setFlashMessage('تم تحديث الاختبار بنجاح', 'success');
                
            } catch (PDOException $e) {
                // Log the error
                error_log("Error updating exam: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء تحديث الاختبار', 'danger');
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم تحديث الاختبار بنجاح (وضع العرض)', 'success');
        }
        
        // Redirect to exams list
        header('Location: /admin/exams');
        exit;
    }
    
    /**
     * Delete an exam
     * 
     * @param int $id The exam ID
     */
    public function delete($id) {
        global $conn, $currentAdmin;
        
        if ($conn) {
            try {
                // Get exam info before deletion for logging
                $examStmt = $conn->prepare("SELECT title FROM exams WHERE id = ?");
                $examStmt->execute([$id]);
                $exam = $examStmt->fetch(PDO::FETCH_ASSOC);
                
                // Soft delete the exam
                $stmt = $conn->prepare("UPDATE exams SET deleted_at = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                // Log the exam deletion
                $logger = new AdminLogger($conn);
                $logger->log(
                    $currentAdmin['id'],
                    $currentAdmin['username'],
                    'delete',
                    'exams',
                    [
                        'exam_id' => $id,
                        'title' => $exam['title'] ?? 'unknown'
                    ]
                );
                
                // Set success message
                setFlashMessage('تم حذف الاختبار بنجاح', 'success');
                
            } catch (PDOException $e) {
                // Log the error
                error_log("Error deleting exam: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء حذف الاختبار', 'danger');
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم حذف الاختبار بنجاح (وضع العرض)', 'success');
        }
        
        // Redirect to exams list
        header('Location: /admin/exams');
        exit;
    }
    
    /**
     * View an exam details
     * 
     * @param int $id The exam ID
     */
    public function view($id) {
        global $conn;
        
        // Get exam
        $exam = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT e.*, a.name as creator_name, a.username as creator_username
                FROM exams e
                LEFT JOIN admins a ON e.admin_id = a.id
                WHERE e.id = ?
            ");
            $stmt->execute([$id]);
            $exam = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($exam) {
                // Convert is_published to status
                $exam['status'] = $exam['is_published'] ? 'published' : 'draft';
                
                // Get recommended courses
                try {
                    $stmt = $conn->prepare("
                        SELECT c.id, c.title
                        FROM exam_course_recommendations ecr
                        JOIN courses c ON ecr.course_id = c.id
                        WHERE ecr.exam_id = ?
                        ORDER BY ecr.priority
                    ");
                    $stmt->execute([$id]);
                    $exam['recommended_courses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    // Table might not exist yet
                    error_log("Error getting recommended courses: " . $e->getMessage());
                    $exam['recommended_courses'] = [];
                }
                
                // Get exam questions count
                try {
                    $stmt = $conn->prepare("
                        SELECT COUNT(*) as total_questions
                        FROM exam_questions
                        WHERE exam_id = ?
                    ");
                    $stmt->execute([$id]);
                    $questionsCount = $stmt->fetch(PDO::FETCH_ASSOC);
                    $exam['total_questions'] = $questionsCount['total_questions'] ?? 0;
                } catch (PDOException $e) {
                    // Table might not exist yet
                    error_log("Error getting questions count: " . $e->getMessage());
                    $exam['total_questions'] = 0;
                }
                
                // Get exam attempts count
                try {
                    $stmt = $conn->prepare("
                        SELECT COUNT(*) as total_attempts
                        FROM exam_attempts
                        WHERE exam_id = ?
                    ");
                    $stmt->execute([$id]);
                    $attemptsCount = $stmt->fetch(PDO::FETCH_ASSOC);
                    $exam['total_attempts'] = $attemptsCount['total_attempts'] ?? 0;
                } catch (PDOException $e) {
                    // Table might not exist yet
                    error_log("Error getting attempts count: " . $e->getMessage());
                    $exam['total_attempts'] = 0;
                }
            }
        }
        
        if (!$exam) {
            // Set error message
            setFlashMessage('الاختبار غير موجود', 'danger');
            
            // Redirect to exams list
            header('Location: /admin/exams');
            exit;
        }
        
        // Set page title
        $pageTitle = 'عرض الاختبار: ' . $exam['title'];
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/exams/view.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
}
