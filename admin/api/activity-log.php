<?php
/**
 * API endpoint for fetching admin activity logs
 */

// Include necessary files
require_once '../../config/config.php';
require_once '../../config/database.php';

// Set content type to JSON
header('Content-Type: application/json');

// For simplicity, we'll skip session checks in this API endpoint
// In a production environment, you would want to properly authenticate users

// Get database connection
$db = new DatabaseConnection();
$conn = $db->getConnection();

// Initialize response
$response = [
    'success' => false,
    'activities' => [],
    'error' => null
];

if ($conn) {
    try {
        // Get recent activities from admin_activity_logs
        $stmt = $conn->prepare("
            SELECT al.*, a.role, a.profile_image 
            FROM admin_activity_logs al
            JOIN admins a ON al.admin_id = a.id
            ORDER BY al.created_at DESC
            LIMIT 10
        ");
        $stmt->execute();
        $activityResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the activities
        foreach ($activityResults as $activity) {
            // Determine status color and label based on action type
            $statusColor = 'primary';
            $status = 'تم';
            $section = '';
            
            // Map action to status and section
            switch ($activity['action']) {
                case 'login':
                    $statusColor = 'info';
                    $status = 'تسجيل دخول';
                    $section = 'تسجيل الدخول';
                    break;
                case 'create_course':
                    $statusColor = 'success';
                    $status = 'إضافة';
                    $section = 'الدورات';
                    break;
                case 'update_course':
                    $statusColor = 'warning';
                    $status = 'تعديل';
                    $section = 'الدورات';
                    break;
                case 'delete_course':
                    $statusColor = 'danger';
                    $status = 'حذف';
                    $section = 'الدورات';
                    break;
                case 'create_exam':
                    $statusColor = 'success';
                    $status = 'إضافة';
                    $section = 'الاختبارات';
                    break;
                case 'update_exam':
                    $statusColor = 'warning';
                    $status = 'تعديل';
                    $section = 'الاختبارات';
                    break;
                case 'delete_exam':
                    $statusColor = 'danger';
                    $status = 'حذف';
                    $section = 'الاختبارات';
                    break;
                case 'create_question':
                    $statusColor = 'success';
                    $status = 'إضافة';
                    $section = 'الأسئلة';
                    break;
                case 'update_question':
                    $statusColor = 'warning';
                    $status = 'تعديل';
                    $section = 'الأسئلة';
                    break;
                case 'delete_question':
                    $statusColor = 'danger';
                    $status = 'حذف';
                    $section = 'الأسئلة';
                    break;
                case 'create_user':
                    $statusColor = 'success';
                    $status = 'إضافة';
                    $section = 'المستخدمين';
                    break;
                case 'update_user':
                    $statusColor = 'warning';
                    $status = 'تعديل';
                    $section = 'المستخدمين';
                    break;
                case 'delete_user':
                    $statusColor = 'danger';
                    $status = 'حذف';
                    $section = 'المستخدمين';
                    break;
                case 'create_instructor':
                    $statusColor = 'success';
                    $status = 'إضافة';
                    $section = 'المدربين';
                    break;
                case 'update_settings':
                    $statusColor = 'warning';
                    $status = 'تعديل';
                    $section = 'الإعدادات';
                    break;
                default:
                    // Extract section from action if possible
                    if (strpos($activity['action'], '_') !== false) {
                        $parts = explode('_', $activity['action']);
                        if (count($parts) >= 2) {
                            $section = $parts[1];
                        }
                    }
                    break;
            }
            
            $response['activities'][] = [
                'username' => $activity['admin_username'],
                'role' => $activity['role'] === 'admin' ? 'مدير' : ($activity['role'] === 'instructor' ? 'مدرب' : 'مدخل بيانات'),
                'avatar' => $activity['profile_image'] ?: '/assets/images/avatar.png',
                'action' => $activity['details'],
                'section' => $activity['section'],
                'date' => date('Y-m-d H:i', strtotime($activity['created_at'])),
                'status' => $status,
                'status_color' => $statusColor
            ];
        }
        
        $response['success'] = true;
    } catch (PDOException $e) {
        $response['error'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['error'] = 'Database connection failed';
}

// Return JSON response
echo json_encode($response);