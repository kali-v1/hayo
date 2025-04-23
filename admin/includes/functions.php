<?php
/**
 * Admin Helper Functions
 * 
 * This file contains helper functions used in the admin panel.
 */

/**
 * Get admin sidebar menu
 * 
 * @return array Sidebar menu items
 */
function getAdminSidebarMenu() {
    return [
        [
            'title' => 'لوحة التحكم',
            'url' => '/admin',
            'icon' => 'tachometer-alt'
        ],
        [
            'title' => 'المستخدمين',
            'url' => '/admin/users',
            'icon' => 'users'
        ],
        [
            'title' => 'الدورات',
            'url' => '/admin/courses',
            'icon' => 'book'
        ],
        [
            'title' => 'الاختبارات',
            'url' => '/admin/exams',
            'icon' => 'file-alt'
        ],
        [
            'title' => 'الأسئلة',
            'url' => '/admin/questions',
            'icon' => 'question-circle'
        ],
        [
            'title' => 'التسجيلات',
            'url' => '/admin/enrollments',
            'icon' => 'user-graduate'
        ],
        [
            'title' => 'المدفوعات',
            'url' => '/admin/payments',
            'icon' => 'money-bill-wave'
        ],
        [
            'title' => 'الإعدادات',
            'url' => '/admin/settings',
            'icon' => 'cog'
        ]
    ];
}

/**
 * Get admin role name
 * 
 * @param string $role Role code
 * @return string Role name
 */
function getAdminRoleName($role) {
    $roles = [
        'admin' => 'مدير النظام',
        'instructor' => 'مدرب',
        'data_entry' => 'مدخل بيانات'
    ];
    
    return $roles[$role] ?? 'مستخدم';
}

/**
 * Get admin flash message
 * 
 * @return array|null Flash message or null if no message
 */
function getAdminFlashMessage() {
    if (isset($_SESSION['flash_message']) && isset($_SESSION['flash_type'])) {
        $message = [
            'message' => $_SESSION['flash_message'],
            'type' => $_SESSION['flash_type']
        ];
        
        // Clear the flash message
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return $message;
    }
    
    return null;
}

/**
 * Format file size
 * 
 * @param int $bytes Size in bytes
 * @return string Formatted size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Get course status label
 * 
 * @param string $status Status code
 * @return string HTML status label
 */
function getCourseStatusLabel($status) {
    $labels = [
        'published' => '<span class="neo-badge neo-badge-success">منشور</span>',
        'draft' => '<span class="neo-badge neo-badge-warning">مسودة</span>',
        'archived' => '<span class="neo-badge neo-badge-secondary">مؤرشف</span>'
    ];
    
    return $labels[$status] ?? '<span class="neo-badge neo-badge-info">' . $status . '</span>';
}

/**
 * Get payment status label
 * 
 * @param string $status Status code
 * @return string HTML status label
 */
function getPaymentStatusLabel($status) {
    $labels = [
        'completed' => '<span class="neo-badge neo-badge-success">مكتمل</span>',
        'pending' => '<span class="neo-badge neo-badge-warning">قيد الانتظار</span>',
        'failed' => '<span class="neo-badge neo-badge-danger">فشل</span>',
        'refunded' => '<span class="neo-badge neo-badge-info">مسترجع</span>'
    ];
    
    return $labels[$status] ?? '<span class="neo-badge neo-badge-secondary">' . $status . '</span>';
}

/**
 * Get user status label
 * 
 * @param string $status Status code
 * @return string HTML status label
 */
function getUserStatusLabel($status) {
    $labels = [
        'active' => '<span class="neo-badge neo-badge-success">نشط</span>',
        'inactive' => '<span class="neo-badge neo-badge-warning">غير نشط</span>',
        'banned' => '<span class="neo-badge neo-badge-danger">محظور</span>'
    ];
    
    return $labels[$status] ?? '<span class="neo-badge neo-badge-secondary">' . $status . '</span>';
}

/**
 * Get exam status label
 * 
 * @param string $status Status code
 * @return string HTML status label
 */
function getExamStatusLabel($status) {
    $labels = [
        'published' => '<span class="neo-badge neo-badge-success">منشور</span>',
        'draft' => '<span class="neo-badge neo-badge-warning">مسودة</span>',
        'archived' => '<span class="neo-badge neo-badge-secondary">مؤرشف</span>'
    ];
    
    return $labels[$status] ?? '<span class="neo-badge neo-badge-info">' . $status . '</span>';
}

/**
 * Get exam attempt status label
 * 
 * @param bool $isPassed Whether the attempt was passed
 * @return string HTML status label
 */
function getExamAttemptStatusLabel($isPassed) {
    return $isPassed 
        ? '<span class="neo-badge neo-badge-success">ناجح</span>' 
        : '<span class="neo-badge neo-badge-danger">راسب</span>';
}

/**
 * Get course type label
 * 
 * @param bool $isFree Whether the course is free
 * @return string HTML type label
 */
function getCourseTypeLabel($isFree) {
    return $isFree 
        ? '<span class="neo-badge neo-badge-success">مجاني</span>' 
        : '<span class="neo-badge neo-badge-primary">مدفوع</span>';
}

/**
 * Format admin date
 * 
 * @param string $date Date string
 * @param string $format Date format
 * @return string Formatted date
 */
function formatAdminDate($date, $format = 'Y-m-d') {
    if (empty($date)) {
        return '';
    }
    
    $datetime = new DateTime($date);
    return $datetime->format($format);
}