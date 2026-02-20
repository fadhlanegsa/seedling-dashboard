<?php
/**
 * Operator Controller
 * Handles nursery operator dashboard and stock management
 */

require_once CORE_PATH . 'Controller.php';

class OperatorController extends Controller {
    
    public function __construct() {
        parent::__construct();
        // Require operator authentication
        $this->requireAuth('operator_persemaian');
    }
    
    /**
     * Operator Dashboard
     */
    public function dashboard() {
        $user = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($user['id']);
        
        if (!$userData['nursery_id']) {
            // Ideally shouldn't happen if data integrity is maintained
            $this->setFlash('error', 'Akun Anda tidak terhubung dengan persemaian manapun.');
            $this->redirect('auth/login');
            return;
        }

        $stockModel = $this->model('Stock');
        $stocks = $stockModel->getByNurseryPaginated($userData['nursery_id'], 1, 100); // Get all for now or paginate
        
        $data = [
            'title' => 'Dashboard Operator Persemaian',
            'user' => $userData,
            'stocks' => $stocks,
            'nursery_name' => $userData['nursery_name'],
            'bpdas_name' => $userData['bpdas_name']
        ];
        
        $this->render('operator/dashboard', $data, 'dashboard');
    }

    /**
     * Manage Requests
     */
    public function requests() {
        $user = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($user['id']);
        
        if (!$userData['nursery_id']) {
            $this->setFlash('error', 'Akun Anda tidak terhubung dengan persemaian manapun.');
            $this->redirect('auth/login');
            return;
        }

        $requestModel = $this->model('Request');
        // Operator can see pending and approved requests assigned to their nursery
        // Actually, maybe they just need to see "approved" by BPDAS?
        // Or if the request was directly to them, it might be "pending"?
        // Let's show all for now, or filter by reasonable statuses
        
        $status = $this->get('status', 'all');
        
        if ($status === 'all') {
            $requests = $requestModel->getByNursery($userData['nursery_id']);
        } else {
            $requests = $requestModel->getByNursery($userData['nursery_id'], $status);
        }
        
        $data = [
            'title' => 'Kelola Permintaan Bibit',
            'requests' => $requests,
            'currentStatus' => $status
        ];
        
        $this->render('operator/requests', $data, 'dashboard');
    }

    /**
     * Request Detail
     */
    public function requestDetail($id) {
        $user = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($user['id']);
        
        if (!$userData['nursery_id']) {
            $this->setFlash('error', 'Akun Anda tidak terhubung dengan persemaian.');
            $this->redirect('operator/requests');
            return;
        }

        $requestModel = $this->model('Request');
        $request = $requestModel->getWithDetails($id);
        
        if (!$request || $request['nursery_id'] != $userData['nursery_id']) {
            $this->setFlash('error', 'Permintaan tidak ditemukan atau bukan untuk persemaian Anda.');
            $this->redirect('operator/requests');
            return;
        }
        
        $history = $requestModel->getHistory($id);
        
        $data = [
            'title' => 'Detail Permintaan',
            'request' => $request,
            'history' => $history
        ];
        
        $this->render('operator/request-detail', $data, 'dashboard');
    }

    /**
     * Map Distribution Visualization
     */
    public function mapDistribution() {
        $user = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($user['id']);
        
        if (!$userData['nursery_id']) {
            $this->setFlash('error', 'Akun Anda tidak terhubung dengan persemaian.');
            $this->redirect('operator/dashboard');
            return;
        }

        $seedlingTypeModel = $this->model('SeedlingType');
        $seedling_types = $seedlingTypeModel->getAllActive();
        
        $data = [
            'title' => 'Peta Distribusi Permintaan',
            'seedling_types' => $seedling_types,
            'nursery_id' => $userData['nursery_id']
        ];
        
        $this->render('operator/map-distribution', $data, 'dashboard');
    }

    /**
     * AJAX: Get Map Data for Operator
     */
    public function getMapData() {
        $user = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($user['id']);
        
        if (!$userData['nursery_id']) {
            $this->json(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $filters = [
            'nursery_id' => $userData['nursery_id'],
            'seedling_type_id' => $this->get('seedling_type_id'),
            'status' => $this->get('status')
        ];
        
        $requestModel = $this->model('Request');
        $mapData = $requestModel->getMapData($filters);
        
        $this->json([
            'success' => true,
            'data' => $mapData
        ]);
    }

    /**
     * Upload Delivery Photo (Proof of Handover)
     */
public function uploadDeliveryPhoto() {
        // Disable error display for JSON response
        $oldDisplayErrors = ini_get('display_errors');
        $oldErrorReporting = error_reporting();
        ini_set('display_errors', 0);
        error_reporting(0);
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }
        
        // Validate CSRF
        if (!isset($_POST[CSRF_TOKEN_NAME]) || $_POST[CSRF_TOKEN_NAME] !== $_SESSION[CSRF_TOKEN_NAME]) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }
        
        $user = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($user['id']);
        $nurseryId = $userData['nursery_id'];
        
        $requestId = $_POST['request_id'] ?? null;
        
        if (!$requestId || empty($_FILES['photo'])) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit;
        }
        
