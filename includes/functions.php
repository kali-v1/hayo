<?php
/**
 * Helper Functions
 * 
 * This file contains helper functions used throughout the application.
 */

/**
 * Redirect to a URL
 * 
 * @param string $url URL to redirect to
 * @return void
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Get current URL
 * 
 * @return string Current URL
 */
function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    
    return "$protocol://$host$uri";
}

/**
 * Get base URL
 * 
 * @return string Base URL
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    return "$protocol://$host";
}

/**
 * Format date
 * 
 * @param string $date Date string
 * @param string $format Date format
 * @return string Formatted date
 */
function formatDate($date, $format = 'Y-m-d H:i:s') {
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Sanitize input
 * 
 * @param string $input Input to sanitize
 * @return string Sanitized input
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags($input));
}

/**
 * Sanitize input for forms
 * 
 * @param string $input Input to sanitize
 * @return string Sanitized input
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitizeInput($value);
        }
        return $input;
    }
    
    return trim(htmlspecialchars(strip_tags($input)));
}

/**
 * Generate random string
 * 
 * @param int $length Length of the string
 * @return string Random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

/**
 * Check if string is valid JSON
 * 
 * @param string $string String to check
 * @return bool True if valid JSON, false otherwise
 */
function isJson($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Get current language
 * 
 * @return string Current language code
 */
function getCurrentLanguage() {
    return isset($_SESSION['language']) ? $_SESSION['language'] : Config::DEFAULT_LANGUAGE;
}

/**
 * Set language
 * 
 * @param string $language Language code
 * @return void
 */
function setLanguage($language) {
    if (in_array($language, Config::LANGUAGES)) {
        $_SESSION['language'] = $language;
    }
}

/**
 * Translate text
 * 
 * @param string $key Translation key
 * @param array $params Parameters for translation
 * @return string Translated text
 */
function translate($key, $params = []) {
    $language = getCurrentLanguage();
    
    // Load language file
    $langFile = "languages/$language.php";
    
    if (file_exists($langFile)) {
        include_once $langFile;
    } else {
        // Fallback to default language
        include_once "languages/" . Config::DEFAULT_LANGUAGE . ".php";
    }
    
    // Get translation
    $translation = isset($translations[$key]) ? $translations[$key] : $key;
    
    // Replace parameters
    foreach ($params as $param => $value) {
        $translation = str_replace(":$param", $value, $translation);
    }
    
    return $translation;
}

/**
 * Format currency
 * 
 * @param float $amount Amount
 * @param string $currency Currency code
 * @return string Formatted currency
 */
function formatCurrency($amount, $currency = 'USD') {
    $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    return $formatter->formatCurrency($amount, $currency);
}

/**
 * Check if current page is RTL
 * 
 * @return bool True if RTL, false otherwise
 */
function isRtl() {
    return getCurrentLanguage() === 'ar';
}

/**
 * Get RTL/LTR direction
 * 
 * @return string 'rtl' or 'ltr'
 */
function getDirection() {
    return isRtl() ? 'rtl' : 'ltr';
}

/**
 * Flash message
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message text
 * @return void
 */
function flashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Set flash message
 * 
 * @param string $message Message text
 * @param string $type Message type (success, error, warning, info)
 * @return void
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get flash message
 * 
 * @return array|null Flash message or null if no message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    
    return null;
}

/**
 * Check if user has access to a course
 * 
 * @param int $courseId Course ID
 * @param int $userId User ID
 * @return bool True if user has access, false otherwise
 */
function hasAccessToCourse($courseId, $userId) {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if course is free
    $query = "SELECT is_free FROM courses WHERE id = :course_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':course_id', $courseId);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row && $row['is_free'] == 1) {
        return true;
    }
    
    // Check if user has purchased the course
    $query = "SELECT COUNT(*) as count FROM user_courses WHERE user_id = :user_id AND course_id = :course_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':course_id', $courseId);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return (int) $row['count'] > 0;
}

/**
 * Check if user has access to an exam
 * 
 * @param int $examId Exam ID
 * @param int $userId User ID
 * @return bool True if user has access, false otherwise
 */
function hasAccessToExam($examId, $userId) {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if exam is free
    $query = "SELECT is_free, course_id FROM exams WHERE id = :exam_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':exam_id', $examId);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row && $row['is_free'] == 1) {
        return true;
    }
    
    // Check if user has access to the course
    return hasAccessToCourse($row['course_id'], $userId);
}

/**
 * Get user's exam attempts
 * 
 * @param int $examId Exam ID
 * @param int $userId User ID
 * @return array Array of exam attempts
 */
function getUserExamAttempts($examId, $userId) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM exam_attempts 
              WHERE exam_id = :exam_id AND user_id = :user_id 
              ORDER BY created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':exam_id', $examId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Convert a string to camelCase
 * 
 * @param string $string String to convert
 * @return string Converted string
 */
function camelCase($string) {
    // Remove non-alphanumeric characters and replace with spaces
    $string = preg_replace('/[^a-zA-Z0-9]/', ' ', $string);
    
    // Convert to lowercase
    $string = strtolower($string);
    
    // Capitalize each word except the first one
    $string = lcfirst(str_replace(' ', '', ucwords($string)));
    
    return $string;
}

/**
 * Calculate exam score
 * 
 * @param array $answers User's answers
 * @param array $questions Exam questions
 * @return array Score information
 */
function calculateExamScore($answers, $questions) {
    $totalPoints = 0;
    $earnedPoints = 0;
    $correctAnswers = 0;
    $incorrectAnswers = 0;
    
    foreach ($questions as $question) {
        $totalPoints += $question['points'];
        
        if (isset($answers[$question['id']])) {
            $userAnswer = $answers[$question['id']];
            $correctAnswer = json_decode($question['correct_answer'], true);
            
            $isCorrect = false;
            
            switch ($question['question_type']) {
                case 'single_choice':
                    $isCorrect = $userAnswer == $correctAnswer;
                    break;
                    
                case 'multiple_choice':
                    sort($userAnswer);
                    sort($correctAnswer);
                    $isCorrect = $userAnswer == $correctAnswer;
                    break;
                    
                case 'drag_drop':
                    $isCorrect = $userAnswer == $correctAnswer;
                    break;
            }
            
            if ($isCorrect) {
                $earnedPoints += $question['points'];
                $correctAnswers++;
            } else {
                $incorrectAnswers++;
            }
        } else {
            $incorrectAnswers++;
        }
    }
    
    $percentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
    
    return [
        'total_points' => $totalPoints,
        'earned_points' => $earnedPoints,
        'percentage' => $percentage,
        'correct_answers' => $correctAnswers,
        'incorrect_answers' => $incorrectAnswers,
        'total_questions' => count($questions)
    ];
}
?>