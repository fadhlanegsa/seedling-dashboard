<?php
/**
 * Seedling Admin Controller
 * Handles Penatausahaan Bibit module
 */

require_once CORE_PATH . 'Controller.php';

class SeedlingAdminController extends Controller {

    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
        // Require specific roles
        if (!$this->requireAuth()) {
            return;
        }

        $user = currentUser();
        $allowedRoles = ['admin', 'bpdas', 'operator_persemaian'];
        if (!in_array($user['role'], $allowedRoles)) {
            $this->redirect('auth/unauthorized');
            return;
        }
    }

    /**
     * Resolves BPDAS and Nursery IDs to prevent NULL values from crashing the system.
     */
    private function resolveLocationIds($postBpdas, $postNursery, $sourceBpdas = null, $sourceNursery = null) {
        $user = currentUser();
        
        $nurseryId = ($user['role'] === 'admin' && !empty($postNursery)) ? $postNursery : ($sourceNursery ?? $user['nursery_id']);
        $bpdasId = ($user['role'] === 'admin' && !empty($postBpdas)) ? $postBpdas : ($sourceBpdas ?? $user['bpdas_id']);

        if (empty($bpdasId) && !empty($nurseryId)) {
            $stmt = $this->db->prepare("SELECT bpdas_id FROM nurseries WHERE id = ?");
            $stmt->execute([$nurseryId]);
            $nursery = $stmt->fetch();
            if ($nursery && !empty($nursery['bpdas_id'])) {
                $bpdasId = $nursery['bpdas_id'];
            }
        }
        
        if (empty($bpdasId)) {
            $bpdasData = $this->db->query("SELECT id FROM bpdas LIMIT 1")->fetch();
            if ($bpdasData) {
                $bpdasId = $bpdasData['id'];
            }
        }
        
        return [
            'nursery_id' => $nurseryId ?: null,
            'bpdas_id' => $bpdasId ?: null
        ];
    }

    /**
     * Module Landing Page
     */
    public function index() {
        $bahanBakuModel = $this->model('BahanBaku');
        $mixingModel = $this->model('MediaMixing');
        $fillingModel = $this->model('BagFilling');
        $sowingModel = $this->model('SeedSowing');
        $harvestModel = $this->model('SeedlingHarvest');
        $user = currentUser();
        
        // Filters from GET
        $filters = [
            'bpdas_id' => $this->get('filter_bpdas'),
            'nursery_id' => $this->get('filter_nursery')
        ];

        // Role-based Lock/Filter
        if ($user['role'] === 'operator_persemaian') {
            $filters['nursery_id'] = $user['nursery_id'];
            $filters['bpdas_id'] = $user['bpdas_id'];
        } elseif ($user['role'] === 'bpdas') {
            $filters['bpdas_id'] = $user['bpdas_id'];
        }
        
        $recentTransactions = $bahanBakuModel->getRecentTransactions(10, $filters);
        $recentProductions = $mixingModel->getRecentProductions(10, $filters);
        $recentFillings = $fillingModel->getRecentFillings(10, $filters);
        $recentSowings = $sowingModel->getRecentSowings(10, $filters);
        $recentHarvests = $harvestModel->getRecentHarvests(10, $filters);

        $weaningModel = $this->model('SeedlingWeaning');
        $recentWeanings = $weaningModel->getRecentWeanings(10, $filters);

        $entresModel = $this->model('SeedlingEntres');
        $recentEntres = $entresModel->getRecentEntres(10, $filters);

        $stockBalance = $bahanBakuModel->getStockBalance($filters);

        $mutationModel = $this->model('SeedlingMutation');
        $recentMutations = $mutationModel->getRecentMutations(10, $filters);

        $stockModel = $this->model('Stock');
        if (!empty($filters['nursery_id'])) {
            $readyStock = $stockModel->getByNursery($filters['nursery_id']);
        } elseif (!empty($filters['bpdas_id'])) {
            $readyStock = $stockModel->getByBPDAS($filters['bpdas_id']);
        } else {
            $readyStock = $stockModel->searchStock($filters);
        }

        // Fetch BPDAS and Nurseries for the filter dropdowns (Admin/BPDAS only)
        $bpdasList = [];
        $nurseryList = [];
        if ($user['role'] !== 'operator_persemaian') {
            $bpdasList = $bahanBakuModel->query("SELECT id, name FROM bpdas ORDER BY name ASC");
            
            $nurserySql = "SELECT id, name, bpdas_id FROM nurseries WHERE 1=1";
            $nurseryParams = [];
            if ($user['role'] === 'bpdas') {
                $nurserySql .= " AND bpdas_id = ?";
                $nurseryParams[] = $user['bpdas_id'];
            } elseif (!empty($filters['bpdas_id'])) {
                $nurserySql .= " AND bpdas_id = ?";
                $nurseryParams[] = $filters['bpdas_id'];
            }
            $nurserySql .= " ORDER BY name ASC";
            $nurseryList = $bahanBakuModel->query($nurserySql, $nurseryParams);
        }

        $data = [
            'title' => 'Penatausahaan Bibit',
            'recentTransactions' => $recentTransactions,
            'recentProductions' => $recentProductions,
            'recentFillings' => $recentFillings,
            'recentSowings' => $recentSowings,
            'recentHarvests' => $recentHarvests,
            'recentWeanings' => $recentWeanings,
            'recentEntres' => $recentEntres,
            'recentMutations' => $recentMutations,
            'readyStock' => $readyStock,
            'stockBalance' => $stockBalance,
            'bpdasList' => $bpdasList,
            'nurseryList' => $nurseryList,
            'filters' => $filters,
            'user' => $user
        ];

        $this->render('seedling_admin/index', $data, 'dashboard');
    }

    /**
     * AJAX: Get Nurseries by BPDAS
     */
    public function getNurseriesByBPDAS() {
        $bpdasId = $this->get('bpdas_id');
        if (!$bpdasId) {
            $this->json(['success' => false, 'message' => 'BPDAS ID required'], 400);
            return;
        }

        $bahanBakuModel = $this->model('BahanBaku');
        $sql = "SELECT id, name FROM nurseries WHERE bpdas_id = ? ORDER BY name ASC";
        $nurseries = $bahanBakuModel->query($sql, [$bpdasId]);

        $this->json([
            'success' => true,
            'data' => $nurseries
        ]);
    }

    /**
     * AJAX: Get All Nurseries (For Admin Form Override)
     */
    public function getAllNurseriesAjax() {
        if (currentUser()['role'] !== 'admin') {
            $this->json(['success' => false], 403);
            return;
        }
        $sql = "SELECT id, name, bpdas_id FROM nurseries ORDER BY name ASC";
        $nurseries = $this->db->query($sql)->fetchAll();
        $this->json(['success' => true, 'data' => $nurseries]);
    }

    /**
     * Bahan Baku IN Form
     */
    public function bahanBakuForm() {
        $bahanBakuModel = $this->model('BahanBaku');
        $user = currentUser();
        
        $categories = $bahanBakuModel->getCategories();
        $transactionId = $bahanBakuModel->generateTransactionID();

        // Build filter based on role
        $filters = [];
        if ($user['role'] === 'operator_persemaian') {
            $filters['nursery_id'] = $user['nursery_id'];
        } elseif ($user['role'] === 'bpdas') {
            $filters['bpdas_id'] = $user['bpdas_id'];
        }
        $recentBahanBaku = $bahanBakuModel->getRecentTransactions(20, $filters);

        $data = [
            'title'           => 'Bahan Baku IN',
            'categories'      => $categories,
            'transactionId'   => $transactionId,
            'today'           => date('Y-m-d'),
            'recentBahanBaku' => $recentBahanBaku,
        ];

        $this->render('seedling_admin/bahan_baku_form', $data, 'dashboard');
    }

    /**
     * AJAX: Get items by category
     */
    public function getItemsByCategory() {
        $category = $this->get('category');
        if (!$category) {
            $this->json(['success' => false, 'message' => 'Category required'], 400);
            return;
        }

        $user = currentUser();
        $nurseryId = ($user['role'] === 'operator_persemaian') ? $user['nursery_id'] : null;

        $bahanBakuModel = $this->model('BahanBaku');
        $items = $bahanBakuModel->getItemsByCategory($category);
        $stocks = $bahanBakuModel->getStockBalance(['nursery_id' => $nurseryId]);

        // Merge stock into items
        foreach ($items as &$item) {
            $item['stock'] = 0;
            foreach ($stocks as $s) {
                if ($s['id'] == $item['id']) {
                    $item['stock'] = (float)$s['current_stock'];
                    break;
                }
            }
        }

        $this->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Master Data List Page
     */
    public function masterData() {
        $bahanBakuModel = $this->model('BahanBaku');
        $items = $bahanBakuModel->getAllMaster();

        $data = [
            'title' => 'Database Penatausahaan Bibit',
            'items' => $items
        ];

        $this->render('seedling_admin/master_data', $data, 'dashboard');
    }

    /**
     * Master Data Form (Add/Edit)
     */
    public function masterDataForm($id = null) {
        $bahanBakuModel = $this->model('BahanBaku');
        $item = $id ? $bahanBakuModel->getMasterItem($id) : null;

        $categories = $this->db->query("SELECT * FROM bahan_baku_categories ORDER BY code ASC")->fetchAll(PDO::FETCH_ASSOC);
        $seedlingTypes = $this->db->query("SELECT id, name, scientific_name FROM seedling_types WHERE is_active = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => ($id ? 'Edit' : 'Tambah') . ' Data Master',
            'item' => $item,
            'categories' => $categories,
            'seedlingTypes' => $seedlingTypes
        ];

        $this->render('seedling_admin/master_data_form', $data, 'dashboard');
    }

    /**
     * Save Master Data
     */
    public function saveMasterData() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('seedling-admin/master-data');
            return;
        }

        if (!$this->validateCSRF()) {
            return;
        }

        $id = $this->post('id');
        $bahanBakuModel = $this->model('BahanBaku');

        $data = [
            'category_code'   => $this->post('category_code'),
            'category'        => $this->post('category'),
            'seedling_type_id' => $this->post('seedling_type_id') ?: null,
            'code'           => strtoupper(sanitize($this->post('code'))),
            'name'           => sanitize($this->post('name')),
            'scientific_name' => sanitize($this->post('scientific_name')),
            'unit'           => $this->post('unit', 'kg'),
            'description'    => sanitize($this->post('description'))
        ];

        // Auto-generate code if empty to prevent UNIQUE constraint violation on DB
        if (empty($data['code']) && !$id) {
            $data['code'] = $bahanBakuModel->generateMasterCode($data['category_code']);
        }

        if (empty($data['category_code']) || empty($data['name'])) {
            $this->setFlash('error', 'Kategori dan Nama Item harus diisi');
            $this->redirect('seedling-admin/master-data-form' . ($id ? '/' . $id : ''));
            return;
        }

        try {
            $result = $bahanBakuModel->saveMaster($data, $id);

            if ($result) {
                $this->setFlash('success', 'Data Master berhasil disimpan');
                $this->redirect('seedling-admin/master-data');
            } else {
                $this->setFlash('error', 'Gagal menyimpan data master. Silakan periksa kelengkapan data.');
                $this->redirect('seedling-admin/master-data-form' . ($id ? '/' . $id : ''));
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $this->setFlash('error', 'Gagal: Kode barang "' . $data['code'] . '" sudah digunakan. Silakan gunakan kode lain.');
            } else {
                $this->setFlash('error', 'Database Error: ' . $e->getMessage());
            }
            $this->redirect('seedling-admin/master-data-form' . ($id ? '/' . $id : ''));
        } catch (Exception $e) {
            $this->setFlash('error', 'General Error: ' . $e->getMessage());
            $this->redirect('seedling-admin/master-data-form' . ($id ? '/' . $id : ''));
        }
    }

    /**
     * Delete Master Data
     */
    public function deleteMasterData($id) {
        // Must be a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('seedling-admin/master-data');
            return;
        }

        // CSRF check with graceful redirect on failure
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!verifyCSRFToken($token)) {
            $this->setFlash('error', 'Sesi habis. Silakan coba lagi.');
            $this->redirect('seedling-admin/master-data');
            return;
        }

        $bahanBakuModel = $this->model('BahanBaku');
        
        try {
            $result = $bahanBakuModel->deleteMaster($id);

            if ($result) {
                $this->setFlash('success', 'Item berhasil dihapus.');
            } else {
                $this->setFlash('error', 'Gagal menghapus. Item mungkin sudah terhapus atau tidak ditemukan.');
            }
        } catch (PDOException $e) {
            // FK constraint - item is used in transactions
            $this->setFlash('error', 'Item tidak bisa dihapus karena sudah digunakan dalam transaksi produksi.');
        } catch (Exception $e) {
            $this->setFlash('error', 'Terjadi kesalahan sistem saat menghapus data.');
        }
        
        $this->redirect('seedling-admin/master-data');
    }

    /**
     * Store Bahan Baku Transaction
     */
    public function storeBahanBaku() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('seedling-admin/bahan-baku-form');
            return;
        }

        if (!$this->validateCSRF()) {
            return;
        }

        $user = currentUser();
        $bahanBakuModel = $this->model('BahanBaku');

        $data = [
            'transaction_id'   => $this->post('transaction_id'),
            'transaction_date' => $this->post('transaction_date'),
            'item_id'          => (int)$this->post('item_id'),
            'quantity'         => (float)$this->post('quantity'),
            'notes'            => sanitize($this->post('notes')),
            'sender'           => sanitize($this->post('sender')),
            'receiver'         => sanitize($this->post('receiver')),
            'foreman'          => sanitize($this->post('foreman')),
            'manager'          => sanitize($this->post('manager')),
            'bpdas_id'         => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['bpdas_id'],
            'nursery_id'       => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['nursery_id'],
            'created_by'       => $user['id']
        ];

        // Basic validation
        if (empty($data['item_id']) || empty($data['quantity'])) {
            $this->setFlash('error', 'Item dan Jumlah harus diisi');
            $this->redirect('seedling-admin/bahan-baku-form');
            return;
        }

        $result = $bahanBakuModel->saveTransaction($data);

        if ($result) {
            $this->setFlash('success', 'Transaksi Bahan Baku berhasil disimpan');
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal menyimpan transaksi');
            $this->redirect('seedling-admin/bahan-baku-form');
        }
    }

    /**
     * Media Mixing Form
     */
    public function mediaMixingForm() {
        $mixingModel = $this->model('MediaMixing');
        $bahanBakuModel = $this->model('BahanBaku');

        $productionCode = $mixingModel->generateProductionID();
        $categories = $bahanBakuModel->getCategories();

        $data = [
            'title' => 'Pencampuran Media Tanam',
            'productionCode' => $productionCode,
            'categories' => $categories,
            'today' => date('Y-m-d')
        ];

        $this->render('seedling_admin/media_mixing_form', $data, 'dashboard');
    }

    /**
     * Store Media Mixing Production
     */
    public function storeMediaMixing() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('seedling-admin/media-mixing-form');
            return;
        }

        if (!$this->validateCSRF()) {
            return;
        }

        $user = currentUser();
        $mixingModel = $this->model('MediaMixing');

        $productionData = [
            'production_code' => $this->post('production_code'),
            'production_date' => $this->post('production_date'),
            'total_production' => (float)$this->post('total_production'),
            'picker_name'      => sanitize($this->post('picker_name')),
            'foreman'          => sanitize($this->post('foreman')),
            'manager'          => sanitize($this->post('manager')),
            'notes'            => sanitize($this->post('notes')),
            'bpdas_id'         => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['bpdas_id'],
            'nursery_id'       => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['nursery_id'],
            'created_by'       => $user['id']
        ];

        // Items from dynamic table
        $itemIds = $this->post('item_id'); // array
        $quantities = $this->post('item_quantity'); // array
        $items = [];

        if (is_array($itemIds)) {
            for ($i = 0; $i < count($itemIds); $i++) {
                if (!empty($itemIds[$i])) {
                    $items[] = [
                        'item_id' => (int)$itemIds[$i],
                        'quantity' => (float)$quantities[$i]
                    ];
                }
            }
        }

        if (empty($items)) {
            $this->setFlash('error', 'Minimal harus ada 1 bahan baku yang digunakan');
            $this->redirect('seedling-admin/media-mixing-form');
            return;
        }

        $result = $mixingModel->saveProduction($productionData, $items);

        if ($result) {
            $this->setFlash('success', 'Pencampuran Media berhasil disimpan');
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal menyimpan pencampuran media');
            $this->redirect('seedling-admin/media-mixing-form');
        }
    }

    /**
     * Bag Filling Form
     */
    public function bagFillingForm() {
        $fillingModel = $this->model('BagFilling');
        $bahanBakuModel = $this->model('BahanBaku');
        
        $user = currentUser();
        $nurseryId = ($user['role'] === 'operator_persemaian') ? $user['nursery_id'] : null;

        $fillingCode = $fillingModel->generateFillingID();
        // Specifically get bags (Category C)
        $bags = $bahanBakuModel->getItemsByCategory('KANTONG BIBIT');
        $stockBalance = $bahanBakuModel->getStockBalance(['nursery_id' => $nurseryId]);
        
        // Map stock balance to bag list for easy UI check
        foreach ($bags as &$bag) {
            $bag['stock'] = 0;
            foreach ($stockBalance as $sb) {
                if ($sb['id'] == $bag['id']) {
                    $bag['stock'] = $sb['current_stock'];
                    break;
                }
            }
        }

        $data = [
            'title' => 'Pengisian Kantong Bibit',
            'fillingCode' => $fillingCode,
            'bags' => $bags,
            'today' => date('Y-m-d')
        ];

        $this->render('seedling_admin/bag_filling_form', $data, 'dashboard');
    }

    /**
     * AJAX: Get Media Tanam Stock
     */
    public function getMediaStockAJAX() {
        $mixingModel = $this->model('MediaMixing');
        $user = currentUser();
        $nurseryId = ($user['role'] === 'operator_persemaian') ? $user['nursery_id'] : null;

        $stocks = $mixingModel->getAvailableMediaStock(['nursery_id' => $nurseryId]);

        $this->json([
            'success' => true,
            'data' => $stocks
        ]);
    }

    /**
     * Store Bag Filling
     */
    public function storeBagFilling() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('seedling-admin');
            return;
        }

        if (!$this->validateCSRF()) {
            return;
        }

        $user = currentUser();
        $fillingModel = $this->model('BagFilling');

        $fillingData = [
            'filling_code'     => $this->post('filling_code'),
            'filling_date'     => $this->post('filling_date'),
            'bag_item_id'      => (int)$this->post('bag_item_id'),
            'bag_quantity'     => (float)$this->post('bag_quantity'),
            'total_production' => (float)$this->post('total_production'),
            'mandor'           => sanitize($this->post('mandor')),
            'manager'          => sanitize($this->post('manager')),
            'notes'            => sanitize($this->post('notes')),
            'bpdas_id'         => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['bpdas_id'],
            'nursery_id'       => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['nursery_id'],
            'created_by'       => $user['id']
        ];

        // Media items
        $mediaIds = $this->post('media_production_id'); // array
        $quantities = $this->post('media_quantity'); // array
        $mediaItems = [];

        if (is_array($mediaIds)) {
            for ($i = 0; $i < count($mediaIds); $i++) {
                if (!empty($mediaIds[$i])) {
                    $mediaItems[] = [
                        'media_production_id' => (int)$mediaIds[$i],
                        'quantity' => (float)$quantities[$i]
                    ];
                }
            }
        }

        if (empty($fillingData['bag_item_id']) || empty($mediaItems)) {
            $this->setFlash('error', 'Kantong dan Media harus terisi');
            $this->redirect('seedling-admin/bag-filling-form');
            return;
        }

        $result = $fillingModel->saveBagFilling($fillingData, $mediaItems);

        if ($result) {
            $this->setFlash('success', 'Pencatatan Pengisian Kantong berhasil');
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal menyimpan data pengisian');
            $this->redirect('seedling-admin/bag-filling-form');
        }
    }

    /**
     * Penaburan Benih Form
     */
    public function seedSowingForm() {
        $sowingModel = $this->model('SeedSowing');
        $bahanBakuModel = $this->model('BahanBaku');
        
        $user = currentUser();
        $nurseryId = ($user['role'] === 'operator_persemaian') ? $user['nursery_id'] : null;

        $sowingCode = $sowingModel->generateSowingID();

        // Get main seeds (Category BENIH)
        $seeds = $bahanBakuModel->getItemsByCategory('BENIH');
        $stockBalance = $bahanBakuModel->getStockBalance(['nursery_id' => $nurseryId]);
        
        // Map stock balance to seeds
        foreach ($seeds as &$seed) {
            $seed['stock'] = 0;
            foreach ($stockBalance as $sb) {
                if ($sb['id'] == $seed['id']) {
                    $seed['stock'] = $sb['current_stock'];
                    break;
                }
            }
        }

        $data = [
            'title' => 'Penaburan Benih',
            'sowingCode' => $sowingCode,
            'seeds' => $seeds,
            'today' => date('Y-m-d')
        ];

        $this->render('seedling_admin/seed_sowing_form', $data, 'dashboard');
    }

    /**
     * AJAX: Get Available Filled Bags (PB-)
     */
    public function getFilledBagsStockAJAX() {
        $fillingModel = $this->model('BagFilling');
        $user = currentUser();
        $nurseryId = ($user['role'] === 'operator_persemaian') ? $user['nursery_id'] : null;

        $stocks = $fillingModel->getAvailableFilledBags(['nursery_id' => $nurseryId]);

        $this->json([
            'success' => true,
            'data' => $stocks
        ]);
    }

    /**
     * AJAX: Get Supporting Materials Stock (Everything NOT BENIH or KANTONG BIBIT)
     */
    public function getMaterialsStockAJAX() {
        $bahanBakuModel = $this->model('BahanBaku');
        $user = currentUser();
        $nurseryId = ($user['role'] === 'operator_persemaian') ? $user['nursery_id'] : null;

        $allStocks = $bahanBakuModel->getStockBalance(['nursery_id' => $nurseryId]);
        $materials = [];

        foreach ($allStocks as $stock) {
            $cat = strtoupper($stock['category']);
            if ($cat !== 'BENIH' && $cat !== 'KANTONG BIBIT') {
                $materials[] = $stock;
            }
        }

        $this->json([
            'success' => true,
            'data' => $materials
        ]);
    }

    /**
     * AJAX: Get Supporting Materials Stock Specifically for Entres
     */
    public function getEntresMaterialsAJAX() {
        $bahanBakuModel = $this->model('BahanBaku');
        $user = currentUser();
        $nurseryId = ($user['role'] === 'operator_persemaian') ? $user['nursery_id'] : null;

        $allStocks = $bahanBakuModel->getStockBalance(['nursery_id' => $nurseryId]);
        $materials = [];

        foreach ($allStocks as $stock) {
            if (trim(strtoupper($stock['category'])) === 'ENTRESS') {
                $materials[] = $stock;
            }
        }

        $this->json([
            'success' => true,
            'data' => $materials
        ]);
    }

    /**
     * Store Penaburan Benih
     */
    public function storeSeedSowing() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('seedling-admin');
            return;
        }

        if (!$this->validateCSRF()) {
            return;
        }

        $user = currentUser();
        $sowingModel = $this->model('SeedSowing');

        $sowingData = [
            'sowing_code'      => $this->post('sowing_code'),
            'sowing_date'      => $this->post('sowing_date'),
            'seed_item_id'     => (int)$this->post('seed_item_id'),
            'seed_quantity'    => (float)$this->post('seed_quantity'),
            'mandor'           => sanitize($this->post('mandor')),
            'manager'          => sanitize($this->post('manager')),
            'notes'            => sanitize($this->post('notes')),
            'bpdas_id'         => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['bpdas_id'],
            'nursery_id'       => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['nursery_id'],
            'created_by'       => $user['id']
        ];

        // Polybags structure
        $polybagIds = $this->post('bag_filling_id');
        $polyQty = $this->post('bag_qty');
        $polybagItems = [];
        
        if (is_array($polybagIds)) {
            for ($i = 0; $i < count($polybagIds); $i++) {
                if (!empty($polybagIds[$i])) {
                    $polybagItems[] = [
                        'bag_filling_id' => (int)$polybagIds[$i],
                        'quantity' => (float)$polyQty[$i]
                    ];
                }
            }
        }

        // Materials structure
        $materialIds = $this->post('material_item_id');
        $materialQty = $this->post('material_qty');
        $materialItems = [];
        
        if (is_array($materialIds)) {
            for ($i = 0; $i < count($materialIds); $i++) {
                if (!empty($materialIds[$i])) {
                    $materialItems[] = [
                        'item_id' => (int)$materialIds[$i],
                        'quantity' => (float)$materialQty[$i]
                    ];
                }
            }
        }

        if (empty($sowingData['seed_item_id'])) {
            $this->setFlash('error', 'Jenis Benih wajib diisi!');
            $this->redirect('seedling-admin/seed-sowing-form');
            return;
        }

        $result = $sowingModel->saveSowing($sowingData, $polybagItems, $materialItems);

        if ($result) {
            $this->setFlash('success', 'Data Penaburan Benih berhasil disimpan');
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal menyimpan data Penaburan Benih');
            $this->redirect('seedling-admin/seed-sowing-form');
        }
    }
    /**
     * Manage Categories Page
     */
    public function manageCategories() {
        $categories = $this->db->query("SELECT * FROM bahan_baku_categories ORDER BY code ASC")->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Kelola Kategori Barang',
            'categories' => $categories
        ];

        $this->render('seedling_admin/manage_categories', $data, 'dashboard');
    }

    /**
     * Save Category
     */
    public function saveCategory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->validateCSRF()) {
            return;
        }

        $id = $this->post('id');
        $code = strtoupper(sanitize($this->post('code')));
        $name = strtoupper(sanitize($this->post('name')));

        if ($id) {
            $sql = "UPDATE bahan_baku_categories SET code = ?, name = ? WHERE id = ?";
            $this->db->prepare($sql)->execute([$code, $name, $id]);
            $this->setFlash('success', 'Kategori berhasil diperbarui');
        } else {
            $sql = "INSERT INTO bahan_baku_categories (code, name) VALUES (?, ?)";
            $this->db->prepare($sql)->execute([$code, $name]);
            $this->setFlash('success', 'Kategori baru berhasil ditambahkan');
        }

        $this->redirect('seedling-admin/manage-categories');
    }

    /**
     * Delete Category
     */
    public function deleteCategory($id) {
        try {
            $this->db->prepare("DELETE FROM bahan_baku_categories WHERE id = ?")->execute([$id]);
            $this->setFlash('success', 'Kategori berhasil dihapus');
        } catch (PDOException $e) {
            $this->setFlash('error', 'Gagal: Kategori ini sedang digunakan oleh barang di Database Master.');
        }
        $this->redirect('seedling-admin/manage-categories');
    }

    /**
     * View: Pemanenan Semai Form
     */
    public function harvestingForm() {
        $harvestModel = $this->model('SeedlingHarvest');
        $harvestCode = $harvestModel->generateHarvestCode();

        $data = [
            'title' => 'Pemanenan Semai',
            'harvestCode' => $harvestCode,
            'today' => date('Y-m-d')
        ];

        $this->render('seedling_admin/harvesting_form', $data, 'dashboard');
    }

    /**
     * AJAX: Get Available Sowings (PC-)
     */
    public function getSowingsAJAX() {
        $sowingModel = $this->model('SeedSowing');
        $user = currentUser();
        $nurseryId = ($user['role'] === 'operator_persemaian') ? $user['nursery_id'] : null;

        $sowings = $sowingModel->getAvailableSowings(['nursery_id' => $nurseryId]);

        $this->json([
            'success' => true,
            'data' => $sowings
        ]);
    }

    /**
     * Store Pemanenan Semai
     */
    public function storeHarvesting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('seedling-admin');
            return;
        }

        if (!$this->validateCSRF()) {
            return;
        }

        $user = currentUser();
        $harvestModel = $this->model('SeedlingHarvest');

        $harvestData = [
            'harvest_code'       => $this->post('harvest_code'),
            'harvest_date'       => $this->post('harvest_date'),
            'sowing_id'          => (int)$this->post('sowing_id'),
            'harvested_quantity' => (int)$this->post('harvested_quantity'),
            'mandor'             => sanitize($this->post('mandor')),
            'manager'            => sanitize($this->post('manager')),
            'location'           => sanitize($this->post('location')),
            'notes'              => sanitize($this->post('notes')),
            'bpdas_id'           => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['bpdas_id'],
            'nursery_id'         => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['nursery_id'],
            'created_by'         => $user['id']
        ];

        if (empty($harvestData['sowing_id']) || empty($harvestData['harvested_quantity'])) {
            $this->setFlash('error', 'Semai dan Jumlah Anakan wajib diisi!');
            $this->redirect('seedling-admin/harvesting-form');
            return;
        }

        $id = $harvestModel->saveHarvest($harvestData);

        if ($id) {
            $this->setFlash('success', "Panen semai <b>{$harvestData['harvest_code']}</b> berhasil disimpan.");
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal menyimpan Pemanenan Semai. Silakan coba lagi.');
            $this->redirect('seedling-admin/harvesting-form');
        }
    }
    /**
     * View: Penyapihan Bibit Form (PE)
     */
    public function weaningForm() {

        $weaningModel = $this->model('SeedlingWeaning');
        $seedlingTypeModel = $this->model('SeedlingType');

        $weaningCode = $weaningModel->generateWeaningCode();
        $seedlingTypes = $seedlingTypeModel->getAllActive();

        $data = [
            'title' => 'Penyapihan Bibit',
            'weaningCode' => $weaningCode,
            'seedlingTypes' => $seedlingTypes,
            'today' => date('Y-m-d')
        ];

        $this->render('seedling_admin/weaning_form', $data, 'dashboard');
    }

    /**
     * AJAX: Get Available Harvests (PA-)
     */
    public function getHarvestsAJAX() {
        $harvestModel = $this->model('SeedlingHarvest');
        $user = currentUser();
        $nurseryId = ($user['role'] === 'operator_persemaian') ? $user['nursery_id'] : null;

        $harvests = $harvestModel->getAvailableHarvests(['nursery_id' => $nurseryId]);

        $this->json([
            'success' => true,
            'data' => $harvests
        ]);
    }

    /**
     * Store Penyapihan Bibit
     */
    public function storeWeaning() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('seedling-admin');
            return;
        }

        if (!$this->validateCSRF()) {
            return;
        }

        $user = currentUser();
        $weaningModel = $this->model('SeedlingWeaning');

        // Handle creation of new Seedling Type via AJAX-like request logic or POST
        $resultItemId = (int)$this->post('result_item_id');
        
        $weaningData = [
            'weaning_code'       => $this->post('weaning_code'),
            'weaning_date'       => $this->post('weaning_date'),
            'harvest_id'         => (int)$this->post('harvest_id'),
            'result_item_id'     => $resultItemId,
            'weaned_quantity'    => (int)$this->post('weaned_quantity'),
            'location'           => sanitize($this->post('location')),
            'mandor'             => sanitize($this->post('mandor')),
            'manager'            => sanitize($this->post('manager')),
            'notes'              => sanitize($this->post('notes')),
            'bpdas_id'           => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['bpdas_id'],
            'nursery_id'         => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['nursery_id'],
            'created_by'         => $user['id']
        ];

        // Process Arrays for Polybags and Materials
        $polybagItems = [];
        $materialItems = [];

        if (isset($_POST['pb_id']) && is_array($_POST['pb_id'])) {
            for ($i = 0; $i < count($_POST['pb_id']); $i++) {
                if (!empty($_POST['pb_id'][$i]) && !empty($_POST['pb_qty'][$i])) {
                    $polybagItems[] = [
                        'bag_filling_id' => (int)$_POST['pb_id'][$i],
                        'quantity' => (float)$_POST['pb_qty'][$i]
                    ];
                }
            }
        }

        if (isset($_POST['mat_id']) && is_array($_POST['mat_id'])) {
            for ($i = 0; $i < count($_POST['mat_id']); $i++) {
                if (!empty($_POST['mat_id'][$i]) && !empty($_POST['mat_qty'][$i])) {
                    $materialItems[] = [
                        'item_id' => (int)$_POST['mat_id'][$i],
                        'quantity' => (float)$_POST['mat_qty'][$i]
                    ];
                }
            }
        }

        if (empty($weaningData['harvest_id']) || empty($weaningData['weaned_quantity']) || empty($weaningData['result_item_id'])) {
            $this->setFlash('error', 'Data Anakan PA, Bibit Dihasilkan, dan Jumlah wajib diisi!');
            $this->redirect('seedling-admin/weaning-form');
            return;
        }

        $result = $weaningModel->saveWeaning($weaningData, $polybagItems, $materialItems);

        if ($result) {
            $this->setFlash('success', "Penyapihan Bibit <b>{$weaningData['weaning_code']}</b> berhasil disimpan.");
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal menyimpan Penyapihan Bibit. Silakan coba lagi.');
            $this->redirect('seedling-admin/weaning-form');
        }
    }

    /**
     * View: Entres Form (ET)
     */
    public function entresForm() {
        $entresModel = $this->model('SeedlingEntres');
        $entresCode = $entresModel->generateEntresCode();

        $data = [
            'title' => 'Entres (ET)',
            'entresCode' => $entresCode,
            'today' => date('Y-m-d')
        ];

        $this->render('seedling_admin/entres_form', $data, 'dashboard');
    }

    /**
     * Store Entres Transaction
     */
    /**
     * AJAX: Get Available Weanings (PE-) for Entres
     */
    public function getWeaningsAJAX() {
        $weaningModel = $this->model('SeedlingWeaning');
        $user = currentUser();
        $nurseryId = ($user['role'] === 'operator_persemaian') ? $user['nursery_id'] : null;

        $weanings = $weaningModel->getAvailableWeanings(['nursery_id' => $nurseryId]);

        $this->json([
            'success' => true,
            'data' => $weanings
        ]);
    }

    public function storeEntres() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->validateCSRF()) {
            $this->redirect('seedling-admin');
            return;
        }

        $user = currentUser();
        $entresModel = $this->model('SeedlingEntres');
        $weaningModel = $this->model('SeedlingWeaning');

        $weaningId = (int)$this->post('weaning_id');
        $usedQty = (int)$this->post('used_quantity');

        if (empty($weaningId) || empty($usedQty)) {
            $this->setFlash('error', 'Data Bibit PE dan Jumlah wajib diisi!');
            $this->redirect('seedling-admin/entres-form');
            return;
        }

        // Fetch Weaning info for automatically determining 'result_item_id'
        // We reuse the column 'harvest_id' in seedling_entres but it now stores weaning_id
        $weaningInfo = $weaningModel->findBy(['id' => $weaningId]);
        if (!$weaningInfo) {
            $this->setFlash('error', 'Data bibit asal (PE) tidak valid.');
            $this->redirect('seedling-admin/entres-form');
            return;
        }

        // Get the species name from the result_item_id of the weaning
        $seedlingTypeModel = $this->model('SeedlingType');
        $baseType = $seedlingTypeModel->find($weaningInfo['result_item_id']);
        
        // Handle auto-generation or fetching of the " - ENTRES" master type
        $resultItemId = $entresModel->getOrCreateEntresType(
            $baseType['name'], 
            $baseType['scientific_name']
        );

        $entresData = [
            'entres_code'    => $this->post('entres_code'),
            'entres_date'    => $this->post('entres_date'),
            'harvest_id'     => $weaningId, // We use harvest_id column for weaning_id
            'result_item_id' => $resultItemId,
            'used_quantity'  => $usedQty,
            'location'       => sanitize($this->post('location')),
            'mandor'         => sanitize($this->post('mandor')),
            'manager'        => sanitize($this->post('manager')),
            'notes'          => sanitize($this->post('notes')),
            'bpdas_id'       => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['bpdas_id'],
            'nursery_id'     => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'))['nursery_id'],
            'created_by'     => $user['id']
        ];

        // Process supporting Bahan Baku
        $materialItems = [];
        if (isset($_POST['mat_id']) && is_array($_POST['mat_id'])) {
            for ($i = 0; $i < count($_POST['mat_id']); $i++) {
                if (!empty($_POST['mat_id'][$i]) && !empty($_POST['mat_qty'][$i])) {
                    $materialItems[] = [
                        'item_id' => (int)$_POST['mat_id'][$i],
                        'quantity' => (float)$_POST['mat_qty'][$i]
                    ];
                }
            }
        }

        $result = $entresModel->saveEntres($entresData, $materialItems);

        if ($result) {
            $this->setFlash('success', "Proses Entres <b>{$entresData['entres_code']}</b> berhasil disimpan.");
            $this->redirect('seedling-admin');
        } else {
            $this->redirect('seedling-admin/entres-form');
        }
    }
    /**
     * View: Mutation Form (BO)
     */
    public function mutationForm() {
        $mutationModel = $this->model('SeedlingMutation');
        
        $data = [
            'mutationCode' => $mutationModel->generateMutationCode(),
            'today'        => date('Y-m-d')
        ];

        $this->render('seedling_admin/mutation_form', $data, 'dashboard');
    }

    /**
     * AJAX: Get Available Sources (PE or ET) for Mutation
     */
    public function getMutationSourcesAjax() {
        $type = $this->get('type'); // 'PE' or 'ET'
        $user = currentUser();
        $nurseryId = ($user['role'] === 'operator_persemaian') ? $user['nursery_id'] : null;

        if ($type === 'PE') {
            $model = $this->model('SeedlingWeaning');
            $data = $model->getAvailableWeanings(['nursery_id' => $nurseryId]);
        } else {
            $model = $this->model('SeedlingEntres');
            $data = $model->getAvailableEntres(['nursery_id' => $nurseryId]);
        }

        $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store Mutation Transaction
     */
    public function storeMutation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->validateCSRF()) {
            $this->redirect('seedling-admin');
            return;
        }

        $user = currentUser();
        $mutationModel = $this->model('SeedlingMutation');
        
        $sourceType = $this->post('source_type'); // PE / ET
        $sourceId = (int)$this->post('source_id');
        $mutationType = $this->post('mutation_type'); // MATI / NAIK KELAS / TRANSFER
        $quantity = (int)$this->post('quantity');

        if (empty($sourceId) || empty($quantity)) {
            $this->setFlash('error', 'Data bibit asal dan jumlah wajib diisi!');
            $this->redirect('seedling-admin/mutation-form');
            return;
        }

        // Get origin location to save it in record
        $originLocation = '';
        if ($sourceType === 'PE') {
            $sourceBatch = $this->model('SeedlingWeaning')->find($sourceId);
        } else {
            $sourceBatch = $this->model('SeedlingEntres')->find($sourceId);
        }
        $originLocation = $sourceBatch['location'] ?? '';

        $mutationData = [
            'mutation_code'   => $mutationModel->generateMutationCode(),
            'mutation_date'   => $this->post('mutation_date'),
            'source_type'     => $sourceType,
            'source_id'       => $sourceId,
            'mutation_type'   => $mutationType,
            'quantity'        => $quantity,
            'origin_location' => $originLocation,
            'target_location' => sanitize($this->post('target_location')),
            'mandor'          => sanitize($this->post('mandor')),
            'manager'         => sanitize($this->post('manager')),
            'notes'           => sanitize($this->post('notes')),
            'bpdas_id'        => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'), $sourceBatch['bpdas_id'] ?? null, $sourceBatch['nursery_id'] ?? null)['bpdas_id'],
            'nursery_id'      => $this->resolveLocationIds($this->post('bpdas_id'), $this->post('nursery_id'), $sourceBatch['bpdas_id'] ?? null, $sourceBatch['nursery_id'] ?? null)['nursery_id'],
            'created_by'      => $user['id']
        ];

        $result = $mutationModel->saveMutation($mutationData);

        if ($result) {
            $this->setFlash('success', "Mutasi Bibit <b>{$mutationData['mutation_code']}</b> berhasil diproses.");
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal menyimpan transaksi mutasi. Silakan periksa sisa stok.');
            $this->redirect('seedling-admin/mutation-form');
        }
    }
}

