<?php
/**
 * API Stock Controller
 * Handles incoming push stock from 3rd party (integration)
 */
require_once CORE_PATH . 'ApiController.php';

class ApiStockController extends ApiController {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Endpoint: POST /api/stock/push
     * Accepts JSON: { "nursery_id": 1, "seedling_type_id": 5, "quantity": 100, "notes": "...", "last_sync_timestamp": "2026-03-12 10:00:00" }
     */
    public function push() {
        // Enforce POST method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method Not Allowed. Use POST.'], 405);
        }
        
        // 1. Authenticate via Header
        $apiKeyData = $this->requireApiKey();
        $authBpdasId = $apiKeyData['bpdas_id'];
        $authNurseryId = $apiKeyData['nursery_id']; // Can be null if key is for entire BPDAS
        
        // 2. Parse JSON Payload
        $payload = $this->getJsonBody();
        
        // 3. Validation
        $requiredFields = ['nursery_id', 'seedling_type_id', 'quantity'];
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field]) || $payload[$field] === '') {
                $this->json(['success' => false, 'message' => "Missing required field: {$field}"], 400);
            }
        }
        
        $targetNurseryId = (int)$payload['nursery_id'];
        $seedlingTypeId = (int)$payload['seedling_type_id'];
        $quantity = (int)$payload['quantity'];
        $notes = isset($payload['notes']) ? $payload['notes'] : null;
        $lastSync = isset($payload['last_sync_timestamp']) ? $payload['last_sync_timestamp'] : null;
        
        // Validate quantity
        if ($quantity < 0) {
            $this->json(['success' => false, 'message' => 'Quantity cannot be negative'], 400);
        }
        
        // 4. Authorization Check
        // Does this API Key have access to manipulate this nursery_id?
        if ($authNurseryId !== null) {
            // Key is locked to a specific nursery
            if ($authNurseryId != $targetNurseryId) {
                $this->json(['success' => false, 'message' => 'Forbidden: This API Key is not authorized for nursery_id ' . $targetNurseryId], 403);
            }
        }
        
        // Even if authNurseryId is null (allows all under BPDAS), we must verify the target nursery belongs to authBpdasId
        $nurseryModel = $this->model('Nursery');
        $targetNursery = $nurseryModel->find($targetNurseryId);
        
        if (!$targetNursery) {
            $this->json(['success' => false, 'message' => 'Nursery not found'], 404);
        }
        
        if ($targetNursery['bpdas_id'] != $authBpdasId) {
            $this->json(['success' => false, 'message' => 'Forbidden: Nursery does not belong to your BPDAS'], 403);
        }
        
        // Validate seedling type exists
        $seedlingModel = $this->model('SeedlingType');
        $seedling = $seedlingModel->find($seedlingTypeId);
        if (!$seedling) {
            $this->json(['success' => false, 'message' => 'Seedling Type not found'], 404);
        }
        
        // 5. Perform Upsert
        try {
            $stockModel = $this->model('Stock');
            $result = $stockModel->upsertApiStock(
                $authBpdasId,
                $targetNurseryId,
                $seedlingTypeId,
                $quantity,
                $notes,
                $lastSync
            );
            
            if ($result) {
                $this->json([
                    'success' => true, 
                    'message' => 'Stock successfully synchronized',
                    'data' => [
                        'nursery_id' => $targetNurseryId,
                        'seedling_type_id' => $seedlingTypeId,
                        'quantity_recorded' => $quantity,
                        'sync_time' => date('Y-m-d H:i:s')
                    ]
                ], 200);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to save stock data'], 500);
            }
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}
