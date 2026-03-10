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
        
        // Fetch BPDAS delegation status
        $bpdasModel = $this->model('BPDAS');
        $bpdas = $bpdasModel->find($userData['bpdas_id']);
        
        $data = [
            'title' => 'Detail Permintaan',
            'request' => $request,
            'history' => $history,
            'can_approve' => $bpdas['can_operator_approve']
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

    /**
     * Delete Stock
     */
    public function deleteStock($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $user = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($user['id']);
        
        if (!$userData['nursery_id']) {
            $this->json(['success' => false, 'message' => 'Akses ditolak']);
            return;
        }
        
        $stockModel = $this->model('Stock');
        $stock = $stockModel->find($id);
        
        if (!$stock || $stock['nursery_id'] != $userData['nursery_id']) {
            $this->json(['success' => false, 'message' => 'Stok tidak ditemukan atau Anda tidak memiliki akses']);
            return;
        }
        
        if ($stockModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'Stok berhasil dihapus']);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal menghapus stok']);
        }
    }

    /**
     * Approve request (Delegated Authority)
     */
    public function approveRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        if (!$this->validateCSRF()) {
            return;
        }
        
        $user = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($user['id']);
        
        // CHECK DELEGATION: Get BPDAS info
        $bpdasModel = $this->model('BPDAS');
        $bpdas = $bpdasModel->find($userData['bpdas_id']);
        
        if (!$bpdas['can_operator_approve']) {
            $this->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang untuk menyetujui permintaan. Harap hubungi BPDAS.']);
            return;
        }
        
        $requestId = $this->post('request_id');
        $notes = $this->post('notes');
        
        $requestModel = $this->model('Request');
        $request = $requestModel->getWithDetails($requestId);
        
        if (!$request || $request['nursery_id'] != $userData['nursery_id']) {
            $this->json(['success' => false, 'message' => 'Permintaan tidak ditemukan']);
            return;
        }
        
        if ($request['status'] !== 'pending') {
            $this->json(['success' => false, 'message' => 'Permintaan sudah diproses']);
            return;
        }

        // Check stock availability
        $stockModel = $this->model('Stock');
        $isMultiItem = !empty($request['items']);
        
        if ($isMultiItem) {
            foreach ($request['items'] as $item) {
                $stock = $stockModel->findByNurseryAndSeedling($userData['nursery_id'], $item['seedling_type_id']);
                if (!$stock || $stock['quantity'] < $item['quantity']) {
                    $this->json(['success' => false, 'message' => 'Stok tidak mencukupi untuk salah satu jenis bibit']);
                    return;
                }
            }
        } else {
            $stock = $stockModel->findByNurseryAndSeedling($userData['nursery_id'], $request['seedling_type_id']);
            if (!$stock || $stock['quantity'] < $request['quantity']) {
                $this->json(['success' => false, 'message' => 'Stok tidak mencukupi']);
                return;
            }
        }
        
        // Begin transaction
        $requestModel->beginTransaction();
        
        try {
            // Approve request
            $requestModel->approve($requestId, $user['id'], $notes);
            
            // Decrease stock
            if ($isMultiItem) {
                foreach ($request['items'] as $item) {
                    $stockModel->decreaseStockFromNursery($userData['nursery_id'], $item['seedling_type_id'], $item['quantity']);
                }
            } else {
                $stockModel->decreaseStockFromNursery($userData['nursery_id'], $request['seedling_type_id'], $request['quantity']);
            }
            
            // Add to history
            $requestModel->addHistory($requestId, 'approved', $user['id'], $notes . ' (Disetujui oleh Operator Persemaian)');
            
            // Generate PDF approval letter (wrapped in try-catch)
            try {
                require_once UTILS_PATH . 'PDFGenerator.php';
                $pdfGenerator = new PDFGenerator();
                $pdfPath = $pdfGenerator->generateApprovalLetter($request);
                $requestModel->update($requestId, ['approval_letter_path' => $pdfPath]);
            } catch (Exception $pdfError) {
                logError("PDF Generation Error (Operator): " . $pdfError->getMessage());
            }
            
            $requestModel->commit();
            $this->json(['success' => true, 'message' => 'Permintaan berhasil disetujui']);
        } catch (Exception $e) {
            $requestModel->rollback();
            logError("Approve Request Error (Operator): " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Gagal menyetujui permintaan']);
        }
    }

    /**
     * Reject request (Delegated Authority)
     */
    public function rejectRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        if (!$this->validateCSRF()) {
            return;
        }
        
        $user = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($user['id']);
        
        // CHECK DELEGATION: Get BPDAS info
        $bpdasModel = $this->model('BPDAS');
        $bpdas = $bpdasModel->find($userData['bpdas_id']);
        
        if (!$bpdas['can_operator_approve']) {
            $this->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang untuk menolak permintaan.']);
            return;
        }
        
        $requestId = $this->post('request_id');
        $reason = $this->post('reason');
        
        if (empty($reason)) {
            $this->json(['success' => false, 'message' => 'Alasan penolakan harus diisi']);
            return;
        }
        
        $requestModel = $this->model('Request');
        $request = $requestModel->getWithDetails($requestId);
        
        if (!$request || $request['nursery_id'] != $userData['nursery_id']) {
            $this->json(['success' => false, 'message' => 'Permintaan tidak ditemukan']);
            return;
        }
        
        if ($request['status'] !== 'pending') {
            $this->json(['success' => false, 'message' => 'Permintaan sudah diproses']);
            return;
        }
        
        // Reject request
        $rejected = $requestModel->reject($requestId, $user['id'], $reason);
        
        if ($rejected) {
            $requestModel->addHistory($requestId, 'rejected', $user['id'], $reason . ' (Ditolak oleh Operator Persemaian)');
            $this->json(['success' => true, 'message' => 'Permintaan berhasil ditolak']);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal menolak permintaan']);
        }
    }

    /**
     * Operator Profile
     */
    public function profile() {
        $userSession = currentUser();
        $userModel = $this->model('User');
        $userData = $userModel->getUserWithNursery($userSession['id']);
        
        $data = [
            'title' => 'Profil Operator Persemaian',
            'user' => $userData
        ];
        
        $this->render('operator/profile', $data, 'dashboard');
    }

    /**
     * Update Operator Profile
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('operator/profile');
            return;
        }

        if (!$this->validateCSRF()) {
            return;
        }

        $userSession = currentUser();
        $userModel = $this->model('User');
        $id = $userSession['id'];

        $updateData = [
            'username' => sanitizeInput($this->post('username')),
            'full_name' => sanitizeInput($this->post('full_name')),
            'email' => filter_var($this->post('email'), FILTER_SANITIZE_EMAIL),
            'phone' => sanitizeInput($this->post('phone'))
        ];

        // Validations
        if (empty($updateData['username']) || empty($updateData['full_name']) || empty($updateData['email'])) {
            $this->setFlash('error', 'Username, Nama Lengkap, dan Email wajib diisi.');
            $this->redirect('operator/profile');
            return;
        }

        if (!filter_var($updateData['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Format email tidak valid.');
            $this->redirect('operator/profile');
            return;
        }

        // Check if username/email exists for OTHER users
        $existingUser = $userModel->findByUsernameOrEmail($updateData['username'], $updateData['email']);
        if ($existingUser && $existingUser['id'] != $id) {
            $this->setFlash('error', 'Username atau Email sudah digunakan oleh pengguna lain.');
            $this->redirect('operator/profile');
            return;
        }

        // Handle password update if provided
        $newPassword = $this->post('new_password');
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                $this->setFlash('error', 'Password baru minimal 6 karakter.');
                $this->redirect('operator/profile');
                return;
            }
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        // Update process
        if ($userModel->update($id, $updateData)) {
            // Update session data
            $_SESSION['user']['username'] = $updateData['username'];
            $_SESSION['user']['full_name'] = $updateData['full_name'];
            $_SESSION['user']['email'] = $updateData['email'];
            
            $this->setFlash('success', 'Profil berhasil diperbarui.');
        } else {
            $this->setFlash('error', 'Gagal memperbarui profil.');
        }

        $this->redirect('operator/profile');
    }
}
