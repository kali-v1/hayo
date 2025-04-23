    /**
     * Delete a course attachment
     */
    public function deleteAttachment() {
        global $conn;
        
        // Get the current admin
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        $isAdmin = $currentAdmin['role'] === 'admin';
        
        // Get attachment ID and course ID
        $attachment_id = $_POST['attachment_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        
        if (empty($attachment_id) || empty($course_id)) {
            // Set error message
            setFlashMessage('بيانات غير صالحة', 'danger');
            
            // Redirect back
            header('Location: /admin/courses');
            exit;
        }
        
        // Check if the course exists and belongs to the instructor (if not admin)
        if ($conn) {
            try {
                $query = "SELECT * FROM courses WHERE id = ?";
                if (!$isAdmin) {
                    $query .= " AND admin_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$course_id, $currentAdmin['id']]);
                } else {
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$course_id]);
                }
                
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
                
                if (!$attachment) {
                    // Set error message
                    setFlashMessage('المرفق غير موجود', 'danger');
                    
                    // Redirect back
                    header("Location: /admin/courses/edit/$course_id");
                    exit;
                }
                
                // Delete attachment file
                $file_path = APP_ROOT . $attachment['file_path'];
                if (file_exists($file_path)) {
                    unlink($file_path);
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