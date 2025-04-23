<?php
/**
 * Admin Helper Functions
 * 
 * This file contains helper functions used in the admin panel.
 */

/**
 * Check if admin has permission
 * 
 * @param string $permission Permission to check
 * @return bool True if admin has permission, false otherwise
 */
function adminHasPermission($permission) {
    if (!isset($_SESSION['admin_role'])) {
        return false;
    }
    
    $role = $_SESSION['admin_role'];
    
    // Admin has all permissions
    if ($role === 'admin') {
        return true;
    }
    
    // Role-based permissions
    $permissions = [
        'instructor' => ['manage_courses', 'manage_exams'],
        'data_entry' => ['manage_questions']
    ];
    
    return isset($permissions[$role]) && in_array($permission, $permissions[$role]);
}

/**
 * Get admin sidebar menu
 * 
 * @return array Sidebar menu items
 */
function getAdminSidebarMenu() {
    $menu = [];
    
    // Dashboard (all roles)
    $menu[] = [
        'title' => 'لوحة التحكم',
        'icon' => 'dashboard',
        'url' => '/admin',
        'permission' => null
    ];
    
    // Courses (admin and instructor)
    $menu[] = [
        'title' => 'الدورات',
        'icon' => 'book',
        'url' => '/admin/courses',
        'permission' => 'manage_courses'
    ];
    
    // Exams (admin and instructor)
    $menu[] = [
        'title' => 'الاختبارات',
        'icon' => 'file-alt',
        'url' => '/admin/exams',
        'permission' => 'manage_courses'
    ];
    
    // Questions (admin and data entry)
    $menu[] = [
        'title' => 'الأسئلة',
        'icon' => 'question-circle',
        'url' => '/admin/questions',
        'permission' => 'manage_questions'
    ];
    
    // Users (admin only)
    $menu[] = [
        'title' => 'المستخدمين',
        'icon' => 'users',
        'url' => '/admin/users',
        'permission' => 'manage_users'
    ];
    
    // Companies (admin only)
    $menu[] = [
        'title' => 'الشركات',
        'icon' => 'building',
        'url' => '/admin/companies',
        'permission' => 'manage_users'
    ];
    
    // Employees (admin only)
    $menu[] = [
        'title' => 'الموظفين',
        'icon' => 'user-tie',
        'url' => '/admin/employees',
        'permission' => 'manage_admins'
    ];
    
    // Settings (admin only)
    $menu[] = [
        'title' => 'الإعدادات',
        'icon' => 'cog',
        'url' => '/admin/settings',
        'permission' => 'manage_settings'
    ];
    
    // Filter menu based on permissions
    return array_filter($menu, function($item) {
        return $item['permission'] === null || adminHasPermission($item['permission']);
    });
}

/**
 * Get admin role name in Arabic
 * 
 * @param string $role Role code
 * @return string Role name in Arabic
 */
function getAdminRoleName($role) {
    $roles = [
        'admin' => 'مدير',
        'data_entry' => 'مدخل بيانات',
        'instructor' => 'مدرب'
    ];
    
    return isset($roles[$role]) ? $roles[$role] : $role;
}

/**
 * Format date in Arabic
 * 
 * @param string $date Date string
 * @param string $format Date format
 * @return string Formatted date
 */
function formatArabicDate($date, $format = 'Y-m-d H:i:s') {
    $datetime = new DateTime($date);
    
    // Convert to Arabic numerals
    $formatted = $datetime->format($format);
    $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    
    return str_replace($western, $eastern, $formatted);
}

/**
 * Get question type name in Arabic
 * 
 * @param string $type Question type
 * @return string Question type in Arabic
 */
function getQuestionTypeName($type) {
    $types = [
        'single_choice' => 'اختيار واحد',
        'multiple_choice' => 'اختيار متعدد',
        'drag_drop' => 'سحب وإفلات'
    ];
    
    return isset($types[$type]) ? $types[$type] : $type;
}

/**
 * Check if admin is super admin
 * 
 * @return bool True if admin is super admin, false otherwise
 */
function isSuperAdmin() {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin';
}

/**
 * Get admin flash message
 * 
 * @return array|null Flash message or null if no message
 */
function getAdminFlashMessage() {
    if (isset($_SESSION['admin_flash_message'])) {
        $message = $_SESSION['admin_flash_message'];
        unset($_SESSION['admin_flash_message']);
        return $message;
    }
    
    return null;
}

/**
 * Set admin flash message
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message text
 * @return void
 */
function setAdminFlashMessage($type, $message) {
    $_SESSION['admin_flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get course statistics
 * 
 * @param int $courseId Course ID
 * @return array Course statistics
 */
function getCourseStatistics($courseId) {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get total enrollments
    $query = "SELECT COUNT(*) as total FROM user_courses WHERE course_id = :course_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':course_id', $courseId);
    $stmt->execute();
    $enrollments = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get average rating
    $query = "SELECT AVG(rating) as average FROM course_ratings WHERE course_id = :course_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':course_id', $courseId);
    $stmt->execute();
    $averageRating = round((float) $stmt->fetch(PDO::FETCH_ASSOC)['average'], 1);
    
    // Get total exams
    $query = "SELECT COUNT(*) as total FROM exams WHERE course_id = :course_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':course_id', $courseId);
    $stmt->execute();
    $totalExams = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    return [
        'enrollments' => $enrollments,
        'average_rating' => $averageRating,
        'total_exams' => $totalExams
    ];
}

/**
 * Get exam statistics
 * 
 * @param int $examId Exam ID
 * @return array Exam statistics
 */
function getExamStatistics($examId) {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get total attempts
    $query = "SELECT COUNT(*) as total FROM exam_attempts WHERE exam_id = :exam_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':exam_id', $examId);
    $stmt->execute();
    $totalAttempts = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get average score
    $query = "SELECT AVG(score) as average FROM exam_attempts WHERE exam_id = :exam_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':exam_id', $examId);
    $stmt->execute();
    $averageScore = round((float) $stmt->fetch(PDO::FETCH_ASSOC)['average'], 1);
    
    // Get pass rate
    $query = "SELECT e.passing_score, COUNT(a.id) as passed 
              FROM exams e 
              LEFT JOIN exam_attempts a ON e.id = a.exam_id AND a.score >= e.passing_score 
              WHERE e.id = :exam_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':exam_id', $examId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $passRate = $totalAttempts > 0 ? round(($result['passed'] / $totalAttempts) * 100, 1) : 0;
    
    return [
        'total_attempts' => $totalAttempts,
        'average_score' => $averageScore,
        'pass_rate' => $passRate
    ];
}
?>