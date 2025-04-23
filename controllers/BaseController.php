<?php
/**
 * Base Controller
 * 
 * This is the base controller that all other controllers should extend.
 * It provides common functionality for all controllers.
 */
class BaseController {
    /**
     * Set up common variables for views
     * 
     * @return array Common variables for views
     */
    protected function setupViewVariables() {
        global $lang;
        
        // Set the appropriate direction based on language
        $direction = $lang === 'ar' ? 'rtl' : 'ltr';
        
        return [
            'lang' => $lang,
            'direction' => $direction,
            'pageTitle' => null
        ];
    }
    
    /**
     * Render a view with the given data
     * 
     * @param string $view The view to render
     * @param array $data The data to pass to the view
     */
    protected function render($view, $data = []) {
        // Extract common variables
        $commonVars = $this->setupViewVariables();
        
        // Merge common variables with view-specific data
        $viewData = array_merge($commonVars, $data);
        
        // Extract variables to make them available in the view
        extract($viewData);
        
        // Set the content template for the layout
        $contentTemplate = __DIR__ . '/../templates/' . $view . '.php';
        
        // Check if the template exists
        if (!file_exists($contentTemplate)) {
            error_log("Template not found: $contentTemplate");
            $contentTemplate = __DIR__ . '/../templates/404.php';
        }
        
        // Include the layout
        include __DIR__ . '/../templates/layout.php';
    }
}
?>