<?php
/**
 * Main Entry Point for the Certification Platform
 * 
 * This file serves as the main entry point for the front-end of the application.
 * It initializes the application, loads configuration, sets up the router,
 * and handles all front-end requests.
 */

// Start session
session_start();

// Define the application root path
define('APP_ROOT', __DIR__);

// Set default timezone
date_default_timezone_set('UTC');

// Load configuration
require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/config/database.php';

// Load helper functions
require_once APP_ROOT . '/includes/helpers.php';

// Load language files
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULT_LANGUAGE);
$lang = in_array($lang, ['en', 'ar']) ? $lang : DEFAULT_LANGUAGE;
$_SESSION['lang'] = $lang;

require_once APP_ROOT . '/languages/' . $lang . '.php';

// Set the appropriate direction based on language
$direction = $lang === 'ar' ? 'rtl' : 'ltr';

// Load core classes
require_once APP_ROOT . '/classes/Database.php';
require_once APP_ROOT . '/classes/Router.php';
require_once APP_ROOT . '/classes/Auth.php';
require_once APP_ROOT . '/classes/User.php';
require_once APP_ROOT . '/classes/Course.php';
require_once APP_ROOT . '/classes/Exam.php';
require_once APP_ROOT . '/classes/Question.php';

// Initialize database connection
try {
    // Use the Database class directly instead of DatabaseConnection
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn === null) {
        error_log("Database connection is null after initialization");
    } else {
        error_log("Database connection successful");
    }
} catch (Exception $e) {
    // Log the error
    error_log("Database connection error: " . $e->getMessage());
    
    // Continue without database connection
    $conn = null;
}

// Initialize router
$router = new Router();

// Load base controller
require_once APP_ROOT . '/controllers/BaseController.php';

// Load controllers
require_once APP_ROOT . '/controllers/HomeController.php';
require_once APP_ROOT . '/controllers/AuthController.php';
require_once APP_ROOT . '/controllers/CourseController.php';
require_once APP_ROOT . '/controllers/ExamController.php';
require_once APP_ROOT . '/controllers/UserController.php';
require_once APP_ROOT . '/controllers/LeaderboardController.php';

// Define routes
$router->get('/', function() {
    $homeController = new HomeController();
    $homeController->index();
});
$router->addRoute('/login', 'AuthController@showLogin');
$router->addRoute('/login', 'AuthController@login', 'POST');
$router->addRoute('/register', 'AuthController@showRegister');
$router->addRoute('/register', 'AuthController@register', 'POST');
$router->addRoute('/logout', 'AuthController@logout');
$router->addRoute('/courses', 'CourseController@index');
$router->addRoute('/course/{id}', 'CourseController@show');

$router->addRoute('/course/{id}/enroll', 'CourseController@enroll');
$router->addRoute('/course/{id}/review', 'CourseController@addReview', 'POST');
$router->addRoute('/exams', 'ExamController@index');

$router->addRoute('/exam/{id}', 'ExamController@show');
$router->addRoute('/exam/{id}/take', 'ExamController@take');
$router->addRoute('/exam/{id}/submit', 'ExamController@submit', 'POST');
$router->addRoute('/exam/{id}/result/{attempt_id}', 'ExamController@result');

$router->addRoute('/profile', 'UserController@profile');
$router->addRoute('/profile/edit', 'UserController@editProfile');
$router->addRoute('/profile/update', 'UserController@updateProfile', 'POST');

$router->addRoute('/profile/password', 'UserController@changePassword');
$router->addRoute('/profile/password', 'UserController@updatePassword', 'POST');
$router->addRoute('/leaderboard', 'LeaderboardController@index');
$router->addRoute('/my-courses', 'UserController@myCourses');
$router->addRoute('/my-exams', 'UserController@myExams');

// Dispatch the request
$router->dispatch();

// Handle 404 errors
$router->setNotFoundHandler(function() {
    http_response_code(404);
    include APP_ROOT . '/templates/404.php';
});
?>