        $requestModel = $this->model('Request');
        $request = $requestModel->getWithDetails($requestId);
        
        if (!$request || $request['nursery_id'] != $nurseryId) {
            echo json_encode(['success' => false, 'message' => 'Permintaan tidak ditemukan']);
            exit;
        }
        
        // Handle File Upload
        try {
            $file = $_FILES['photo'];
            
            // Check for upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'Gagal mengupload file (Error code: ' . $file['error'] . ')']);
                exit;
            }
            
            // Generate unique filename
            $filename = 'delivery_' . $requestId . '_' . time() . '.webp';
            $targetPath = UPLOAD_PATH . $filename;
            
            // Compress and convert to WebP
            require_once UTILS_PATH . 'ImageCompressor.php';
            $compressor = new ImageCompressor(1200, 512000); // Max 1200px, target 500KB
            $result = $compressor->compress($file['tmp_name'], $targetPath);
            
            if (!$result['success']) {
                echo json_encode(['success' => false, 'message' => 'Gagal memproses gambar: ' . $result['message']]);
                exit;
            }
            
            // Delete old photo if exists
            if (!empty($request['delivery_photo_path'])) {
                $oldPath = UPLOAD_PATH . $request['delivery_photo_path'];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            
            // Start transaction
            $requestModel->beginTransaction();
            
            // Update request with photo path and change status to 'delivered'
            // NOTE: Stock already deducted at approval, so we just update status
            $updated = $requestModel->update($requestId, [
                'delivery_photo_path' => $filename,
                'status' => 'delivered'
            ]);
            
            if ($updated) {
                // Add history
                $requestModel->addHistory($requestId, 'delivered', $user['id'], 'Bibit diserahkan oleh operator persemaian');
                $requestModel->commit();
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Bukti serah terima berhasil diupload',
                    'data' => [
                        'filename' => $filename
                    ]
                ]);
            } else {
                $requestModel->rollback();
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data']);
            }
            
        } catch (Exception $e) {
            if (isset($requestModel)) $requestModel->rollback();
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
        
        // Restore error settings
        error_reporting($oldErrorReporting);
        ini_set('display_errors', $oldDisplayErrors);
        exit;
    }

    /**
     * Manage Stock View
     */
    public function stock() {
        $user = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($user['id']);
        $nurseryId = $userData['nursery_id'];

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $stockModel = $this->model('Stock');
        $stocks = $stockModel->getByNurseryPaginated($nurseryId, $page, 10);
        
        $data = [
            'title' => 'Kelola Stok Bibit',
            'stocks' => $stocks,
            'nursery_name' => $userData['nursery_name']
        ];
        
        $this->render('operator/stock/index', $data, 'dashboard');
    }

    /**
     * Add/Edit Stock Form
     */
    public function stockForm($id = null) {
        $user = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($user['id']);
        
        $seedlingModel = $this->model('SeedlingType');
        $seedlingTypes = $seedlingModel->all(['is_active' => 1], 'name ASC');
        
        $stock = null;
        if ($id) {
            $stockModel = $this->model('Stock');
            $stock = $stockModel->find($id);
            
            // Authorization check: Ensure stock belongs to user's nursery
            if ($stock['nursery_id'] != $userData['nursery_id']) {
                $this->setFlash('error', 'Anda tidak memiliki akses ke data ini');
                $this->redirect('operator/stock');
                return;
            }
        }
        
        $data = [
            'title' => $id ? 'Edit Stok' : 'Tambah Stok',
            'stock' => $stock,
            'seedling_types' => $seedlingTypes
        ];
        
        $this->render('operator/stock/form', $data, 'dashboard');
    }

    /**
     * Save Stock
     */
    public function saveStock() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('operator/stock');
            return;
        }
        
        if (!$this->validateCSRF()) {
            return;
        }
        
        $user = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($user['id']);
        $nurseryId = $userData['nursery_id'];
        
        $seedlingTypeId = $this->post('seedling_type_id');
        $quantity = (int)$this->post('quantity');
        $notes = $this->post('notes');
        $id = $this->post('id');
        
        if (empty($seedlingTypeId) || $quantity < 0) {
            $this->setFlash('error', 'Mohon lengkapi data dengan benar');
            $this->redirect('operator/stock/form' . ($id ? "/$id" : ''));
            return;
        }
        
        $stockModel = $this->model('Stock');
        
        try {
            if ($id) {
                // Update specific stock entry
                // Verify ownership again
                $existing = $stockModel->find($id);
                if ($existing['nursery_id'] != $nurseryId) {
                    throw new Exception("Unauthorized access");
                }
                
                $stockModel->update($id, [
                    'quantity' => $quantity,
                    'notes' => $notes,
                    'last_update_date' => date('Y-m-d')
                ]);
            } else {
                // Create or update by type
                $stockModel->updateOrCreateNurseryStock($nurseryId, $seedlingTypeId, $quantity, $notes);
            }
            
            $this->setFlash('success', 'Data stok berhasil disimpan');
            $this->redirect('operator/stock');
            
        } catch (Exception $e) {
            $this->setFlash('error', 'Gagal menyimpan data: ' . $e->getMessage());
            $this->redirect('operator/stock');
        }
    }
}
