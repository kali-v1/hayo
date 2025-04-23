<?php
/**
 * Admin Panel Entry Point
 *
 * This file serves as the main entry point for the admin panel of the application.
 * It initializes the admin panel, loads configuration, sets up the router,
 * and handles all admin panel requests.
 */

// Start session
@ini_set('session.save_path', sys_get_temp_dir());
session_start();

// Define the application root path
define('APP_ROOT', dirname(__DIR__));
define('ADMIN_ROOT', __DIR__);

// Set default timezone
date_default_timezone_set('UTC');

// Load configuration
require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/config/database.php';

// Load helper functions
require_once APP_ROOT . '/includes/helpers.php';
require_once ADMIN_ROOT . '/includes/admin_helpers.php';
require_once APP_ROOT . '/includes/ti/template.php';

// Load core classes
require_once APP_ROOT . '/classes/Database.php';
require_once APP_ROOT . '/classes/Router.php';
require_once ADMIN_ROOT . '/classes/Admin.php';
require_once ADMIN_ROOT . '/classes/AdminAuth.php';
require_once ADMIN_ROOT . '/classes/AdminLogger.php';
require_once APP_ROOT . '/classes/Company.php';
require_once APP_ROOT . '/classes/User.php';
require_once APP_ROOT . '/classes/Course.php';
require_once APP_ROOT . '/classes/Exam.php';

// Initialize database connection
try {
    $dbConnection = new DatabaseConnection();
    $conn = $dbConnection->getConnection();
} catch (Exception $e) {
    // Log the error
    error_log("Database connection error: " . $e->getMessage());
    
    // Continue without database connection
    $conn = null;
}

// Initialize router
$router = new Router();

// Check if admin is logged in
$adminAuth = new AdminAuth();
$isLoggedIn = $adminAuth->isLoggedIn();

// Initialize current admin variable
$currentAdmin = null;
if ($isLoggedIn) {
    $currentAdmin = $adminAuth->getCurrentAdmin();
}

// Define routes
if (!$isLoggedIn && !in_array($_SERVER['REQUEST_URI'], ['/admin/login', '/admin/auth'])) {
    // Redirect to login page if not logged in
    header('Location: /admin/login');
    exit;
}

// Check permissions for protected routes
if ($isLoggedIn) {
    $currentAdmin = $adminAuth->getCurrentAdmin();
    $isAdmin = $currentAdmin['role'] === 'admin';
    $currentPath = $_SERVER['REQUEST_URI'];
    
    // Get admin role
    $adminRole = $currentAdmin['role'];
    
    // Check if the current path requires specific permissions
    $hasAccess = false;
    
    // Dashboard access for all authenticated users
    if ($currentPath === '/admin' || $currentPath === '/admin/') {
        $hasAccess = true;
    }
    // Admin has access to everything
    else if ($isAdmin) {
        $hasAccess = true;
    } 
    // Data entry has access to questions, specific exam routes, profile and logout only
    else if ($adminRole === 'data_entry') {
        // Data entry users can access these routes
        if (strpos($currentPath, '/admin/questions') === 0 || 
            $currentPath === '/admin/exams/create' || 
            $currentPath === '/admin/exams/store' || 
            strpos($currentPath, '/admin/profile') === 0 ||
            strpos($currentPath, '/admin/logout') === 0) {
            $hasAccess = true;
        }
        
        // Explicitly deny access to employees routes
        if (strpos($currentPath, '/admin/employees') === 0) {
            $hasAccess = false;
        }
    }
    // Instructors have limited access
    else if ($adminRole === 'instructor') {
        // Instructors don't have access to these routes
        if (strpos($currentPath, '/admin/exams') === 0 || 
            strpos($currentPath, '/admin/questions') === 0 || 
            strpos($currentPath, '/admin/users') === 0 ||
            strpos($currentPath, '/admin/employees') === 0) {
            $hasAccess = false;
        } 
        // Special case for course and lesson attachment deletion
        else if ($currentPath === '/admin/courses/delete-attachment' || $currentPath === '/admin/lessons/delete-attachment') {
            $hasAccess = true;
        }
        else {
            $hasAccess = true;
        }
    }
    
    // If no access, show error and redirect
    if (!$hasAccess) {
        // Set error message
        setAdminFlashMessage('غير مصرح لك بالوصول إلى هذه الصفحة', 'error');
        
        // Redirect to dashboard
        header('Location: /admin');
        exit;
    }
}

