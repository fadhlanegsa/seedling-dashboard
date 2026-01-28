<?php
/**
 * Public Controller
 * Handles public user dashboard and seedling requests
 */

require_once CORE_PATH . 'Controller.php';

class PublicController extends Controller {
    
    public function __construct() {
        parent::__construct();
        // Note: landing() method will be public, others require auth
        // Auth check is done in individual methods
    }
    
    /**
     * Landing page (public, no auth required)
     */
    public function landing() {
        $stockModel = $this->model('Stock');
        $requestModel = $this->model('Request');
        $bpdasModel = $this->model('BPDAS');
        $provinceModel = $this->model('Province');
        
        // Get aggregate statistics
        $stats = [
            'total_stock' => $stockModel->getTotalNationalStock(),
            'total_requests' => $requestModel->getTotalCount(),
            'total_distributed' => $requestModel->getTotalDistributed(),
            'total_bpdas' => $bpdasModel->getActiveCount(),
            'total_provinces' => $provinceModel->count(),
            'approved_requests' => $requestModel->count(['status' => 'approved']),
            'completed_requests' => $requestModel->count(['status' => 'completed'])
        ];
        
        $this->render('public/landing', ['stats' => $stats], null);
    }
    
    /**
     * Public user dashboard
     */
    public function dashboard() {
        $this->requireAuth('public');
        
        $user = currentUser();
        $requestModel = $this->model('Request');
        
        // Get user's requests
        $requests = $requestModel->getByUser($user['id']);
        
        // Get statistics
        $stats = [
            'total_requests' => count($requests),
            'pending' => count(array_filter($requests, fn($r) => $r['status'] === 'pending')),
            'approved' => count(array_filter($requests, fn($r) => $r['status'] === 'approved')),
            'rejected' => count(array_filter($requests, fn($r) => $r['status'] === 'rejected'))
        ];
        
        // Get recent requests
        $recentRequests = array_slice($requests, 0, 5);
        
        $data = [
            'title' => 'Dashboard Saya',
            'stats' => $stats,
            'recentRequests' => $recentRequests
        ];
        
        $this->render('public/dashboard', $data, 'dashboard');
    }
    
    /**
     * Request seedling form
     */
    public function requestForm() {
        $this->requireAuth('public');
        
        $provinceModel = $this->model('Province');
        $provinces = $provinceModel->getAllOrdered();
        
        $data = [
            'title' => 'Ajukan Permintaan Bibit',
            'provinces' => $provinces
        ];
        
        $this->render('public/request-form', $data, 'dashboard');
    }
    
