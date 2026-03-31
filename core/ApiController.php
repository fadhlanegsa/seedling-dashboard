<?php
/**
 * Base API Controller Class
 */
require_once CORE_PATH . 'Controller.php';

class ApiController extends Controller {
    protected $apiKeyData = null;
    
    public function __construct() {
        parent::__construct();
        
        // Ensure default output is JSON
        header('Content-Type: application/json');
        
        // CORS Headers
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, x-api-key, Authorization");
        
        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit(0);
        }
    }
    
    /**
     * Validate and require X-API-Key header
     * 
     * @return array API Key data (bpdas_id, nursery_id, etc.)
     */
    protected function requireApiKey() {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        
        $apiKey = '';
        if (isset($_SERVER['HTTP_X_API_KEY'])) {
            $apiKey = $_SERVER['HTTP_X_API_KEY'];
        } elseif (isset($headers['x-api-key'])) {
            $apiKey = $headers['x-api-key'];
        } elseif (isset($headers['X-Api-Key'])) {
            $apiKey = $headers['X-Api-Key'];
        }
        
        if (empty($apiKey)) {
            $this->json(['success' => false, 'message' => 'Unauthorized: API Key is missing'], 401);
        }
        
        // Verify against database
        $apiKeyModel = $this->model('ApiKey');
        $keyData = $apiKeyModel->findByKey($apiKey);
        
        if (!$keyData || !$keyData['is_active']) {
            $this->json(['success' => false, 'message' => 'Unauthorized: Invalid or inactive API Key'], 401);
        }
        
        $this->apiKeyData = $keyData;
        return $keyData;
    }
    
    /**
     * Parse JSON request body
     * 
     * @return array
     */
    protected function getJsonBody() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        return is_array($data) ? $data : [];
    }
}