// Public routes (no authentication required)
$router->addRoute('/admin/login', 'AdminAuthController@showLogin');
$router->addRoute('/admin/auth', 'AdminAuthController@login', 'POST');

// Protected routes (authentication required)
$router->addRoute('/admin', 'AdminDashboardController@index');
$router->addRoute('/admin/logout', 'AdminAuthController@logout');

// Users management
$router->addRoute('/admin/users', 'AdminUsersController@index');
$router->addRoute('/admin/users/search-suggestions', 'AdminUsersController@searchSuggestions');
$router->addRoute('/admin/users/create', 'AdminUsersController@create');
$router->addRoute('/admin/users/store', 'AdminUsersController@store', 'POST');
$router->addRoute('/admin/users/view/{id}', 'AdminUsersController@view');
$router->addRoute('/admin/users/edit/{id}', 'AdminUsersController@edit');
$router->addRoute('/admin/users/update/{id}', 'AdminUsersController@update', 'POST');
$router->addRoute('/admin/users/delete/{id}', 'AdminUsersController@delete');

// Employees management
$router->addRoute('/admin/employees', 'AdminEmployeesController@index');
$router->addRoute('/admin/employees/create', 'AdminEmployeesController@create');
$router->addRoute('/admin/employees/store', 'AdminEmployeesController@store', 'POST');
$router->addRoute('/admin/employees/view/{id}', 'AdminEmployeesController@view');
$router->addRoute('/admin/employees/edit/{id}', 'AdminEmployeesController@edit');
$router->addRoute('/admin/employees/update/{id}', 'AdminEmployeesController@update', 'POST');
$router->addRoute('/admin/employees/delete/{id}', 'AdminEmployeesController@delete');

// Companies management
$router->addRoute('/admin/companies', 'AdminCompaniesController@index');
$router->addRoute('/admin/companies/create', 'AdminCompaniesController@create');
$router->addRoute('/admin/companies/store', 'AdminCompaniesController@store', 'POST');
$router->addRoute('/admin/companies/view/{id}', 'AdminCompaniesController@view');
$router->addRoute('/admin/companies/edit/{id}', 'AdminCompaniesController@edit');
$router->addRoute('/admin/companies/update/{id}', 'AdminCompaniesController@update', 'POST');
$router->addRoute('/admin/companies/delete/{id}', 'AdminCompaniesController@delete');

// Courses management
$router->addRoute('/admin/courses', 'AdminCoursesController@index');
$router->addRoute('/admin/courses/search-suggestions', 'AdminCoursesController@searchSuggestions');
$router->addRoute('/admin/courses/create', 'AdminCoursesController@create');
$router->addRoute('/admin/courses/store', 'AdminCoursesController@store', 'POST');
$router->addRoute('/admin/courses/view/{id}', 'AdminCoursesController@view');
$router->addRoute('/admin/courses/edit/{id}', 'AdminCoursesController@edit');
$router->addRoute('/admin/courses/update/{id}', 'AdminCoursesController@update', 'POST');
$router->addRoute('/admin/courses/delete/{id}', 'AdminCoursesController@delete');
$router->addRoute('/admin/courses/delete-attachment', 'AdminCoursesController@deleteAttachment', 'POST');

// Lessons management
$router->addRoute('/admin/lessons/delete-attachment', function() use ($conn, $currentAdmin) {
    $lessonsController = new LessonsController($conn, $currentAdmin);
    $lessonsController->deleteAttachment();
}, 'POST');

