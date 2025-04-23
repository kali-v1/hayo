<?php
/**
 * Config Class
 * 
 * This class provides access to application configuration settings.
 */
class Config {
    /**
     * Application name
     */
    const APP_NAME = 'Certification Platform';
    
    /**
     * Application version
     */
    const APP_VERSION = '1.0.0';
    
    /**
     * Default language
     */
    const DEFAULT_LANGUAGE = 'en';
    
    /**
     * Available languages
     */
    const LANGUAGES = ['en', 'ar'];
    
    /**
     * Password minimum length
     */
    const PASSWORD_MIN_LENGTH = 8;
    
    /**
     * Session lifetime in seconds
     */
    const SESSION_LIFETIME = 7200; // 2 hours
    
    /**
     * CSRF token name
     */
    const CSRF_TOKEN_NAME = 'csrf_token';
    
    /**
     * Get a configuration value
     * 
     * @param string $key Configuration key
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value
     */
    public static function get($key, $default = null) {
        if (defined($key)) {
            return constant($key);
        }
        
        return $default;
    }
}