<?php
/**
 * Admin Helper Functions
 * 
 * This file contains helper functions specific to the admin panel.
 */

/**
 * Get the admin sidebar menu items based on the admin's role
 * 
 * @return array The sidebar menu items
 */
function getAdminSidebarMenu() {
    $menu = [];
    
    // Dashboard (available to all roles)
    $menu[] = [
        'title' => 'لوحة التحكم',
        'url' => '/admin',
        'icon' => 'tachometer-alt'
    ];
    
    // Users management (admin only)
    if (adminHasPermission('manage_users')) {
        $menu[] = [
            'title' => 'المستخدمين',
            'url' => '/admin/users',
            'icon' => 'users'
        ];
    }
    
    // Courses management (admin and instructor)
    if (adminHasPermission('manage_courses') || adminHasPermission('manage_own_courses')) {
        $menu[] = [
            'title' => 'الدورات',
            'url' => '/admin/courses',
            'icon' => 'book'
        ];
    }
    
    // Exams management (admin only)
    if (adminHasPermission('manage_exams')) {
        $menu[] = [
            'title' => 'الاختبارات',
            'url' => '/admin/exams',
            'icon' => 'file-alt'
        ];
    }
    
    // Questions management (admin and data entry only)
    if (adminHasPermission('manage_questions')) {
        $menu[] = [
            'title' => 'الأسئلة',
            'url' => '/admin/questions',
            'icon' => 'question-circle'
        ];
    }
    
    // Profile (available to all roles)
    $menu[] = [
        'title' => 'الملف الشخصي',
        'url' => '/admin/profile',
        'icon' => 'user-cog'
    ];
    
    // Logout removed from sidebar menu to avoid duplication
    
    return $menu;
}

/**
 * Check if the admin has a specific permission
 * 
 * @param string $permission The permission to check
 * @return bool True if the admin has the permission, false otherwise
 */
function adminHasPermission($permission) {
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_role'])) {
        return false;
    }
    
    $adminRole = $_SESSION['admin_role'];
    
    // Admin has all permissions
    if ($adminRole === 'admin') {
        return true;
    }
    
    // Define permissions for each role
    $permissions = [
        'admin' => ['manage_users', 'manage_courses', 'manage_exams', 'manage_questions', 'view_all_courses', 'view_all_stats', 'approve_courses'],
        'instructor' => ['manage_own_courses', 'view_own_stats'],
        'data_entry' => ['manage_questions']
    ];
    
    // Check if the admin's role has the requested permission
    return isset($permissions[$adminRole]) && in_array($permission, $permissions[$adminRole]);
}

/**
 * Set a flash message to be displayed on the next page load
 * 
 * @param string $message The message to display
 * @param string $type The type of message (success, error, warning, info)
 */
function setAdminFlashMessage($message, $type = 'info') {
    $_SESSION['admin_flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Get the flash message and clear it from the session
 * 
 * @return array|null The flash message or null if none exists
 */
function getAdminFlashMessage() {
    if (isset($_SESSION['admin_flash_message'])) {
        $flashMessage = $_SESSION['admin_flash_message'];
        unset($_SESSION['admin_flash_message']);
        return $flashMessage;
    }
    
    return null;
}

/**
 * Format a date in Arabic format
 * 
 * @param string $date The date to format
 * @param string $format The format to use
 * @return string The formatted date
 */
function formatArabicDate($date, $format = 'Y-m-d H:i:s') {
    $timestamp = strtotime($date);
    
    // Arabic month names
    $months = [
        'يناير', 'فبراير', 'مارس', 'إبريل', 'مايو', 'يونيو',
        'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
    ];
    
    // Arabic day names
    $days = [
        'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'
    ];
    
    $formatted = date($format, $timestamp);
    
    // Replace month names
    if (strpos($format, 'F') !== false) {
        $monthIndex = date('n', $timestamp) - 1;
        $formatted = str_replace(date('F', $timestamp), $months[$monthIndex], $formatted);
    }
    
    // Replace short month names
    if (strpos($format, 'M') !== false) {
        $monthIndex = date('n', $timestamp) - 1;
        $formatted = str_replace(date('M', $timestamp), mb_substr($months[$monthIndex], 0, 3, 'UTF-8'), $formatted);
    }
    
    // Replace day names
    if (strpos($format, 'l') !== false) {
        $dayIndex = date('w', $timestamp);
        $formatted = str_replace(date('l', $timestamp), $days[$dayIndex], $formatted);
    }
    
    // Replace short day names
    if (strpos($format, 'D') !== false) {
        $dayIndex = date('w', $timestamp);
        $formatted = str_replace(date('D', $timestamp), mb_substr($days[$dayIndex], 0, 3, 'UTF-8'), $formatted);
    }
    
    return $formatted;
}

/**
 * Sanitize input data
 * 
 * @param mixed $data The data to sanitize
 * @return mixed The sanitized data
 */
function sanitizeAdminInput($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitizeAdminInput($value);
        }
    } else {
        $data = htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    return $data;
}

/**
 * Generate a random password
 * 
 * @param int $length The length of the password
 * @return string The generated password
 */
function generateRandomPassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    return $password;
}

/**
 * Check if a string is valid JSON
 * 
 * @param string $string The string to check
 * @return bool True if the string is valid JSON, false otherwise
 */
if (!function_exists('isValidJson')) {
    function isValidJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}

/**
 * Get the admin role name in Arabic
 * 
 * @param string $role The role code
 * @return string The role name in Arabic
 */
function getAdminRoleName($role) {
    $roles = [
        'admin' => 'مدير',
        'instructor' => 'مدرب',
        'data_entry' => 'مدخل بيانات'
    ];
    
    return isset($roles[$role]) ? $roles[$role] : $role;
}

/**
 * Format a number as currency
 * 
 * @param float $amount The amount to format
 * @param string $currency The currency code (default: SAR)
 * @return string The formatted amount
 */
function formatAdminCurrency($amount, $currency = 'SAR') {
    $symbols = [
        'SAR' => 'ر.س',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£'
    ];
    
    $symbol = isset($symbols[$currency]) ? $symbols[$currency] : $currency;
    
    return number_format($amount, 2) . ' ' . $symbol;
}

/**
 * Get the file extension from a file name
 * 
 * @param string $filename The file name
 * @return string The file extension
 */
if (!function_exists('getFileExtension')) {
    function getFileExtension($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}

/**
 * Generate a unique file name
 * 
 * @param string $originalName The original file name
 * @return string The unique file name
 */
if (!function_exists('generateUniqueFileName')) {
function generateUniqueFileName($originalName) {
    $extension = getFileExtension($originalName);
    return uniqid() . '_' . time() . '.' . $extension;
}
}

/**
 * Check if a file is an image
 * 
 * @param string $filename The file name
 * @return bool True if the file is an image, false otherwise
 */
if (!function_exists('isImage')) {
function isImage($filename) {
    $extension = getFileExtension($filename);
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    
    return in_array($extension, $imageExtensions);
}
}

/**
 * Truncate a string to a specified length
 * 
 * @param string $string The string to truncate
 * @param int $length The maximum length
 * @param string $append The string to append if truncated
 * @return string The truncated string
 */
if (!function_exists('truncateString')) {
function truncateString($string, $length = 100, $append = '...') {
    if (mb_strlen($string, 'UTF-8') > $length) {
        $string = mb_substr($string, 0, $length, 'UTF-8') . $append;
    }
    
    return $string;
}
}
?>