<?php
/**
 * Lessons Controller
 * 
 * This controller handles all lesson management operations
 */
class LessonsController {
    private $db;
    private $currentAdmin;
    
    /**
     * Constructor
     */
    public function __construct($db, $currentAdmin) {
        $this->db = $db;
        $this->currentAdmin = $currentAdmin;
    }
    
    /**
     * Delete a lesson attachment
     */
    public function deleteAttachment() {
        // Log request data for debugging
        error_log("DELETE LESSON ATTACHMENT REQUEST: " . json_encode($_POST));
        error_log("REQUEST METHOD: " . $_SERVER['REQUEST_METHOD']);
        
        // Get attachment ID, lesson ID and course ID
        $raw_attachment_id = $_POST['attachment_id'] ?? 'not set';
        $raw_lesson_id = $_POST['lesson_id'] ?? 'not set';
        $raw_course_id = $_POST['course_id'] ?? 'not set';
        
        error_log("RAW VALUES - attachment_id: $raw_attachment_id, lesson_id: $raw_lesson_id, course_id: $raw_course_id");
        
        // Convert to integers
        $attachment_id = isset($_POST['attachment_id']) ? (int)$_POST['attachment_id'] : 0;
        $lesson_id = isset($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : 0;
        $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
        
        error_log("PARSED VALUES - attachment_id: $attachment_id, lesson_id: $lesson_id, course_id: $course_id");
        
        if ($attachment_id <= 0 || $lesson_id <= 0 || $course_id <= 0) {
            // Set error message
            setFlashMessage('خطأ: لم يتم تحديد المرفق بشكل صحيح - attachment_id: ' . $attachment_id . ', lesson_id: ' . $lesson_id . ', course_id: ' . $course_id, 'danger');
            
            // Redirect back to the lesson edit page if lesson_id and course_id are available
            if (!empty($lesson_id) && !empty($course_id)) {
                header("Location: /admin/courses/$course_id/lessons/$lesson_id/edit");
            } else if (!empty($course_id)) {
                header("Location: /admin/courses/$course_id/lessons");
            } else {
                header('Location: /admin/courses');
            }
            exit;
        }
        
        // Check if the lesson exists and belongs to the current admin
        $isAdmin = $this->currentAdmin['role'] === 'admin';
        
        try {
            $query = "SELECT l.* FROM lessons l 
                      JOIN courses c ON l.course_id = c.id 
                      WHERE l.id = ? AND l.course_id = ?";
            $params = [$lesson_id, $course_id];
            
            if (!$isAdmin) {
                $query .= " AND c.admin_id = ?";
                $params[] = $this->currentAdmin['id'];
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$lesson) {
                // Lesson not found or doesn't belong to this admin
                setFlashMessage('الدرس غير موجود أو ليس لديك صلاحية تعديله', 'danger');
                header('Location: /admin/courses');
                exit;
            }
            
            // Get attachment
            $attachmentStmt = $this->db->prepare("
                SELECT * FROM lesson_attachments 
                WHERE id = ? AND lesson_id = ?
            ");
            $attachmentStmt->execute([$attachment_id, $lesson_id]);
            $attachment = $attachmentStmt->fetch(PDO::FETCH_ASSOC);
            
            // Log attachment data for debugging
            error_log("ATTACHMENT DATA: " . json_encode($attachment));
            
            if (!$attachment) {
                // Attachment not found
                setFlashMessage('المرفق غير موجود', 'danger');
                header("Location: /admin/courses/$course_id/lessons/$lesson_id/edit");
                exit;
            }
            
            // Delete attachment file
            $file_path = APP_ROOT . $attachment['file_path'];
            error_log("Attempting to delete file: " . $file_path);
            if (file_exists($file_path)) {
                unlink($file_path);
                error_log("File deleted successfully");
            } else {
                error_log("File does not exist: " . $file_path);
            }
            
            // Delete attachment record
            $deleteStmt = $this->db->prepare("DELETE FROM lesson_attachments WHERE id = ?");
            $deleteStmt->execute([$attachment_id]);
            
            // Set success message
            setFlashMessage('تم حذف المرفق بنجاح', 'success');
        } catch (PDOException $e) {
            // Log the error
            error_log("Error deleting lesson attachment: " . $e->getMessage());
            
            // Set error message
            setFlashMessage('حدث خطأ أثناء حذف المرفق', 'danger');
        }
        
        // Redirect back to the lesson edit page
        header("Location: /admin/courses/$course_id/lessons/$lesson_id/edit");
        exit;
    }
    
    /**
     * List all lessons for a specific course
     */
    public function index($courseId) {
        // Check if user is admin
        $isAdmin = $this->currentAdmin['role'] === 'admin';
        
        // Verify the course exists and belongs to the current admin if not an admin
        if ($isAdmin) {
            $stmt = $this->db->prepare("SELECT id, title FROM courses WHERE id = ?");
            $stmt->execute([$courseId]);
        } else {
            $stmt = $this->db->prepare("SELECT id, title FROM courses WHERE id = ? AND admin_id = ?");
            $stmt->execute([$courseId, $this->currentAdmin['id']]);
        }
        
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$course) {
            // Course not found or doesn't belong to this admin
            header('Location: /admin/courses');
            exit;
        }
        
        // Get all lessons for this course with attachment count
        $stmt = $this->db->prepare("
            SELECT l.id, l.title, l.description, l.video_url, l.duration, l.order_number, l.is_free, l.status, l.created_at,
                   (SELECT COUNT(*) FROM lesson_attachments WHERE lesson_id = l.id) as attachment_count
            FROM lessons l
            WHERE l.course_id = ?
            ORDER BY l.order_number ASC
        ");
        $stmt->execute([$courseId]);
        $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Count total lessons and published lessons
        $totalLessons = count($lessons);
        $publishedLessons = 0;
        $totalDuration = 0;
        
        foreach ($lessons as $lesson) {
            if ($lesson['status'] === 'published') {
                $publishedLessons++;
            }
            $totalDuration += $lesson['duration'];
        }
        
        // Set page title
        $pageTitle = "إدارة دروس الدورة: " . $course['title'];
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/lessons/index_new.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Show the form to create a new lesson
     */
    public function create($courseId) {
        // Check if user is admin
        $isAdmin = $this->currentAdmin['role'] === 'admin';
        
        // Verify the course exists and belongs to the current admin if not an admin
        if ($isAdmin) {
            $stmt = $this->db->prepare("SELECT id, title FROM courses WHERE id = ?");
            $stmt->execute([$courseId]);
        } else {
            $stmt = $this->db->prepare("SELECT id, title FROM courses WHERE id = ? AND admin_id = ?");
            $stmt->execute([$courseId, $this->currentAdmin['id']]);
        }
        
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$course) {
            // Course not found or doesn't belong to this admin
            header('Location: /admin/courses');
            exit;
        }
        
        // Get the highest order number for this course
        $stmt = $this->db->prepare("SELECT MAX(order_number) as max_order FROM lessons WHERE course_id = ?");
        $stmt->execute([$courseId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextOrder = ($result['max_order'] ?? 0) + 1;
        
        // Set page title
        $pageTitle = "إضافة درس جديد - " . $course['title'];
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/lessons/create_new.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Store a new lesson
     */
    public function store($courseId) {
        // Check if user is admin
        $isAdmin = $this->currentAdmin['role'] === 'admin';
        
        // Verify the course exists and belongs to the current admin if not an admin
        if ($isAdmin) {
            $stmt = $this->db->prepare("SELECT id FROM courses WHERE id = ?");
            $stmt->execute([$courseId]);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM courses WHERE id = ? AND admin_id = ?");
            $stmt->execute([$courseId, $this->currentAdmin['id']]);
        }
        
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$course) {
            // Course not found or doesn't belong to this admin
            header('Location: /admin/courses');
            exit;
        }
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $content = $_POST['content'] ?? '';
        $videoUrl = trim($_POST['video_url'] ?? '');
        $duration = (int)($_POST['duration'] ?? 0);
        $orderNumber = (int)($_POST['order_number'] ?? 0);
        $isFree = isset($_POST['is_free']) ? 1 : 0;
        $status = $_POST['status'] ?? 'draft';
        
        if (empty($title)) {
            $_SESSION['error'] = 'عنوان الدرس مطلوب';
            header("Location: /admin/courses/{$courseId}/lessons/create");
            exit;
        }
        
        try {
            // Begin transaction
            $this->db->beginTransaction();
            
            // Insert the new lesson
            $stmt = $this->db->prepare("
                INSERT INTO lessons (course_id, title, description, content, video_url, duration, order_number, is_free, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $courseId, $title, $description, $content, $videoUrl, $duration, $orderNumber, $isFree, $status
            ]);
            
            // Get the lesson ID
            $lessonId = $this->db->lastInsertId();
            
            // Process attachments if any
            if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
                $upload_dir = APP_ROOT . '/uploads/lesson_attachments/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Prepare statement for inserting attachments
                $attachmentStmt = $this->db->prepare("
                    INSERT INTO lesson_attachments (lesson_id, title, file_path, file_type, file_size)
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
                            $file_path = '/uploads/lesson_attachments/' . $file_name;
                            
                            // Insert attachment record
                            $attachmentStmt->execute([
                                $lessonId,
                                basename($_FILES['attachments']['name'][$i]),
                                $file_path,
                                $file_type,
                                $file_size
                            ]);
                        }
                    }
                }
            }
            
            // Commit transaction
            $this->db->commit();
            
            $_SESSION['success'] = 'تم إضافة الدرس بنجاح';
        } catch (PDOException $e) {
            // Rollback transaction
            $this->db->rollBack();
            
            // Log the error
            error_log("Error creating lesson: " . $e->getMessage());
            
            $_SESSION['error'] = 'حدث خطأ أثناء إضافة الدرس: ' . $e->getMessage();
        }
        
        header("Location: /admin/courses/{$courseId}/lessons");
        exit;
    }
    
    /**
     * Show the form to edit a lesson
     */
    public function edit($courseId, $lessonId) {
        // Check if user is admin
        $isAdmin = $this->currentAdmin['role'] === 'admin';
        
        // Verify the course exists and belongs to the current admin if not an admin
        if ($isAdmin) {
            $stmt = $this->db->prepare("SELECT id, title FROM courses WHERE id = ?");
            $stmt->execute([$courseId]);
        } else {
            $stmt = $this->db->prepare("SELECT id, title FROM courses WHERE id = ? AND admin_id = ?");
            $stmt->execute([$courseId, $this->currentAdmin['id']]);
        }
        
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$course) {
            // Course not found or doesn't belong to this admin
            header('Location: /admin/courses');
            exit;
        }
        
        // Get the lesson
        $stmt = $this->db->prepare("
            SELECT id, title, description, content, video_url, duration, order_number, is_free, status
            FROM lessons
            WHERE id = ? AND course_id = ?
        ");
        $stmt->execute([$lessonId, $courseId]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$lesson) {
            $_SESSION['error'] = 'الدرس غير موجود';
            header("Location: /admin/courses/{$courseId}/lessons");
            exit;
        }
        
        // Set page title
        $pageTitle = "تعديل درس - " . $course['title'];
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/lessons/edit_new.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Update a lesson
     */
    public function update($courseId, $lessonId) {
        // Check if user is admin
        $isAdmin = $this->currentAdmin['role'] === 'admin';
        
        // Verify the course exists and belongs to the current admin if not an admin
        if ($isAdmin) {
            $stmt = $this->db->prepare("SELECT id FROM courses WHERE id = ?");
            $stmt->execute([$courseId]);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM courses WHERE id = ? AND admin_id = ?");
            $stmt->execute([$courseId, $this->currentAdmin['id']]);
        }
        
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$course) {
            // Course not found or doesn't belong to this admin
            header('Location: /admin/courses');
            exit;
        }
        
        // Verify the lesson exists and belongs to this course
        $stmt = $this->db->prepare("SELECT id FROM lessons WHERE id = ? AND course_id = ?");
        $stmt->execute([$lessonId, $courseId]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$lesson) {
            $_SESSION['error'] = 'الدرس غير موجود';
            header("Location: /admin/courses/{$courseId}/lessons");
            exit;
        }
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $content = $_POST['content'] ?? '';
        $videoUrl = trim($_POST['video_url'] ?? '');
        $duration = (int)($_POST['duration'] ?? 0);
        $orderNumber = (int)($_POST['order_number'] ?? 0);
        $isFree = isset($_POST['is_free']) ? 1 : 0;
        $status = $_POST['status'] ?? 'draft';
        
        if (empty($title)) {
            $_SESSION['error'] = 'عنوان الدرس مطلوب';
            header("Location: /admin/courses/{$courseId}/lessons/{$lessonId}/edit");
            exit;
        }
        
        try {
            // Begin transaction
            $this->db->beginTransaction();
            
            // Update the lesson
            $stmt = $this->db->prepare("
                UPDATE lessons
                SET title = ?, description = ?, content = ?, video_url = ?, duration = ?, order_number = ?, is_free = ?, status = ?
                WHERE id = ? AND course_id = ?
            ");
            
            $stmt->execute([
                $title, $description, $content, $videoUrl, $duration, $orderNumber, $isFree, $status, $lessonId, $courseId
            ]);
            
            // Process attachments if any
            if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
                $upload_dir = APP_ROOT . '/uploads/lesson_attachments/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Prepare statement for inserting attachments
                $attachmentStmt = $this->db->prepare("
                    INSERT INTO lesson_attachments (lesson_id, title, file_path, file_type, file_size)
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
                            $file_path = '/uploads/lesson_attachments/' . $file_name;
                            
                            // Insert attachment record
                            $attachmentStmt->execute([
                                $lessonId,
                                basename($_FILES['attachments']['name'][$i]),
                                $file_path,
                                $file_type,
                                $file_size
                            ]);
                        }
                    }
                }
            }
            
            // Commit transaction
            $this->db->commit();
            
            $_SESSION['success'] = 'تم تحديث الدرس بنجاح';
        } catch (PDOException $e) {
            // Rollback transaction
            $this->db->rollBack();
            
            // Log the error
            error_log("Error updating lesson: " . $e->getMessage());
            
            $_SESSION['error'] = 'حدث خطأ أثناء تحديث الدرس: ' . $e->getMessage();
        }
        
