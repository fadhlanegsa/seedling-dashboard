<?php
/**
 * BPDAS Controller
 * Handles BPDAS dashboard and operations
 */

require_once CORE_PATH . 'Controller.php';

class BPDASController extends Controller {
    
    public function __construct() {
        parent::__construct();
        // Require BPDAS authentication for all methods
        $this->requireAuth('bpdas');
    }
    
    /**
     * BPDAS Dashboard
     */
    public function dashboard() {
        $user = currentUser();
        $bpdasId = $user['bpdas_id'];
        
        $stockModel = $this->model('Stock');
        $requestModel = $this->model('Request');
        
        // Get statistics
        $stockStats = $stockModel->getBPDASStatistics($bpdasId);
        $requestStats = $requestModel->getStatistics($bpdasId);
        
        // Get recent stock updates
        $recentStock = $stockModel->getByBPDAS($bpdasId);
        $recentStock = array_slice($recentStock, 0, 5);
        
        // Get pending requests
        $pendingRequests = $requestModel->getByBPDAS($bpdasId, 'pending');

        // Get Nurseries Data
        $nurseryModel = $this->model('Nursery');
        $nurseries = $nurseryModel->getByBPDAS($bpdasId);
        
        foreach ($nurseries as &$nursery) {
            $nursery['stats'] = $stockModel->getNurseryStockSummary($nursery['id']);
            // Get top 5 stock items for this nursery
            $nurseryStock = $stockModel->getByNurseryPaginated($nursery['id'], 1, 5);
            $nursery['stock_items'] = $nurseryStock['data'];
        }
        unset($nursery);

        // Get recent deliveries (gallery)
        $recentDeliveries = $requestModel->getRecentDeliveries($bpdasId, 8); // Limit 8 for 2 rows
        
        $data = [
            'title' => 'Dashboard BPDAS',
            'stockStats' => $stockStats,
            'requestStats' => $requestStats,
            'recentStock' => $recentStock,
            'pendingRequests' => $pendingRequests,
            'nurseries' => $nurseries,
            'recentDeliveries' => $recentDeliveries
        ];
        
        $this->render('bpdas/dashboard', $data, 'dashboard');
    }
    
    /**
     * Manage stock page
     */
    public function stock() {
        $user = currentUser();
        $bpdasId = $user['bpdas_id'];
        
        $page = $this->get('page', 1);
        $perPage = $this->get('per_page', ITEMS_PER_PAGE);
        
        // Handle "all" option
        if ($perPage === 'all') {
            $perPage = 9999; // Large number to get all records
        } else {
            $perPage = (int)$perPage;
        }
        
        $filters = [
            'bpdas_id' => $bpdasId,
            'month' => $this->get('month'),
            'year' => $this->get('year')
        ];
        
        $stockModel = $this->model('Stock');
        // Using common search method to support date filtering
        $result = $stockModel->searchStockPaginated($page, $perPage, $filters);
        
        $data = [
            'title' => 'Kelola Stok Bibit',
            'stock' => $result['data'],
            'pagination' => $result,
            'currentPerPage' => $this->get('per_page', ITEMS_PER_PAGE),
            'filters' => $filters
        ];
        
        $this->render('bpdas/stock', $data, 'dashboard');
    }
    
    /**
     * Add/Edit stock form
     */
    public function stockForm($id = null) {
        $user = currentUser();
        $bpdasId = $user['bpdas_id'];
        
        $stockModel = $this->model('Stock');
        $seedlingTypeModel = $this->model('SeedlingType');
        
        $stock = null;
        if ($id) {
            $stock = $stockModel->find($id);
            // Verify ownership
            if (!$stock || $stock['bpdas_id'] != $bpdasId) {
                $this->setFlash('error', 'Stok tidak ditemukan');
                $this->redirect('bpdas/stock');
                return;
            }
        }
        
        $seedlingTypes = $seedlingTypeModel->getAllActive();
        
        $data = [
            'title' => $id ? 'Edit Stok Bibit' : 'Tambah Stok Bibit',
            'stock' => $stock,
            'seedlingTypes' => $seedlingTypes
        ];
        
        $this->render('bpdas/stock-form', $data, 'dashboard');
    }
    