$router->addRoute('/admin/courses/{courseId}/lessons', function($courseId) use ($conn, $currentAdmin) {
    $lessonsController = new LessonsController($conn, $currentAdmin);
    $lessonsController->index($courseId);
});
$router->addRoute('/admin/courses/{courseId}/lessons/create', function($courseId) use ($conn, $currentAdmin) {
    $lessonsController = new LessonsController($conn, $currentAdmin);
    $lessonsController->create($courseId);
});
$router->addRoute('/admin/courses/{courseId}/lessons/store', function($courseId) use ($conn, $currentAdmin) {
    $lessonsController = new LessonsController($conn, $currentAdmin);
    $lessonsController->store($courseId);
}, 'POST');
$router->addRoute('/admin/courses/{courseId}/lessons/{lessonId}/edit', function($courseId, $lessonId) use ($conn, $currentAdmin) {
    $lessonsController = new LessonsController($conn, $currentAdmin);
    $lessonsController->edit($courseId, $lessonId);
});
$router->addRoute('/admin/courses/{courseId}/lessons/{lessonId}/update', function($courseId, $lessonId) use ($conn, $currentAdmin) {
    $lessonsController = new LessonsController($conn, $currentAdmin);
    $lessonsController->update($courseId, $lessonId);
}, 'POST');
$router->addRoute('/admin/courses/{courseId}/lessons/{lessonId}/delete', function($courseId, $lessonId) use ($conn, $currentAdmin) {
    $lessonsController = new LessonsController($conn, $currentAdmin);
    $lessonsController->delete($courseId, $lessonId);
}, 'POST');
$router->addRoute('/admin/courses/{courseId}/lessons/reorder', function($courseId) use ($conn, $currentAdmin) {
    $lessonsController = new LessonsController($conn, $currentAdmin);
    $lessonsController->reorder($courseId);
}, 'POST');
$router->addRoute('/admin/courses/{courseId}/lessons/{lessonId}/attachments', function($courseId, $lessonId) use ($conn, $currentAdmin) {
    $lessonsController = new LessonsController($conn, $currentAdmin);
    $lessonsController->getAttachments($courseId, $lessonId);
});

// Exams management
$router->addRoute('/admin/exams', 'AdminExamController@index');
$router->addRoute('/admin/exams/search-suggestions', 'AdminExamController@searchSuggestions');
$router->addRoute('/admin/exams/create', 'AdminExamController@create');
$router->addRoute('/admin/exams/store', 'AdminExamController@store', 'POST');
$router->addRoute('/admin/exams/view/{id}', 'AdminExamController@view');
$router->addRoute('/admin/exams/edit/{id}', 'AdminExamController@edit');
$router->addRoute('/admin/exams/update/{id}', 'AdminExamController@update', 'POST');
$router->addRoute('/admin/exams/delete/{id}', 'AdminExamController@delete');

// Activity Logs
$router->addRoute('/admin/activity-logs', 'AdminActivityLogController@index');

// Questions management
$router->addRoute('/admin/questions', 'AdminQuestionController@index');
$router->addRoute('/admin/questions/create', 'AdminQuestionController@create');
$router->addRoute('/admin/questions/store', 'AdminQuestionController@store', 'POST');
$router->addRoute('/admin/questions/view/{id}', 'AdminQuestionController@view');
$router->addRoute('/admin/questions/edit/{id}', 'AdminQuestionController@edit');
$router->addRoute('/admin/questions/update/{id}', 'AdminQuestionController@update', 'POST');
$router->addRoute('/admin/questions/delete/{id}', 'AdminQuestionController@delete');

// Admin profile
$router->addRoute('/admin/profile', 'AdminProfileController@index');
$router->addRoute('/admin/profile/update', 'AdminProfileController@update', 'POST');
$router->addRoute('/admin/profile/password', 'AdminProfileController@updatePassword', 'POST');

// Handle 404 errors
$router->setNotFoundHandler(function() {
    http_response_code(404);
    include ADMIN_ROOT . '/templates/404.php';
});

// Load controllers
require_once ADMIN_ROOT . '/controllers/AdminAuthController.php';
require_once ADMIN_ROOT . '/controllers/AdminDashboardController.php';
require_once ADMIN_ROOT . '/controllers/AdminUsersController.php';
require_once ADMIN_ROOT . '/controllers/AdminEmployeesController.php';
require_once ADMIN_ROOT . '/controllers/AdminCompaniesController.php';
require_once ADMIN_ROOT . '/controllers/AdminCoursesController.php';
require_once ADMIN_ROOT . '/controllers/AdminExamController.php';
require_once ADMIN_ROOT . '/controllers/AdminQuestionController.php';
require_once ADMIN_ROOT . '/controllers/AdminProfileController.php';
require_once ADMIN_ROOT . '/controllers/AdminActivityLogController.php';
require_once ADMIN_ROOT . '/controllers/LessonsController.php';

// Dispatch the request
$router->dispatch();
?>
