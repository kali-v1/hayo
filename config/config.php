<?php
/**
 * Application Configuration
 * 
 * This file contains the configuration settings for the application.
 */

// Application settings
define('APP_NAME', 'Certification Platform');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost');
define('APP_EMAIL', 'info@certificationplatform.com');

// Base URLs
define('BASE_URL', '/');
define('ADMIN_URL', '/admin');

// Default language
define('DEFAULT_LANGUAGE', 'en');

// Available languages
define('AVAILABLE_LANGUAGES', ['en', 'ar']);

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip']);

// Pagination settings
define('ITEMS_PER_PAGE', 10);

// Security settings
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_LIFETIME', 7200); // 2 hours
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_OPTIONS', ['cost' => 12]);

// Payment settings
define('PAYMENT_GATEWAY', 'stripe');
define('PAYMENT_CURRENCY', 'SAR');
define('PAYMENT_TEST_MODE', true);

// Email settings
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'noreply@example.com');
define('SMTP_PASSWORD', '');
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_FROM_EMAIL', 'noreply@example.com');
define('SMTP_FROM_NAME', 'Certification Platform');

// Social media settings
define('FACEBOOK_URL', 'https://www.facebook.com/certificationplatform');
define('TWITTER_URL', 'https://twitter.com/certplatform');
define('INSTAGRAM_URL', 'https://www.instagram.com/certificationplatform');
define('LINKEDIN_URL', 'https://www.linkedin.com/company/certificationplatform');

// Error reporting
ini_set('display_errors', 1); // Set to 1 for development, 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Create logs directory if it doesn't exist
if (!file_exists(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}

// Time zone
date_default_timezone_set('UTC');

// Session settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
}
?>