        header("Location: /admin/courses/{$courseId}/lessons");
        exit;
    }
    
    /**
     * Get lesson attachments (API endpoint)
     */
    public function getAttachments($courseId, $lessonId) {
        // Verify the lesson exists and belongs to this course
        $stmt = $this->db->prepare("
            SELECT l.id 
            FROM lessons l
            JOIN courses c ON l.course_id = c.id
            WHERE l.id = ? AND l.course_id = ?
        ");
        $stmt->execute([$lessonId, $courseId]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$lesson) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'الدرس غير موجود']);
            exit;
        }
        
        // Get attachments
        $stmt = $this->db->prepare("
            SELECT id, title, file_path, file_type, file_size, created_at
            FROM lesson_attachments
            WHERE lesson_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$lessonId]);
        $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'attachments' => $attachments
        ]);
        exit;
    }
    
    /**
     * Delete a lesson
     */
    public function delete($courseId, $lessonId) {
        // Check if user is admin
        $isAdmin = $this->currentAdmin['role'] === 'admin';
        
        // Verify the course exists and belongs to the current admin if not an admin
        if ($isAdmin) {
            $stmt = $this->db->prepare("SELECT id FROM courses WHERE id = ?");
            $stmt->execute([$courseId]);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM courses WHERE id = ? AND admin_id = ?");
            $stmt->execute([$courseId, $this->currentAdmin['id']]);
        }
        
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$course) {
            // Course not found or doesn't belong to this admin
            header('Location: /admin/courses');
            exit;
        }
        
        // Delete the lesson
        $stmt = $this->db->prepare("DELETE FROM lessons WHERE id = ? AND course_id = ?");
        $result = $stmt->execute([$lessonId, $courseId]);
        
        if ($result) {
            $_SESSION['success'] = 'تم حذف الدرس بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء حذف الدرس';
        }
        
        header("Location: /admin/courses/{$courseId}/lessons");
        exit;
    }
    
    /**
     * Reorder lessons
     */
    public function reorder($courseId) {
        // Verify the course belongs to the current admin
        $stmt = $this->db->prepare("SELECT id FROM courses WHERE id = ? AND admin_id = ?");
        $stmt->execute([$courseId, $this->currentAdmin['id']]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$course) {
            // Course not found or doesn't belong to this admin
            echo json_encode(['success' => false, 'message' => 'الدورة غير موجودة']);
            exit;
        }
        
        // Get the lesson order data
        $lessonOrder = json_decode($_POST['lesson_order'] ?? '[]', true);
        
        if (empty($lessonOrder)) {
            echo json_encode(['success' => false, 'message' => 'بيانات الترتيب غير صحيحة']);
            exit;
        }
        
        // Update the order of each lesson
        $this->db->beginTransaction();
        
        try {
            $stmt = $this->db->prepare("UPDATE lessons SET order_number = ? WHERE id = ? AND course_id = ?");
            
            foreach ($lessonOrder as $order => $lessonId) {
                $stmt->execute([$order + 1, $lessonId, $courseId]);
            }
            
            $this->db->commit();
            echo json_encode(['success' => true, 'message' => 'تم تحديث ترتيب الدروس بنجاح']);
        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء تحديث ترتيب الدروس']);
        }
        
        exit;
    }
}