<?php
/**
 * SeedSource Controller
 * Handles Direktori Sumber Benih Nasional operations for Admin/BPDAS
 */

require_once CORE_PATH . 'Controller.php';
require_once MODELS_PATH . 'SeedSource.php';
require_once MODELS_PATH . 'Province.php';
require_once MODELS_PATH . 'SeedlingType.php';


class SeedSourceController extends Controller {
    
    public function __construct() {
        parent::__construct();
        // Note: Auth check will be done in each method individually
    }
    
    /**
     * Check if user has admin or BPDAS role
     */
    private function checkAccess() {
        if (!isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('auth/login');
            exit;
        }
        
        $user = currentUser();
        if (!in_array($user['role'], ['admin', 'bpdas'])) {
            $this->redirect('auth/unauthorized');
            exit;
        }
    }
    
    /**
     * List all seed sources (Admin/BPDAS)
     */
    public function index() {
        $this->checkAccess();
        
        $seedSourceModel = new SeedSource();
        $provinceModel = new Province();
        $seedlingTypeModel = new SeedlingType();
        
        // Get all seed sources
        $seedSources = $seedSourceModel->getAll();
        $provinces = $provinceModel->getAllOrdered();
        $seedlingTypes = $seedlingTypeModel->getAllActive();
        
        $this->render('admin/seed-sources/index', [
            'title' => 'Direktori Sumber Benih Nasional',
            'seedSources' => $seedSources,
            'provinces' => $provinces,
            'seedlingTypes' => $seedlingTypes
        ], 'dashboard');
    }
    
    /**
     * Show create form
     */
    public function create() {
        $this->checkAccess();
        
        $provinceModel = new Province();
        $seedlingTypeModel = new SeedlingType();
        
        $this->render('admin/seed-sources/create', [
            'title' => 'Tambah Sumber Benih',
            'provinces' => $provinceModel->getAllOrdered(),
            'seedlingTypes' => $seedlingTypeModel->getAllActive()
        ], 'dashboard');
    }
    
    /**
     * Handle create submission
     */
    public function store() {
        $this->checkAccess();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/seed-sources');
            return;
        }
        
