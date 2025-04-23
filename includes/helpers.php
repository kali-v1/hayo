<?php
/**
 * Helper Functions
 * 
 * This file contains helper functions for the application.
 */

/**
 * Translate a string to the current language
 * 
 * @param string $key The translation key
 * @return string The translated string
 */
function translate($key) {
    global $translations;
    
    if (isset($translations[$key])) {
        return $translations[$key];
    }
    
    return $key;
}

/**
 * Set a flash message to be displayed on the next page load
 * 
 * @param string $message The message to display
 * @param string $type The type of message (success, error, warning, info)
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Get the flash message and clear it from the session
 * 
 * @return array|null The flash message or null if none exists
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flashMessage = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flashMessage;
    }
    
    return null;
}

/**
 * Format a date
 * 
 * @param string $date The date to format
 * @param string $format The format to use
 * @return string The formatted date
 */
function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

/**
 * Format a number as currency
 * 
 * @param float $amount The amount to format
 * @param string $currency The currency code (default: SAR)
 * @return string The formatted amount
 */
function formatCurrency($amount, $currency = 'SAR') {
    $symbols = [
        'SAR' => 'SAR',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£'
    ];
    
    $symbol = isset($symbols[$currency]) ? $symbols[$currency] : $currency;
    
    if ($_SESSION['lang'] === 'ar') {
        return number_format($amount, 2) . ' ' . $symbol;
    } else {
        return $symbol . ' ' . number_format($amount, 2);
    }
}

/**
 * Sanitize input data
 * 
 * @param mixed $data The data to sanitize
 * @return mixed The sanitized data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitizeInput($value);
        }
    } else {
        $data = htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    return $data;
}

/**
 * Get the current URL
 * 
 * @return string The current URL
 */
function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    
    return $protocol . '://' . $host . $uri;
}

/**
 * Get the base URL
 * 
 * @return string The base URL
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    return $protocol . '://' . $host;
}

/**
 * Add language parameter to URL
 * 
 * @param string $lang The language code
 * @return string The URL with language parameter
 */
function addLanguageToUrl($lang) {
    $url = $_SERVER['REQUEST_URI'];
    $parsedUrl = parse_url($url);
    $path = $parsedUrl['path'];
    $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
    
    parse_str($query, $queryParams);
    $queryParams['lang'] = $lang;
    
    // Remove page parameter to reset pagination when changing language
    if (isset($queryParams['page'])) {
        unset($queryParams['page']);
    }
    
    $newQuery = http_build_query($queryParams);
    
    return $path . ($newQuery ? '?' . $newQuery : '');
}

/**
 * Get the current language
 * 
 * @return string The current language code
 */
function getCurrentLanguage() {
    return isset($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULT_LANGUAGE;
}

/**
 * Get the text direction based on the current language
 * 
 * @return string The text direction (ltr or rtl)
 */
function getDirection() {
    return getCurrentLanguage() === 'ar' ? 'rtl' : 'ltr';
}

/**
 * Check if the current language is RTL
 * 
 * @return bool True if the current language is RTL, false otherwise
 */
function isRtl() {
    return getDirection() === 'rtl';
}

/**
 * Check if a string is valid JSON
 * 
 * @param string $string The string to check
 * @return bool True if the string is valid JSON, false otherwise
 */
function isValidJson($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Generate a random string
 * 
 * @param int $length The length of the string
 * @return string The generated string
 */
function generateRandomString($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $string = '';
    
    for ($i = 0; $i < $length; $i++) {
        $string .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    return $string;
}

/**
 * Get the file extension from a file name
 * 
 * @param string $filename The file name
 * @return string The file extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Generate a unique file name
 * 
 * @param string $originalName The original file name
 * @return string The unique file name
 */
function generateUniqueFileName($originalName) {
    $extension = getFileExtension($originalName);
    return uniqid() . '_' . time() . '.' . $extension;
}

/**
 * Check if a file is an image
 * 
 * @param string $filename The file name
 * @return bool True if the file is an image, false otherwise
 */
function isImage($filename) {
    $extension = getFileExtension($filename);
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    
    return in_array($extension, $imageExtensions);
}

/**
 * Truncate a string to a specified length
 * 
 * @param string $string The string to truncate
 * @param int $length The maximum length
 * @param string $append The string to append if truncated
 * @return string The truncated string
 */
function truncateString($string, $length = 100, $append = '...') {
    if (mb_strlen($string, 'UTF-8') > $length) {
        $string = mb_substr($string, 0, $length, 'UTF-8') . $append;
    }
    
    return $string;
}

/**
 * Convert a string to a URL-friendly slug
 * 
 * @param string $string The string to convert
 * @return string The slug
 */
function slugify($string) {
    // Replace non-alphanumeric characters with hyphens
    $string = preg_replace('/[^\p{L}\p{N}]+/u', '-', $string);
    // Remove hyphens from the beginning and end
    $string = trim($string, '-');
    // Convert to lowercase
    $string = mb_strtolower($string, 'UTF-8');
    
    return $string;
}

/**
 * Get the time elapsed since a date
 * 
 * @param string $datetime The date and time
 * @return string The time elapsed
 */
function timeElapsedString($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    
    $string = [
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    
    if (!$string) {
        return 'just now';
    }
    
    return array_shift($string) . ' ago';
}

/**
 * Calculate the percentage
 * 
 * @param float $value The value
 * @param float $total The total
 * @param int $decimals The number of decimal places
 * @return float The percentage
 */
function calculatePercentage($value, $total, $decimals = 0) {
    if ($total == 0) {
        return 0;
    }
    
    return round(($value / $total) * 100, $decimals);
}

/**
 * Check if the current page matches a given route
 * 
 * @param string $route The route to check
 * @return bool True if the current page matches the route, false otherwise
 */
function isCurrentPage($route) {
    $currentRoute = $_SERVER['REQUEST_URI'];
    
    // Remove query string
    if (strpos($currentRoute, '?') !== false) {
        $currentRoute = substr($currentRoute, 0, strpos($currentRoute, '?'));
    }
    
    // Exact match
    if ($currentRoute === $route) {
        return true;
    }
    
    // Check if the route is a prefix of the current route
    if ($route !== '/' && strpos($currentRoute, $route) === 0) {
        return true;
    }
    
    return false;
}

/**
 * Get the pagination HTML
 * 
 * @param int $currentPage The current page
 * @param int $totalPages The total number of pages
 * @param string $baseUrl The base URL for pagination links
 * @return string The pagination HTML
 */
function getPagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
    } else {
        $html .= '<li class="page-item disabled"><a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=1">1</a></li>';
        if ($startPage > 2) {
            $html .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $html .= '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $html .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
    } else {
        $html .= '<li class="page-item disabled"><a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}
?>