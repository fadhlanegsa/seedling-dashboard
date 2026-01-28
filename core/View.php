<?php
/**
 * View Class
 * Dashboard Stok Bibit Persemaian Indonesia
 */

class View {
    /**
     * Render view with layout
     * 
     * @param string $view View file name
     * @param array $data Data to pass to view
     * @param string $layout Layout file name
     */
    public function render($view, $data = [], $layout = 'main') {
        // Include view helpers
        $helperFile = VIEWS_PATH . 'helpers/view_helpers.php';
        if (file_exists($helperFile)) {
            require_once $helperFile;
        }
        
        // Extract data to variables
        extract($data);
        
        // Get flash message if exists
        $flash = $this->getFlash();
        
        // Start output buffering
        ob_start();
        
        // Include view file
        $viewFile = VIEWS_PATH . str_replace('.', '/', $view) . '.php';
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            logError("View not found: $view");
            die("View not found: $view");
        }
        
        // Get view content
        $content = ob_get_clean();
        
        // Include layout if specified
        if ($layout) {
            $layoutFile = VIEWS_PATH . 'layouts/' . $layout . '.php';
            
            if (file_exists($layoutFile)) {
                include $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }
    
    /**
     * Render partial view (without layout)
     * 
     * @param string $partial Partial view name
     * @param array $data Data to pass to view
     */
    public function partial($partial, $data = []) {
        extract($data);
        
        $partialFile = VIEWS_PATH . str_replace('.', '/', $partial) . '.php';
        
        if (file_exists($partialFile)) {
            include $partialFile;
        } else {
            logError("Partial not found: $partial");
        }
    }
    
    /**
     * Get and clear flash message
     * 
     * @return array|null
     */
    private function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
    
    /**
     * Escape HTML output
     * 
     * @param string $string String to escape
     * @return string
     */
    public function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate URL
     * 
     * @param string $path Path
     * @return string
     */
    public function url($path = '') {
        return url($path);
    }
    
    /**
     * Generate asset URL
     * 
     * @param string $path Asset path
     * @return string
     */
    public function asset($path) {
        return asset($path);
    }
    
    /**
     * Check if current page matches path
     * 
     * @param string $path Path to check
     * @return bool
     */
    public function isActive($path) {
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = BASE_PATH;
        $currentPath = str_replace($basePath, '', $currentPath);
        
        return strpos($currentPath, $path) === 0;
    }
    
    /**
     * Get active class if current page matches
     * 
     * @param string $path Path to check
     * @param string $class Class to return if active
     * @return string
     */
    public function activeClass($path, $class = 'active') {
        return $this->isActive($path) ? $class : '';
    }
}
