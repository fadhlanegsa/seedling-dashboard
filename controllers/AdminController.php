<?php
/**
 * Admin Controller
 * Handles admin dashboard and operations
 */

require_once CORE_PATH . 'Controller.php';

class AdminController extends Controller {
    
    public function __construct() {
        parent::__construct();
        // Require admin authentication for all methods
        $this->requireAuth('admin');
    }
    
    /**
     * Admin Dashboard with Analytics
     */
    public function dashboard() {
        $bpdasModel = $this->model('BPDAS');
        $seedlingTypeModel = $this->model('SeedlingType');
        $stockModel = $this->model('Stock');
        $requestModel = $this->model('Request');
        
        // Get statistics
        $stats = [
            'total_bpdas' => $bpdasModel->getActiveCount(),
            'total_seedling_types' => $seedlingTypeModel->getActiveCount(),
            'total_national_stock' => $stockModel->getTotalNationalStock(),
            'pending_requests' => $requestModel->getPendingCount()
        ];
        
        // Get data for charts
        $stockByProvince = $bpdasModel->getStockByProvince();
        $topSeedlings = $stockModel->getTopSeedlingTypes(10);
        
        $data = [
            'title' => 'Dashboard Admin',
            'stats' => $stats,
            'stockByProvince' => $stockByProvince,
            'topSeedlings' => $topSeedlings,
            'distributionStats' => $requestModel->getMonthlyDistributionStats(date('Y'))
        ];
        
        $this->render('admin/dashboard', $data, 'dashboard');
    }
    
    /**
     * Manage BPDAS page
     */
    public function bpdas() {
        $page = $this->get('page', 1);
        
        $bpdasModel = $this->model('BPDAS');
        $result = $bpdasModel->paginate($page);
        
        $data = [
            'title' => 'Kelola BPDAS',
            'bpdas' => $result['data'],
            'pagination' => $result
        ];
        
        $this->render('admin/bpdas', $data, 'dashboard');
    }
    
    /**
     * Add/Edit BPDAS form
     */
    public function bpdasForm($id = null) {
        $bpdasModel = $this->model('BPDAS');
        $provinceModel = $this->model('Province');
        
        $bpdas = null;
        if ($id) {
            $bpdas = $bpdasModel->find($id);
            if (!$bpdas) {
                $this->setFlash('error', 'BPDAS tidak ditemukan');
                $this->redirect('admin/bpdas');
                return;
            }
        }
        
        $provinces = $provinceModel->getAllOrdered();
        
        $data = [
            'title' => $id ? 'Edit BPDAS' : 'Tambah BPDAS',
            'bpdas' => $bpdas,
            'provinces' => $provinces
        ];
        
        $this->render('admin/bpdas-form', $data, 'dashboard');
    }
    