        // Validate required fields
        $errors = [];
        if (empty($_POST['seed_source_name'])) {
            $errors[] = 'Nama Sumber Benih wajib diisi';
        }
        if (empty($_POST['province_id'])) {
            $errors[] = 'Provinsi wajib dipilih';
        }
        
        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('admin/seed-sources/create');
            return;
        }
        
        // Prepare data
        $data = [
            'seed_source_name' => sanitize($_POST['seed_source_name']),
            'local_name' => sanitize($_POST['local_name'] ?? null),
            'botanical_name' => sanitize($_POST['botanical_name'] ?? null),
            'area_hectares' => !empty($_POST['area_hectares']) ? floatval($_POST['area_hectares']) : null,
            'seed_class' => sanitize($_POST['seed_class'] ?? null),
            'location' => sanitize($_POST['location'] ?? null),
            'latitude' => !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null,
            'longitude' => !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null,
            'owner_name' => sanitize($_POST['owner_name'] ?? null),
            'owner_phone' => sanitize($_POST['owner_phone'] ?? null),
            'ownership_type' => sanitize($_POST['ownership_type'] ?? null),
            'certificate_number' => sanitize($_POST['certificate_number'] ?? null),
            'certificate_date' => !empty($_POST['certificate_date']) ? $_POST['certificate_date'] : null,
            'certificate_validity' => !empty($_POST['certificate_validity']) ? $_POST['certificate_validity'] : null,
            'tree_count' => !empty($_POST['tree_count']) ? intval($_POST['tree_count']) : null,
            'flowering_season' => sanitize($_POST['flowering_season'] ?? null),
            'fruiting_season' => sanitize($_POST['fruiting_season'] ?? null),
            'production_estimate_per_year' => !empty($_POST['production_estimate_per_year']) ? floatval($_POST['production_estimate_per_year']) : null,
            'seed_quantity_estimate' => !empty($_POST['seed_quantity_estimate']) ? intval($_POST['seed_quantity_estimate']) : null,
            'utilization' => sanitize($_POST['utilization'] ?? null),
            'province_id' => intval($_POST['province_id']),
            'seedling_type_id' => !empty($_POST['seedling_type_id']) ? intval($_POST['seedling_type_id']) : null
        ];
        
        $seedSourceModel = new SeedSource();
        
        if ($seedSourceModel->create($data)) {
            $this->setFlash('success', 'Sumber benih berhasil ditambahkan');
            $this->redirect('admin/seed-sources');
        } else {
            $this->setFlash('error', 'Gagal menambahkan sumber benih');
            $this->redirect('admin/seed-sources/create');
        }
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $this->checkAccess();
        
        $seedSourceModel = new SeedSource();
        $provinceModel = new Province();
        $seedlingTypeModel = new SeedlingType();
        
        $seedSource = $seedSourceModel->getById($id);
        
        if (!$seedSource) {
            $this->setFlash('error', 'Sumber benih tidak ditemukan');
            $this->redirect('admin/seed-sources');
            return;
        }
        
        $this->render('admin/seed-sources/edit', [
            'title' => 'Edit Sumber Benih',
            'seedSource' => $seedSource,
            'provinces' => $provinceModel->getAllOrdered(),
            'seedlingTypes' => $seedlingTypeModel->getAllActive()
        ], 'dashboard');
    }
    
    /**
     * Handle update submission
     */
    public function update($id) {
        $this->checkAccess();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/seed-sources');
            return;
        }
        
        // Validate required fields
        $errors = [];
        if (empty($_POST['seed_source_name'])) {
            $errors[] = 'Nama Sumber Benih wajib diisi';
        }
        if (empty($_POST['province_id'])) {
            $errors[] = 'Provinsi wajib dipilih';
        }
        
        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('admin/seed-sources/edit/' . $id);
            return;
        }
        
        // Prepare data
        $data = [
            'seed_source_name' => sanitize($_POST['seed_source_name']),
            'local_name' => sanitize($_POST['local_name'] ?? null),
            'botanical_name' => sanitize($_POST['botanical_name'] ?? null),
            'area_hectares' => !empty($_POST['area_hectares']) ? floatval($_POST['area_hectares']) : null,
            'seed_class' => sanitize($_POST['seed_class'] ?? null),
            'location' => sanitize($_POST['location'] ?? null),
            'latitude' => !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null,
            'longitude' => !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null,
            'owner_name' => sanitize($_POST['owner_name'] ?? null),
            'owner_phone' => sanitize($_POST['owner_phone'] ?? null),
            'ownership_type' => sanitize($_POST['ownership_type'] ?? null),
            'certificate_number' => sanitize($_POST['certificate_number'] ?? null),
            'certificate_date' => !empty($_POST['certificate_date']) ? $_POST['certificate_date'] : null,
            'certificate_validity' => !empty($_POST['certificate_validity']) ? $_POST['certificate_validity'] : null,
            'tree_count' => !empty($_POST['tree_count']) ? intval($_POST['tree_count']) : null,
            'flowering_season' => sanitize($_POST['flowering_season'] ?? null),
            'fruiting_season' => sanitize($_POST['fruiting_season'] ?? null),
            'production_estimate_per_year' => !empty($_POST['production_estimate_per_year']) ? floatval($_POST['production_estimate_per_year']) : null,
            'seed_quantity_estimate' => !empty($_POST['seed_quantity_estimate']) ? intval($_POST['seed_quantity_estimate']) : null,
            'utilization' => sanitize($_POST['utilization'] ?? null),
            'province_id' => intval($_POST['province_id']),
            'seedling_type_id' => !empty($_POST['seedling_type_id']) ? intval($_POST['seedling_type_id']) : null
        ];
        
        $seedSourceModel = new SeedSource();
        
        if ($seedSourceModel->update($id, $data)) {
            $this->setFlash('success', 'Sumber benih berhasil diperbarui');
            $this->redirect('admin/seed-sources');
        } else {
            $this->setFlash('error', 'Gagal memperbarui sumber benih');
            $this->redirect('admin/seed-sources/edit/' . $id);
        }
    }
    
    /**
     * Delete seed source
     */
    public function delete($id) {
        $this->checkAccess();
        
        $seedSourceModel = new SeedSource();
        
        if ($seedSourceModel->delete($id)) {
            $this->setFlash('success', 'Sumber benih berhasil dihapus');
        } else {
            $this->setFlash('error', 'Gagal menghapus sumber benih');
        }
        
        $this->redirect('admin/seed-sources');
    }
    
    /**
     * View detail (admin view)
     */
    public function detail($id) {
        $this->checkAccess();
        
        $seedSourceModel = new SeedSource();
        $seedSource = $seedSourceModel->getById($id);
        
        if (!$seedSource) {
            $this->setFlash('error', 'Sumber benih tidak ditemukan');
            $this->redirect('admin/seed-sources');
            return;
        }
        
        $this->render('admin/seed-sources/view', [
            'title' => 'Detail Sumber Benih',
            'seedSource' => $seedSource
        ], 'dashboard');
    }
}