    /**
     * Save stock (create or update)
     */
    public function saveStock() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('bpdas/stock');
            return;
        }
        
        if (!$this->validateCSRF()) {
            return;
        }
        
        $user = currentUser();
        $bpdasId = $user['bpdas_id'];
        
        $id = $this->post('id');
        $seedlingTypeId = $this->post('seedling_type_id');
        $quantity = $this->post('quantity');
        $notes = $this->post('notes');
        
        // Validate
        if (empty($seedlingTypeId) || empty($quantity)) {
            $this->setFlash('error', 'Jenis bibit dan jumlah harus diisi');
            $this->redirect('bpdas/stock-form' . ($id ? "/$id" : ''));
            return;
        }
        
        if (!is_numeric($quantity) || $quantity < 0) {
            $this->setFlash('error', 'Jumlah stok harus berupa angka positif');
            $this->redirect('bpdas/stock-form' . ($id ? "/$id" : ''));
            return;
        }
        
        $stockModel = $this->model('Stock');
        
        if ($id) {
            // Update existing stock
            $stock = $stockModel->find($id);
            if (!$stock || $stock['bpdas_id'] != $bpdasId) {
                $this->setFlash('error', 'Stok tidak ditemukan');
                $this->redirect('bpdas/stock');
                return;
            }
            
            $result = $stockModel->update($id, [
                'quantity' => $quantity,
                'last_update_date' => date('Y-m-d'),
                'notes' => $notes
            ]);
            
            $message = 'Stok berhasil diupdate';
        } else {
            // Create new stock or update if exists
            $result = $stockModel->updateOrCreate($bpdasId, $seedlingTypeId, $quantity, $notes);
            $message = 'Stok berhasil ditambahkan';
        }
        
        if ($result) {
            $this->setFlash('success', $message);
        } else {
            $this->setFlash('error', 'Gagal menyimpan stok');
        }
        
        $this->redirect('bpdas/stock');
    }
    
    /**
     * Delete stock
     */
    public function deleteStock($id) {
        $user = currentUser();
        $bpdasId = $user['bpdas_id'];
        
        $stockModel = $this->model('Stock');
        $stock = $stockModel->find($id);
        
        if (!$stock || $stock['bpdas_id'] != $bpdasId) {
            $this->json(['success' => false, 'message' => 'Stok tidak ditemukan']);
            return;
        }
        
        if ($stockModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'Stok berhasil dihapus']);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal menghapus stok']);
        }
    }
    
    /**
     * Incoming requests page
     */
    public function requests() {
        $user = currentUser();
        $bpdasId = $user['bpdas_id'];
        
        $status = $this->get('status');
        
        $requestModel = $this->model('Request');
        $requests = $requestModel->getByBPDAS($bpdasId, $status);
        
        $data = [
            'title' => 'Permintaan Bibit',
            'requests' => $requests,
            'currentStatus' => $status
        ];
        
        $this->render('bpdas/requests', $data, 'dashboard');
    }
    
    /**
     * Request detail page
     */
    public function requestDetail($id) {
        $user = currentUser();
        $bpdasId = $user['bpdas_id'] ?? null;
        
        // Validate ID parameter
        if (empty($id) || !is_numeric($id)) {
            $this->setFlash('error', 'ID permintaan tidak valid');
            $this->redirect('bpdas/requests');
            return;
        }
        
        // Check if user has valid BPDAS ID
        if (empty($bpdasId)) {
            $this->setFlash('error', 'Akun BPDAS tidak valid. Silakan hubungi administrator.');
            $this->redirect('bpdas/dashboard');
            return;
        }
        
        $requestModel = $this->model('Request');
        $request = $requestModel->getWithDetails($id);
        
        if (!$request) {
            $this->setFlash('error', 'Permintaan tidak ditemukan');
            $this->redirect('bpdas/requests');
            return;
        }
        
        if ($request['bpdas_id'] != $bpdasId) {
            $this->setFlash('error', 'Permintaan ini bukan untuk BPDAS Anda');
            $this->redirect('bpdas/requests');
            return;
        }
        
        // Get request history
        $history = $requestModel->getHistory($id);
        
        // Get Nurseries for assignment
        $nurseryModel = $this->model('Nursery');
        $nurseries = $nurseryModel->getByBPDAS($bpdasId);
        
        $data = [
            'title' => 'Detail Permintaan',
            'request' => $request,
            'history' => $history,
            'nurseries' => $nurseries
        ];
        
        $this->render('bpdas/request-detail', $data, 'dashboard');
    }
    
    /**
     * Approve request
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
        $bpdasId = $user['bpdas_id'];
        
        $requestId = $this->post('request_id');
        $notes = $this->post('notes');
        $nurseryIdInput = $this->post('nursery_id');
        
        $requestModel = $this->model('Request');
        $request = $requestModel->getWithDetails($requestId);
        
        if (!$request || $request['bpdas_id'] != $bpdasId) {
            $this->json(['success' => false, 'message' => 'Permintaan tidak ditemukan']);
            return;
        }
        
        if ($request['status'] !== 'pending') {
            $this->json(['success' => false, 'message' => 'Permintaan sudah diproses']);
            return;
        }

        // Determine Nursery ID
        $nurseryId = $request['nursery_id'] ? $request['nursery_id'] : $nurseryIdInput;

        if (empty($nurseryId)) {
            $this->json(['success' => false, 'message' => 'Harap pilih persemaian untuk memproses permintaan ini']);
            return;
        }
        
        // Check stock availability at specific nursery
        $stockModel = $this->model('Stock');
        $isMultiItem = !empty($request['items']);
        
        if ($isMultiItem) {
            foreach ($request['items'] as $item) {
                $stock = $stockModel->findByNurseryAndSeedling($nurseryId, $item['seedling_type_id']);
                if (!$stock || $stock['quantity'] < $item['quantity']) {
                    $this->json(['success' => false, 'message' => 'Stok tidak mencukupi di persemaian terpilih untuk salah satu jenis bibit']);
                    return;
                }
            }
        } else {
            // Legacy single item
            $stock = $stockModel->findByNurseryAndSeedling($nurseryId, $request['seedling_type_id']);
            
            if (!$stock || $stock['quantity'] < $request['quantity']) {
                $this->json(['success' => false, 'message' => 'Stok tidak mencukupi di persemaian terpilih']);
                return;
            }
        }
        
        // Begin transaction
        $requestModel->beginTransaction();
        
        try {
            // Start output buffering to catch any unwanted output
            ob_start();
            
            // Assign nursery if not set
            if (empty($request['nursery_id'])) {
                $requestModel->update($requestId, ['nursery_id' => $nurseryId]);
            }

            // Approve request
            $requestModel->approve($requestId, $user['id'], $notes);
            
            // Decrease stock from nursery
            if ($isMultiItem) {
                foreach ($request['items'] as $item) {
                    $stockModel->decreaseStockFromNursery($nurseryId, $item['seedling_type_id'], $item['quantity']);
                }
            } else {
                $stockModel->decreaseStockFromNursery($nurseryId, $request['seedling_type_id'], $request['quantity']);
            }
            
            // Add to history
            $requestModel->addHistory($requestId, 'approved', $user['id'], $notes);
            
            // Generate PDF approval letter (wrapped in try-catch)
            $pdfPath = null;
            try {
                require_once UTILS_PATH . 'PDFGenerator.php';
                $pdfGenerator = new PDFGenerator();
                $pdfPath = $pdfGenerator->generateApprovalLetter($request);
                
                // Update request with PDF path
                $requestModel->update($requestId, ['approval_letter_path' => $pdfPath]);
            } catch (Exception $pdfError) {
                // Log PDF error but don't fail the approval
                logError("PDF Generation Error: " . $pdfError->getMessage());
            }
            
            // Clean output buffer
            ob_end_clean();
            
            $requestModel->commit();
            
            $this->json(['success' => true, 'message' => 'Permintaan berhasil disetujui']);
        } catch (Exception $e) {
            // Clean output buffer on error
            ob_end_clean();
            
            $requestModel->rollback();
            logError("Approve Request Error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Gagal menyetujui permintaan']);
        }
    }
    
    /**
     * Reject request
     */
    public function rejectRequest() {
        // Start output buffering to prevent any unwanted output
        ob_start();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_end_clean();
            $this->json(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        if (!$this->validateCSRF()) {
            ob_end_clean();
            return;
        }
        
        $user = currentUser();
        $bpdasId = $user['bpdas_id'];
        
        $requestId = $this->post('request_id');
        $reason = $this->post('reason');
        
        if (empty($reason)) {
            ob_end_clean();
            $this->json(['success' => false, 'message' => 'Alasan penolakan harus diisi']);
            return;
        }
        
        $requestModel = $this->model('Request');
        $request = $requestModel->getWithDetails($requestId);
        
        if (!$request || $request['bpdas_id'] != $bpdasId) {
            ob_end_clean();
            $this->json(['success' => false, 'message' => 'Permintaan tidak ditemukan']);
            return;
        }
        
        if ($request['status'] !== 'pending') {
            ob_end_clean();
            $this->json(['success' => false, 'message' => 'Permintaan sudah diproses']);
            return;
        }
        
        // Reject request
        $rejected = $requestModel->reject($requestId, $user['id'], $reason);
        
        if ($rejected) {
            // Add to history
            $requestModel->addHistory($requestId, 'rejected', $user['id'], $reason);
            
            // Send email notification (wrapped in try-catch to prevent errors)
            try {
                require_once UTILS_PATH . 'EmailSender.php';
                $emailSender = new EmailSender();
                $emailSender->sendRejectionNotification($request, $reason);
            } catch (Exception $e) {
                // Log error but don't fail the rejection
                error_log('Email notification failed: ' . $e->getMessage());
            }
            
            // Clean buffer before sending JSON
            ob_end_clean();
            $this->json(['success' => true, 'message' => 'Permintaan berhasil ditolak']);
        } else {
            ob_end_clean();
            $this->json(['success' => false, 'message' => 'Gagal menolak permintaan']);
        }
    }
    
    /**
     * BPDAS profile page
     */
    public function profile() {
        $user = currentUser();
        $bpdasModel = $this->model('BPDAS');
        $bpdas = $bpdasModel->getWithProvince($user['bpdas_id']);
        
        $data = [
            'title' => 'Profil BPDAS',
            'user' => $user,
            'bpdas' => $bpdas
        ];
        
        $this->render('bpdas/profile', $data, 'dashboard');
    }
    
    /**
     * Update profile
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('bpdas/profile');
            return;
        }
        
        if (!$this->validateCSRF()) {
            return;
        }
        
        $user = currentUser();
        $userId = $user['id'];
        
        $data = [
            'username' => sanitize($this->post('username')),
            'full_name' => sanitize($this->post('full_name')),
            'email' => sanitize($this->post('email')),
            'phone' => sanitize($this->post('phone'))
        ];
        
        // Validate required fields
        if (empty($data['username']) || empty($data['full_name']) || empty($data['email'])) {
            $this->setFlash('error', 'Username, Nama, dan Email wajib diisi');
            $this->redirect('bpdas/profile');
            return;
        }
        
        // Validate email
        if (!$this->validateEmail($data['email'])) {
            $this->setFlash('error', 'Format email tidak valid');
            $this->redirect('bpdas/profile');
            return;
        }
        
        $userModel = $this->model('User');
        
        // Check if username exists (excluding current user)
        if ($userModel->usernameExists($data['username'], $userId)) {
            $this->setFlash('error', 'Username sudah digunakan');
            $this->redirect('bpdas/profile');
            return;
        }
        
        // Check if email exists (excluding current user)
        if ($userModel->emailExists($data['email'], $userId)) {
            $this->setFlash('error', 'Email sudah digunakan');
            $this->redirect('bpdas/profile');
            return;
        }
        
        // Update password if provided
        $newPassword = $this->post('new_password');
        if (!empty($newPassword)) {
            if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
                $this->setFlash('error', 'Password minimal ' . PASSWORD_MIN_LENGTH . ' karakter');
                $this->redirect('bpdas/profile');
                return;
            }
            
            $userModel->changePassword($userId, $newPassword);
        }
        
        if ($userModel->update($userId, $data)) {
            // Update session
            $_SESSION['user'] = array_merge($_SESSION['user'], $data);
            $this->setFlash('success', 'Profil berhasil diupdate');
        } else {
            $this->setFlash('error', 'Gagal mengupdate profil');
        }
        
        $this->redirect('bpdas/profile');
    }
    
    /**
     * Map distribution visualization page (BPDAS specific)
     */
    public function mapDistribution() {
        $user = currentUser();
        $bpdasId = $user['bpdas_id'];
        
        $bpdasModel = $this->model('BPDAS');
        $seedlingTypeModel = $this->model('SeedlingType');
        
        $bpdas = $bpdasModel->find($bpdasId);
        $seedling_types = $seedlingTypeModel->getAllActive();
        
        $data = [
            'title' => 'Peta Distribusi Bibit',
            'bpdas_name' => $bpdas['name'],
            'seedling_types' => $seedling_types
        ];
        
        $this->render('bpdas/map-distribution', $data, 'dashboard');
    }
    
    /**
     * AJAX: Get map data for BPDAS visualization
     */
    public function getMapData() {
        $user = currentUser();
        $bpdasId = $user['bpdas_id'];
        
        $filters = [
            'bpdas_id' => $bpdasId, // Always filter by current BPDAS
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
     * Upload delivery proof photo
     * Only for approved requests
     */
    public function uploadDeliveryPhoto() {
        // Suppress all errors and warnings to prevent JSON contamination
        $oldErrorReporting = error_reporting(0);
        $oldDisplayErrors = ini_get('display_errors');
        ini_set('display_errors', '0');
        
        // Start output buffering to prevent any unwanted output
        ob_start();
        
        try {
            $user = currentUser();
            $bpdasId = $user['bpdas_id'];
            
            // Get request ID
            $requestId = $this->post('request_id');
            if (empty($requestId)) {
                ob_end_clean();
                $this->json([
                    'success' => false,
                    'message' => 'Request ID tidak valid'
                ]);
                return;
            }
            
            // Get request
            $requestModel = $this->model('Request');
            $request = $requestModel->getWithDetails($requestId);
            
            if (!$request) {
                ob_end_clean();
                $this->json([
                    'success' => false,
                    'message' => 'Permintaan tidak ditemukan'
                ]);
                return;
            }
            
            // Verify request belongs to this BPDAS
            if ($request['bpdas_id'] != $bpdasId) {
                ob_end_clean();
                $this->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke permintaan ini'
                ]);
                return;
            }
            
            // Verify request is approved
            if ($request['status'] !== 'approved') {
                ob_end_clean();
                $this->json([
                    'success' => false,
                    'message' => 'Foto hanya dapat diupload untuk permintaan yang sudah disetujui'
                ]);
                return;
            }
            
            // Check if file is uploaded
            if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
                ob_end_clean();
                $this->json([
                    'success' => false,
                    'message' => 'File foto harus diupload'
                ]);
                return;
            }
            
            $file = $_FILES['photo'];
            
            // Check for upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                ob_end_clean();
                $this->json([
                    'success' => false,
                    'message' => 'Gagal mengupload file (Error code: ' . $file['error'] . ')'
                ]);
                return;
            }
            
            // Validate file type (must be image)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mimeType, $allowedTypes)) {
                ob_end_clean();
                $this->json([
                    'success' => false,
                    'message' => 'File harus berupa gambar (JPEG, PNG, GIF, atau WebP)'
                ]);
                return;
            }
            
            // Validate file size (max 10MB before compression)
            $maxSize = 10485760; // 10MB
            if ($file['size'] > $maxSize) {
                $sizeMB = round($file['size'] / 1048576, 2);
                ob_end_clean();
                $this->json([
                    'success' => false,
                    'message' => "Ukuran file terlalu besar ({$sizeMB} MB). Maksimal 10 MB"
                ]);
                return;
            }
            
            // Generate unique filename
            $filename = 'delivery_' . $requestId . '_' . time() . '.webp';
            $targetPath = UPLOAD_PATH . $filename;
            
            // Compress and convert to WebP
            require_once UTILS_PATH . 'ImageCompressor.php';
            $compressor = new ImageCompressor(1200, 512000); // Max 1200px, target 500KB
            $result = $compressor->compress($file['tmp_name'], $targetPath);
            
            if (!$result['success']) {
                ob_end_clean();
                $this->json([
                    'success' => false,
                    'message' => 'Gagal memproses gambar: ' . $result['message']
                ]);
                return;
            }
            
            // Delete old photo if exists
            if (!empty($request['delivery_photo_path'])) {
                $oldPath = UPLOAD_PATH . $request['delivery_photo_path'];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            
            // Update request with photo path and change status to 'delivered'
            $updated = $requestModel->update($requestId, [
                'delivery_photo_path' => $filename,
                'status' => 'delivered'
            ]);
            
            if (!$updated) {
                // Cleanup uploaded file
                if (file_exists($targetPath)) {
                    @unlink($targetPath);
                }
                
                ob_end_clean();
                $this->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data foto'
                ]);
                return;
            }
            
            // Clean output buffer before sending JSON
            ob_end_clean();
            
            // Restore error settings
            error_reporting($oldErrorReporting);
            ini_set('display_errors', $oldDisplayErrors);
            
            // Success
            $this->json([
                'success' => true,
                'message' => 'Foto bukti serah terima berhasil diupload',
                'data' => [
                    'filename' => $filename,
                    'size' => ImageCompressor::formatSize($result['size']),
                    'dimensions' => $result['width'] . 'x' . $result['height'],
                    'quality' => $result['quality']
                ]
            ]);
            
        } catch (Exception $e) {
            // Clean buffer
            ob_end_clean();
            
            // Restore error settings
            error_reporting($oldErrorReporting);
            ini_set('display_errors', $oldDisplayErrors);
            
            // Log error
            error_log('Upload delivery photo error: ' . $e->getMessage());
            
            // Return error
            $this->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