    /**
     * Submit seedling request
     */
    public function submitRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('public/request-form');
            return;
        }
        
        if (!$this->validateCSRF()) {
            return;
        }
        
        $user = currentUser();
        
        $data = [
            'user_id' => $user['id'],
            'bpdas_id' => $this->post('bpdas_id'),
            'seedling_type_id' => $this->post('seedling_type_id'),
            'quantity' => $this->post('quantity'),
            'purpose' => sanitize($this->post('purpose')),
            'land_area' => $this->post('land_area'),
            'latitude' => $this->post('latitude'),
            'longitude' => $this->post('longitude')
        ];
        
        // Validate required fields
        $errors = $this->validateRequired($data, [
            'bpdas_id', 'seedling_type_id', 'quantity', 'purpose'
        ]);
        
        if (!empty($errors)) {
            $this->setFlash('error', 'Semua field harus diisi');
            $this->redirect('public/request-form');
            return;
        }
        
        // Validate quantity
        if (!is_numeric($data['quantity']) || $data['quantity'] <= 0) {
            $this->setFlash('error', 'Jumlah bibit harus berupa angka positif');
            $this->redirect('public/request-form');
            return;
        }
        
        // Validate coordinates
        if (empty($data['latitude']) || empty($data['longitude'])) {
            $this->setFlash('error', 'Lokasi tanam harus ditentukan pada peta');
            $this->redirect('public/request-form');
            return;
        }
        
        // Validate latitude range (-90 to 90)
        if (!is_numeric($data['latitude']) || $data['latitude'] < -90 || $data['latitude'] > 90) {
            $this->setFlash('error', 'Koordinat latitude tidak valid');
            $this->redirect('public/request-form');
            return;
        }
        
        // Validate longitude range (-180 to 180)
        if (!is_numeric($data['longitude']) || $data['longitude'] < -180 || $data['longitude'] > 180) {
            $this->setFlash('error', 'Koordinat longitude tidak valid');
            $this->redirect('public/request-form');
            return;
        }
        
        // Validate land area (required, must be > 0)
        if (empty($data['land_area']) || !is_numeric($data['land_area']) || $data['land_area'] <= 0) {
            $this->setFlash('error', 'Luas lahan wajib diisi dan harus lebih dari 0');
            $this->redirect('public/request-form');
            return;
        }
        
        // Handle proposal upload for requests > 25 seedlings
        $proposalPath = null;
        if ($data['quantity'] > 25) {
            // Check if file is uploaded
            if (!isset($_FILES['proposal']) || $_FILES['proposal']['error'] === UPLOAD_ERR_NO_FILE) {
                $this->setFlash('error', 'Permintaan bibit lebih dari 25 batang wajib melampirkan surat pengajuan/proposal');
                $this->redirect('public/request-form');
                return;
            }
            
            $file = $_FILES['proposal'];
            
            // Check for upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $this->setFlash('error', 'Gagal mengupload file proposal');
                $this->redirect('public/request-form');
                return;
            }
            
            // Validate file type (PDF only)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if ($mimeType !== 'application/pdf') {
                $this->setFlash('error', 'File proposal harus berformat PDF');
                $this->redirect('public/request-form');
                return;
            }
            
            // Validate file size (max 1MB)
            $maxSize = 1048576; // 1MB in bytes
            if ($file['size'] > $maxSize) {
                $sizeMB = round($file['size'] / 1048576, 2);
                $this->setFlash('error', "Ukuran file proposal terlalu besar ({$sizeMB} MB). Maksimal 1 MB");
                $this->redirect('public/request-form');
                return;
            }
            
            // Generate unique filename
            $extension = 'pdf';
            $filename = 'proposal_' . uniqid() . '_' . time() . '.' . $extension;
            $uploadPath = UPLOAD_PATH . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $this->setFlash('error', 'Gagal menyimpan file proposal');
                $this->redirect('public/request-form');
                return;
            }
            
            $proposalPath = $filename;
        }
        
        // Add proposal path to data
        $data['proposal_file_path'] = $proposalPath;
        
        // Check stock availability
        $stockModel = $this->model('Stock');
        $stock = $stockModel->findByBPDASAndSeedling($data['bpdas_id'], $data['seedling_type_id']);
        
        if (!$stock || $stock['quantity'] < $data['quantity']) {
            $this->setFlash('error', 'Stok bibit tidak mencukupi. Stok tersedia: ' . ($stock['quantity'] ?? 0));
            $this->redirect('public/request-form');
            return;
        }
        
        // Create request
        $requestModel = $this->model('Request');
        $requestId = $requestModel->createRequest($data);
        
        if ($requestId) {
            // Add to history
            $requestModel->addHistory($requestId, 'pending', $user['id'], 'Permintaan dibuat');
            
            // Send email notification to BPDAS
            try {
                require_once UTILS_PATH . 'EmailSender.php';
                $emailSender = new EmailSender();
                $request = $requestModel->getWithDetails($requestId);
                $emailSender->sendNewRequestNotification($request);
            } catch (Exception $e) {
                logError("Email notification error: " . $e->getMessage());
            }
            
            $this->setFlash('success', 'Permintaan berhasil diajukan. Silakan tunggu persetujuan dari BPDAS.');
            $this->redirect('public/my-requests');
        } else {
            $this->setFlash('error', 'Gagal mengajukan permintaan. Silakan coba lagi.');
            $this->redirect('public/request-form');
        }
    }
    
    /**
     * My requests page
     */
    public function myRequests() {
        $user = currentUser();
        $status = $this->get('status');
        
        $requestModel = $this->model('Request');
        $requests = $requestModel->getByUser($user['id'], $status);
        
        $data = [
            'title' => 'Permintaan Saya',
            'requests' => $requests,
            'currentStatus' => $status
        ];
        
        $this->render('public/my-requests', $data, 'dashboard');
    }
    
    /**
     * Request detail page
     */
    public function requestDetail($id) {
        $user = currentUser();
        
        $requestModel = $this->model('Request');
        $request = $requestModel->getWithDetails($id);
        
        // Verify ownership
        if (!$request || $request['user_id'] != $user['id']) {
            $this->setFlash('error', 'Permintaan tidak ditemukan');
            $this->redirect('public/my-requests');
            return;
        }
        
        // Get request history
        $history = $requestModel->getHistory($id);
        
        $data = [
            'title' => 'Detail Permintaan',
            'request' => $request,
            'history' => $history
        ];
        
        $this->render('public/request-detail', $data, 'dashboard');
    }
    
    /**
     * Download approval letter
     */
    public function downloadApprovalLetter($id) {
        $user = currentUser();
        
        $requestModel = $this->model('Request');
        $request = $requestModel->getWithDetails($id);
        
        // Verify ownership and status
        if (!$request || $request['user_id'] != $user['id']) {
            $this->setFlash('error', 'Permintaan tidak ditemukan');
            $this->redirect('public/my-requests');
            return;
        }
        
        if ($request['status'] !== 'approved') {
            $this->setFlash('error', 'Surat persetujuan hanya tersedia untuk permintaan yang disetujui');
            $this->redirect('public/request-detail/' . $id);
            return;
        }
        
        // Check if PDF exists
        if (empty($request['approval_letter_path']) || !file_exists(UPLOAD_PATH . $request['approval_letter_path'])) {
            // Generate PDF if not exists
            try {
                require_once UTILS_PATH . 'PDFGenerator.php';
                $pdfGenerator = new PDFGenerator();
                $pdfPath = $pdfGenerator->generateApprovalLetter($request);
                
                // Update request with PDF path
                $requestModel->update($id, ['approval_letter_path' => $pdfPath]);
                
                $filePath = UPLOAD_PATH . $pdfPath;
            } catch (Exception $e) {
                logError("PDF generation error: " . $e->getMessage());
                $this->setFlash('error', 'Gagal menghasilkan surat persetujuan');
                $this->redirect('public/request-detail/' . $id);
                return;
            }
        } else {
            $filePath = UPLOAD_PATH . $request['approval_letter_path'];
        }
        
        // Download file
        if (file_exists($filePath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="Surat_Persetujuan_' . $request['request_number'] . '.pdf"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            $this->setFlash('error', 'File tidak ditemukan');
            $this->redirect('public/request-detail/' . $id);
        }
    }
    
    /**
     * Profile page
     */
    public function profile() {
        $user = currentUser();
        
        $data = [
            'title' => 'Profil Saya',
            'user' => $user
        ];
        
        $this->render('public/profile', $data, 'dashboard');
    }
    
    /**
     * Update profile
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('public/profile');
            return;
        }
        
        if (!$this->validateCSRF()) {
            return;
        }
        
        $user = currentUser();
        $userId = $user['id'];
        
        $data = [
            'full_name' => sanitize($this->post('full_name')),
            'email' => sanitize($this->post('email')),
            'phone' => sanitize($this->post('phone')),
            'nik' => sanitize($this->post('nik'))
        ];
        
        // Validate email
        if (!$this->validateEmail($data['email'])) {
            $this->setFlash('error', 'Format email tidak valid');
            $this->redirect('public/profile');
            return;
        }
        
        // Validate NIK
        if (!preg_match('/^\d{16}$/', $data['nik'])) {
            $this->setFlash('error', 'NIK harus 16 digit angka');
            $this->redirect('public/profile');
            return;
        }
        
        $userModel = $this->model('User');
        
        // Check if email exists (excluding current user)
        if ($userModel->emailExists($data['email'], $userId)) {
            $this->setFlash('error', 'Email sudah digunakan');
            $this->redirect('public/profile');
            return;
        }
        
        // Update password if provided
        $newPassword = $this->post('new_password');
        if (!empty($newPassword)) {
            if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
                $this->setFlash('error', 'Password minimal ' . PASSWORD_MIN_LENGTH . ' karakter');
                $this->redirect('public/profile');
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
        
        $this->redirect('public/profile');
    }
    
    /**
     * AJAX: Get BPDAS by province
     */
    public function getBPDASByProvince() {
        $provinceId = $this->get('province_id');
        
        if (!$provinceId) {
            $this->json(['success' => false, 'message' => 'Province ID required']);
            return;
        }
        
        $bpdasModel = $this->model('BPDAS');
        $bpdasList = $bpdasModel->getByProvince($provinceId);
        
        $this->json(['success' => true, 'data' => $bpdasList]);
    }
    
    /**
     * AJAX: Get seedling types by BPDAS
     */
    public function getSeedlingsByBPDAS() {
        $bpdasId = $this->get('bpdas_id');
        
        if (!$bpdasId) {
            $this->json(['success' => false, 'message' => 'BPDAS ID required']);
            return;
        }
        
        $stockModel = $this->model('Stock');
        $stock = $stockModel->getByBPDAS($bpdasId);
        
        // Filter only available stock
        $available = array_filter($stock, fn($s) => $s['quantity'] > 0);
        
        $this->json(['success' => true, 'data' => array_values($available)]);
    }
    
    /**
     * AJAX: Check stock availability
     */
    public function checkStockAvailability() {
        $bpdasId = $this->get('bpdas_id');
        $seedlingTypeId = $this->get('seedling_type_id');
        
        if (!$bpdasId || !$seedlingTypeId) {
            $this->json(['success' => false, 'message' => 'Missing parameters']);
            return;
        }
        
        $stockModel = $this->model('Stock');
        $stock = $stockModel->findByBPDASAndSeedling($bpdasId, $seedlingTypeId);
        
        if ($stock) {
            $this->json([
                'success' => true,
                'available' => true,
                'quantity' => $stock['quantity'],
                'last_update' => formatDate($stock['last_update_date'])
            ]);
        } else {
            $this->json([
                'success' => true,
                'available' => false,
                'quantity' => 0
            ]);
        }
    }
    
    /**
     * Stock search page (public, no auth required)
     */
    public function stockSearch() {
        $provinceModel = $this->model('Province');
        $seedlingTypeModel = $this->model('SeedlingType');
        
        $data = [
            'title' => 'Cari Stok Bibit',
            'provinces' => $provinceModel->getAllOrdered(),
            'seedlingTypes' => $seedlingTypeModel->getAllActive()
        ];
        
        $this->render('public/stock-search', $data, null);
    }
    
    /**
     * AJAX: Search stock (public, no auth required)
     */
    public function searchStockAjax() {
        $stockModel = $this->model('Stock');
        
        $filters = [];
        
        if ($this->get('province_id')) {
            $filters['province_id'] = $this->get('province_id');
        }
        
        if ($this->get('seedling_type_id')) {
            $filters['seedling_type_id'] = $this->get('seedling_type_id');
        }
        
        $stocks = $stockModel->searchStock($filters);
        
        $this->json([
            'success' => true,
            'data' => $stocks
        ]);
    }
}