    /**
     * Save BPDAS (create or update)
     */
    public function saveBPDAS() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/bpdas');
            return;
        }
        
        if (!$this->validateCSRF()) {
            return;
        }
        
        $id = $this->post('id');
        $data = [
            'name' => sanitize($this->post('name')),
            'province_id' => $this->post('province_id'),
            'address' => sanitize($this->post('address')),
            'phone' => sanitize($this->post('phone')),
            'email' => sanitize($this->post('email')),
            'contact_person' => sanitize($this->post('contact_person')),
            'latitude' => $this->post('latitude'),
            'longitude' => $this->post('longitude')
        ];
        
        // Validate required fields
        $errors = $this->validateRequired($data, ['name', 'province_id', 'address']);
        
        if (!empty($errors)) {
            $this->setFlash('error', 'Field yang wajib harus diisi');
            $this->redirect('admin/bpdas-form' . ($id ? "/$id" : ''));
            return;
        }
        
        $bpdasModel = $this->model('BPDAS');
        
        if ($id) {
            // Update
            $result = $bpdasModel->update($id, $data);
            $message = 'BPDAS berhasil diupdate';
        } else {
            // Create
            $result = $bpdasModel->create($data);
            $message = 'BPDAS berhasil ditambahkan';
        }
        
        if ($result) {
            $this->setFlash('success', $message);
        } else {
            $this->setFlash('error', 'Gagal menyimpan BPDAS');
        }
        
        $this->redirect('admin/bpdas');
    }
    
    /**
     * Create BPDAS account
     */
    public function createBPDASAccount($bpdasId) {
        $bpdasModel = $this->model('BPDAS');
        $bpdas = $bpdasModel->find($bpdasId);
        
        if (!$bpdas) {
            $this->json(['success' => false, 'message' => 'BPDAS tidak ditemukan']);
            return;
        }
        
        // Generate username and password
        $username = 'bpdas_' . strtolower(str_replace(' ', '_', $bpdas['name']));
        $password = generateRandomString(10);
        
        $userModel = $this->model('User');
        
        // Check if username exists
        if ($userModel->usernameExists($username)) {
            $this->json(['success' => false, 'message' => 'Username sudah ada']);
            return;
        }
        
        $userData = [
            'username' => $username,
            'email' => $bpdas['email'] ?? $username . '@bpdas.id',
            'password' => $password,
            'full_name' => $bpdas['contact_person'] ?? 'Admin ' . $bpdas['name'],
            'phone' => $bpdas['phone'],
            'role' => 'bpdas',
            'bpdas_id' => $bpdasId
        ];
        
        $userId = $userModel->register($userData);
        
        if ($userId) {
            $this->json([
                'success' => true,
                'message' => 'Akun BPDAS berhasil dibuat',
                'username' => $username,
                'password' => $password
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal membuat akun']);
        }
    }
    
    /**
     * Delete BPDAS
     */
    public function deleteBPDAS($id) {
        $bpdasModel = $this->model('BPDAS');
        
        if ($bpdasModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'BPDAS berhasil dihapus']);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal menghapus BPDAS']);
        }
    }
    
    /**
     * Manage seedling types page
     */
    public function seedlingTypes() {
        $page = $this->get('page', 1);
        $category = $this->get('category');
        
        $seedlingTypeModel = $this->model('SeedlingType');
        $result = $seedlingTypeModel->paginate($page, ITEMS_PER_PAGE, $category);
        
        $categories = $seedlingTypeModel->getCategoriesWithCounts();
        
        $data = [
            'title' => 'Kelola Jenis Bibit',
            'seedlingTypes' => $result['data'],
            'pagination' => $result,
            'categories' => $categories,
            'currentCategory' => $category
        ];
        
        $this->render('admin/seedling-types', $data, 'dashboard');
    }
    
    /**
     * Add/Edit seedling type form
     */
    public function seedlingForm($id = null) {
        $seedlingTypeModel = $this->model('SeedlingType');
        
        $seedlingType = null;
        if ($id) {
            $seedlingType = $seedlingTypeModel->find($id);
            if (!$seedlingType) {
                $this->setFlash('error', 'Jenis bibit tidak ditemukan');
                $this->redirect('admin/seedling-types');
                return;
            }
        }
        
        $data = [
            'title' => $id ? 'Edit Jenis Bibit' : 'Tambah Jenis Bibit',
            'seedlingType' => $seedlingType,
            'categories' => SEEDLING_CATEGORIES
        ];
        
        $this->render('admin/seedling-form', $data, 'dashboard');
    }
    
    /**
     * Save seedling type
     */
    public function saveSeedlingType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/seedling-types');
            return;
        }
        
        if (!$this->validateCSRF()) {
            return;
        }
        
        $id = $this->post('id');
        $data = [
            'name' => sanitize($this->post('name')),
            'scientific_name' => sanitize($this->post('scientific_name')),
            'category' => $this->post('category'),
            'description' => sanitize($this->post('description'))
        ];
        
        if (empty($data['name'])) {
            $this->setFlash('error', 'Nama bibit harus diisi');
            $this->redirect('admin/seedling-form' . ($id ? "/$id" : ''));
            return;
        }
        
        $seedlingTypeModel = $this->model('SeedlingType');
        
        // Check if name exists
        if ($seedlingTypeModel->nameExists($data['name'], $id)) {
            $this->setFlash('error', 'Nama bibit sudah ada');
            $this->redirect('admin/seedling-form' . ($id ? "/$id" : ''));
            return;
        }
        
        if ($id) {
            $result = $seedlingTypeModel->update($id, $data);
            $message = 'Jenis bibit berhasil diupdate';
        } else {
            $result = $seedlingTypeModel->create($data);
            $message = 'Jenis bibit berhasil ditambahkan';
        }
        
        if ($result) {
            $this->setFlash('success', $message);
        } else {
            $this->setFlash('error', 'Gagal menyimpan jenis bibit');
        }
        
        $this->redirect('admin/seedling-types');
    }
    
    /**
     * Delete seedling type
     */
    public function deleteSeedlingType($id) {
        $seedlingTypeModel = $this->model('SeedlingType');
        
        if ($seedlingTypeModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'Jenis bibit berhasil dihapus']);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal menghapus jenis bibit']);
        }
    }
    
    /**
     * Manage all stock page
     */
    public function stock() {
        $page = $this->get('page', 1);
        $filters = [
            'province_id' => $this->get('province_id'),
            'bpdas_id' => $this->get('bpdas_id'),
            'seedling_type_id' => $this->get('seedling_type_id'),
            'category' => $this->get('category'),
            'month' => $this->get('month'),
            'year' => $this->get('year')
        ];
        
        $stockModel = $this->model('Stock');
        $provinceModel = $this->model('Province');
        $bpdasModel = $this->model('BPDAS');
        $seedlingTypeModel = $this->model('SeedlingType');
        
        // Get paginated stock data
        $result = $stockModel->searchStockPaginated($page, ITEMS_PER_PAGE, $filters);
        $provinces = $provinceModel->getAllOrdered();
        $bpdasList = $bpdasModel->getAllWithProvince();
        $seedlingTypes = $seedlingTypeModel->getAllActive();
        
        $data = [
            'title' => 'Kelola Stok Nasional',
            'stock' => $result['data'],
            'pagination' => $result,
            'filters' => $filters,
            'provinces' => $provinces,
            'bpdasList' => $bpdasList,
            'seedlingTypes' => $seedlingTypes
        ];
        
        $this->render('admin/stock', $data, 'dashboard');
    }
    
    /**
     * Manage all requests page
     */
    public function requests() {
        $page = $this->get('page', 1);
        $filters = [
            'status' => $this->get('status'),
            'province_id' => $this->get('province_id'),
            'bpdas_id' => $this->get('bpdas_id')
        ];
        
        $requestModel = $this->model('Request');
        $provinceModel = $this->model('Province');
        $bpdasModel = $this->model('BPDAS');
        
        $result = $requestModel->paginate($page, ITEMS_PER_PAGE, $filters);
        $provinces = $provinceModel->getAllOrdered();
        $bpdasList = $bpdasModel->getAllWithProvince();
        
        $data = [
            'title' => 'Kelola Permintaan',
            'requests' => $result['data'],
            'pagination' => $result,
            'filters' => $filters,
            'provinces' => $provinces,
            'bpdasList' => $bpdasList
        ];
        
        $this->render('admin/requests', $data, 'dashboard');
    }
    
    /**
     * View request detail
     */
    public function requestDetail($id) {
        $requestModel = $this->model('Request');
        
        $request = $requestModel->getDetailById($id);
        
        if (!$request) {
            $this->setFlash('error', 'Permintaan tidak ditemukan');
            $this->redirect('admin/requests');
            return;
        }
        
        // Get request history
        $history = $requestModel->getHistory($id);
        
        $data = [
            'title' => 'Detail Permintaan',
            'request' => $request,
            'history' => $history
        ];
        
        $this->render('admin/request-detail', $data, 'dashboard');
    }
    
    /**
     * Manage users page
     */
    public function users() {
        $page = $this->get('page', 1);
        $role = $this->get('role');
        
        $userModel = $this->model('User');
        $result = $userModel->paginate($page, ITEMS_PER_PAGE, $role);
        
        $data = [
            'title' => 'Kelola Pengguna',
            'users' => $result['data'],
            'pagination' => $result,
            'currentRole' => $role
        ];
        
        $this->render('admin/users', $data, 'dashboard');
    }
    
    /**
     * Add/Edit user form
     */
    public function userForm($id = null) {
        $userModel = $this->model('User');
        $bpdasModel = $this->model('BPDAS');
        
        $user = null;
        if ($id) {
            $user = $userModel->find($id);
            if (!$user) {
                $this->setFlash('error', 'Pengguna tidak ditemukan');
                $this->redirect('admin/users');
                return;
            }
        }
        
        $bpdasList = $bpdasModel->getAllWithProvince();
    
    // Load nurseries for operator role
    $nurseryModel = $this->model('Nursery');
    $nurseries = $nurseryModel->query("SELECT n.*, b.name as bpdas_name 
                                       FROM nurseries n 
                                       JOIN bpdas b ON n.bpdas_id = b.id 
                                       WHERE n.is_active = 1 
                                       ORDER BY b.name ASC, n.name ASC");
    
    $data = [
        'title' => $id ? 'Edit Pengguna' : 'Tambah Pengguna',
        'user' => $user,
        'bpdasList' => $bpdasList,
        'nurseries' => $nurseries
    ];
        
        $this->render('admin/user-form', $data, 'dashboard');
    }
    
    /**
     * Save user
     */
    public function saveUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/users');
            return;
        }
        
        if (!$this->validateCSRF()) {
            return;
        }
        
        $id = $this->post('id');
        $data = [
            'username' => sanitize($this->post('username')),
            'email' => sanitize($this->post('email')),
            'full_name' => sanitize($this->post('full_name')),
            'phone' => sanitize($this->post('phone')),
            'role' => $this->post('role'),
        'bpdas_id' => $this->post('bpdas_id') ?: null,
        'nursery_id' => $this->post('nursery_id') ?: null,
        'is_active' => $this->post('is_active', 1)
        ];
        
        $userModel = $this->model('User');
        
        // Validate
        if (empty($data['username']) || empty($data['email']) || empty($data['full_name'])) {
            $this->setFlash('error', 'Field yang wajib harus diisi');
            $this->redirect('admin/user-form' . ($id ? "/$id" : ''));
            return;
        }
        
        if (!$this->validateEmail($data['email'])) {
            $this->setFlash('error', 'Format email tidak valid');
            $this->redirect('admin/user-form' . ($id ? "/$id" : ''));
            return;
        }
        
        // Check username
        if ($userModel->usernameExists($data['username'], $id)) {
            $this->setFlash('error', 'Username sudah digunakan');
            $this->redirect('admin/user-form' . ($id ? "/$id" : ''));
            return;
        }
        
        // Check email
        if ($userModel->emailExists($data['email'], $id)) {
            $this->setFlash('error', 'Email sudah terdaftar');
            $this->redirect('admin/user-form' . ($id ? "/$id" : ''));
            return;
        }
        
        if ($id) {
            // Update
            $password = $this->post('password');
            if (!empty($password)) {
                $userModel->changePassword($id, $password);
            }
            
            $result = $userModel->update($id, $data);
            $message = 'Pengguna berhasil diupdate';
        } else {
            // Create
            $password = $this->post('password');
            if (empty($password)) {
                $this->setFlash('error', 'Password harus diisi');
                $this->redirect('admin/user-form');
                return;
            }
            
            $data['password'] = $password;
            $result = $userModel->register($data);
            $message = 'Pengguna berhasil ditambahkan';
        }
        
        if ($result) {
            $this->setFlash('success', $message);
        } else {
            $this->setFlash('error', 'Gagal menyimpan pengguna');
        }
        
        $this->redirect('admin/users');
    }
    
    /**
     * Delete user
     */
    public function deleteUser($id) {
        $user = currentUser();
        
        // Prevent deleting own account
        if ($id == $user['id']) {
            $this->json(['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri']);
            return;
        }
        
        $userModel = $this->model('User');
        
        if ($userModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'Pengguna berhasil dihapus']);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal menghapus pengguna']);
        }
    }
    
    /**
     * Map distribution visualization page
     */
    public function mapDistribution() {
        $provinceModel = $this->model('Province');
        $bpdasModel = $this->model('BPDAS');
        $seedlingTypeModel = $this->model('SeedlingType');
        
        $provinces = $provinceModel->getAllOrdered();
        $bpdas_list = $bpdasModel->getAllWithProvince();
        $seedling_types = $seedlingTypeModel->getAllActive();
        
        $data = [
            'title' => 'Peta Distribusi Bibit',
            'provinces' => $provinces,
            'bpdas_list' => $bpdas_list,
            'seedling_types' => $seedling_types
        ];
        
        $this->render('admin/map-distribution', $data, 'dashboard');
    }
    
    /**
     * AJAX: Get map data for visualization
     */
    public function getMapData() {
        $filters = [
            'province_id' => $this->get('province_id'),
            'bpdas_id' => $this->get('bpdas_id'),
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
     * Manage Nurseries
     */
    public function nurseries() {
        $user = currentUser();
        $userModel = $this->model('User');
        $nurseryModel = $this->model('Nursery');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        // Basic pagination or list all? Let's list all for now as there aren't too many
        // For better structure, we might want to paginate if many
        
        // Let's get all BPDAS to group nurseries or filter
        $bpdasModel = $this->model('BPDAS');
        $bpdasList = $bpdasModel->all(['is_active' => 1], 'name ASC');
        
        // If filter applied
        $bpdasId = isset($_GET['bpdas_id']) ? (int)$_GET['bpdas_id'] : null;
        
        $nurseries = [];
        if ($bpdasId) {
            $nurseries = $nurseryModel->getByBPDAS($bpdasId);
        } else {
            // Get all with BPDAS info manually or add a method in model
            // For now, let's just get all and enrich
             $nurseries = $nurseryModel->query("SELECT n.*, b.name as bpdas_name 
                                              FROM nurseries n 
                                              JOIN bpdas b ON n.bpdas_id = b.id 
                                              WHERE n.is_active = 1 
                                              ORDER BY b.name ASC, n.name ASC");
        }
        
        $data = [
            'title' => 'Kelola Persemaian',
            'nurseries' => $nurseries,
            'bpdas_list' => $bpdasList,
            'selected_bpdas' => $bpdasId
        ];
        
        $this->render('admin/nurseries/index', $data, 'dashboard');
    }

    /**
     * Create Nursery Form
     */
    public function createNursery() {
        $bpdasModel = $this->model('BPDAS');
        $bpdasList = $bpdasModel->all(['is_active' => 1], 'name ASC');
        
        $data = [
            'title' => 'Tambah Persemaian',
            'bpdas_list' => $bpdasList
        ];
        
        $this->render('admin/nurseries/form', $data, 'dashboard');
    }
    
    /**
     * Store Nursery
     */
    public function storeNursery() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/nurseries');
            return;
        }
        
        if (!$this->validateCSRF()) {
            return;
        }
        
        $name = sanitize($this->post('name'));
        $bpdasId = (int)$this->post('bpdas_id');
        $address = sanitize($this->post('address'));
        
        if (empty($name) || empty($bpdasId)) {
            $this->setFlash('error', 'Nama dan BPDAS harus diisi');
            $this->redirect('admin/nurseries/create');
            return;
        }
        
        try {
            $nurseryModel = $this->model('Nursery');
            $result = $nurseryModel->create([
                'name' => $name,
                'bpdas_id' => $bpdasId,
                'address' => $address,
                'is_active' => 1
            ]);

            if ($result) {
                $this->setFlash('success', 'Persemaian berhasil ditambahkan');
            } else {
                $this->setFlash('error', 'Gagal menambahkan persemaian (Database Error)');
            }
        } catch (Exception $e) {
            $this->setFlash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        
        $this->redirect('admin/nurseries');
    }
    
    /**
     * Edit Nursery Form
     */
    public function editNursery($id) {
        $nurseryModel = $this->model('Nursery');
        $nursery = $nurseryModel->find($id);
        
        if (!$nursery) {
            $this->setFlash('error', 'Persemaian tidak ditemukan');
            $this->redirect('admin/nurseries');
            return;
        }
        
        $bpdasModel = $this->model('BPDAS');
        $bpdasList = $bpdasModel->all(['is_active' => 1], 'name ASC');
        
        $data = [
            'title' => 'Edit Persemaian',
            'nursery' => $nursery,
            'bpdas_list' => $bpdasList
        ];
        
        $this->render('admin/nurseries/form', $data, 'dashboard');
    }
    
    /**
     * Update Nursery
     */
    public function updateNursery() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/nurseries');
            return;
        }
        
        if (!$this->validateCSRF()) {
            return;
        }
        
        $id = (int)$this->post('id');
        $name = sanitize($this->post('name'));
        $bpdasId = (int)$this->post('bpdas_id');
        $address = sanitize($this->post('address'));
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        try {
            $nurseryModel = $this->model('Nursery');
            $result = $nurseryModel->update($id, [
                'name' => $name,
                'bpdas_id' => $bpdasId,
                'address' => $address,
                'is_active' => $isActive
            ]);

            if ($result) {
                $this->setFlash('success', 'Data persemaian berhasil diperbarui');
            } else {
                $this->setFlash('error', 'Gagal memperbarui data persemaian');
            }
        } catch (Exception $e) {
            $this->setFlash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        
        $this->redirect('admin/nurseries');
    }
    
    /**
     * Delete Nursery
     */
    public function deleteNursery($id) {
        // Ideally we should soft delete or check for dependencies (stock/users)
        // For now, let's check dependencies
        $userModel = $this->model('User');
        $users = $userModel->all(['nursery_id' => $id]);
        
        $stockModel = $this->model('Stock');
        $stocks = $stockModel->all(['nursery_id' => $id]);
        
        if (!empty($users) || !empty($stocks)) {
            $this->setFlash('error', 'Tidak dapat menghapus persemaian yang memiliki data User atau Stok. Nonaktifkan saja jika perlu.');
            $this->redirect('admin/nurseries');
            return;
        }
        
        $nurseryModel = $this->model('Nursery');
        $nurseryModel->delete($id);
        
        $this->setFlash('success', 'Persemaian berhasil dihapus');
        $this->redirect('admin/nurseries');
    }
}
