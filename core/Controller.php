<?php
/**
 * Base Controller Class
 * Dashboard Stok Bibit Persemaian Indonesia
 */

class Controller {
    protected $view;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->view = new View();
    }
    
    /**
     * Load model
     * 
     * @param string $model Model name
     * @return object
     */
    protected function model($model) {
        $modelPath = MODELS_PATH . $model . '.php';
        
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        }
        
        logError("Model not found: $model");
        die("Model not found: $model");
    }
    
    /**
     * Render view
     * 
     * @param string $view View name
     * @param array $data Data to pass to view
     * @param string $layout Layout to use
     */
    protected function render($view, $data = [], $layout = 'main') {
        $this->view->render($view, $data, $layout);
    }
    
    /**
     * Return JSON response
     * 
     * @param mixed $data Data to return
     * @param int $statusCode HTTP status code
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Redirect to URL
     * 
     * @param string $path Path to redirect to
     */
    protected function redirect($path = '') {
        redirect($path);
    }
    
    /**
     * Check if user is authenticated
     * 
     * @param string $role Required role (optional)
     * @return bool
     */
    protected function requireAuth($role = null) {
        if (!isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('auth/login');
            return false;
        }
        
        if ($role && !hasRole($role)) {
            $this->redirect('auth/unauthorized');
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate CSRF token
     * 
     * @return bool
     */
    protected function validateCSRF() {
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        
        if (!verifyCSRFToken($token)) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return false;
        }
        
        return true;
    }
    
    /**
     * Get POST data
     * 
     * @param string $key Key to get (optional)
     * @param mixed $default Default value
     * @return mixed
     */
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Get GET data
     * 
     * @param string $key Key to get (optional)
     * @param mixed $default Default value
     * @return mixed
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Set flash message
     * 
     * @param string $type Message type (success, error, warning, info)
     * @param string $message Message text
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Get and clear flash message
     * 
     * @return array|null
     */
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
    
    /**
     * Validate required fields
     * 
     * @param array $data Data to validate
     * @param array $required Required fields
     * @return array Validation errors
     */
    protected function validateRequired($data, $required) {
        $errors = [];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate email
     * 
     * @param string $email Email to validate
     * @return bool
     */
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Upload file
     * 
     * @param array $file File from $_FILES
     * @param string $destination Destination directory
     * @param array $allowedTypes Allowed file types
     * @return string|bool Filename or false
     */
    protected function uploadFile($file, $destination = UPLOAD_PATH, $allowedTypes = ALLOWED_FILE_TYPES) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return false;
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            return false;
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedTypes)) {
            return false;
        }
        
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $destination . $filename;
        
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }
        
        return false;
    }
}
