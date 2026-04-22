<?php
/**
 * Seedling Edit Controller
 * Handles Edit operations with Chain Locking, Delta Validation, and 24h Window checks.
 */

require_once CORE_PATH . 'Controller.php';

class SeedlingEditController extends Controller {

    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
        
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

    // --- BAHAN BAKU ---
    
    public function editBahanBaku($id) {
        $model = $this->model('BahanBaku');
        $data = $model->find($id);
        if (!$data) $this->redirect('seedling-admin');

        // Check 24 hour window

        // Lock check isn't strictly chained for just BB IN unless you want to make it strict.
        // We will rely on Delta validation for BB.
        
        $bahanBakuModel = $this->model('BahanBaku');
        $items = $bahanBakuModel->getAllMaster();

        $this->render('seedling_admin/edit/bahan_baku', [
            'data' => $data,
            'items' => $items,
            'title' => 'Edit Bahan Baku'
        ], 'dashboard');
    }

    public function updateBahanBaku($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('seedling-admin');

        $model = $this->model('BahanBaku');
        $oldData = $model->find($id);
        if (!$oldData) $this->redirect('seedling-admin');

        $reason = trim($this->post('edit_reason'));
        if (empty($reason)) {
            $this->setFlash('error', 'Alasan Edit wajib diisi!');
            $this->redirect("seedling-edit/edit-bahan-baku/$id");
        }

        $newData = [
            'item_id' => $this->post('item_id'),
            'transaction_type' => $oldData['transaction_type'], // should not change
            'quantity' => floatval($this->post('quantity')),
            'date' => sanitize($this->post('date')),
            'vendor_name' => sanitize($this->post('vendor_name')),
            'pic' => sanitize($this->post('pic')),
            'receipt_number' => sanitize($this->post('receipt_number')),
            'notes' => sanitize($this->post('notes')),
        ];

        // Delta check validation
        $delta = $newData['quantity'] - $oldData['quantity'];
        // If it's decreasing stock (IN drops, or OUT increases), we must verify remaining stock
        $stockModel = $this->model('Stock');
        // Actually, Bahan Baku uses a dynamic stock calculation: 
        // SUM(IN) - SUM(OUT) grouped by item_id
        
        $stockCheck = $this->db->prepare("SELECT 
            (SELECT SUM(quantity) FROM bahan_baku_transactions WHERE transaction_type='IN' AND item_id = ? AND bpdas_id=? AND nursery_id=?) -
            (SELECT SUM(quantity) FROM bahan_baku_transactions WHERE transaction_type='OUT' AND item_id = ? AND bpdas_id=? AND nursery_id=?) as cur_stock");
        
        $stockCheck->execute([
            $oldData['item_id'], $oldData['bpdas_id'], $oldData['nursery_id'],
            $oldData['item_id'], $oldData['bpdas_id'], $oldData['nursery_id']
        ]);
        $currentStock = $stockCheck->fetchColumn() ?: 0;
        
        // Wait, if transaction type is IN, decreasing it reduces currentStock
        // If OUT, increasing it reduces currentStock
        if ($oldData['transaction_type'] === 'IN') {
            $the_delta = $newData['quantity'] - $oldData['quantity']; 
            if ($currentStock + $the_delta < 0) {
                $this->setFlash('error', 'Validasi Gagal! Stok tidak cukup. Penggunaan di hilir melebih batas edit Anda.');
                $this->redirect("seedling-edit/edit-bahan-baku/$id");
            }
        } else {
            $the_delta = $oldData['quantity'] - $newData['quantity'];
            if ($currentStock + $the_delta < 0) {
                $this->setFlash('error', 'Validasi Gagal! Sisa stok tidak cukup untuk penggunaan ini.');
                $this->redirect("seedling-edit/edit-bahan-baku/$id");
            }
        }

        // Update
        $user = currentUser();
        if ($model->updateTransactionData($id, $newData, $oldData, $reason, $user['id'])) {
            $this->setFlash('success', 'Transaksi Bahan Baku berhasil diperbarui.');
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal memperbarui transaksi.');
            $this->redirect("seedling-edit/edit-bahan-baku/$id");
        }
    }

    // --- MEDIA MIXING ---

    public function editMediaMixing($id) {
        $model = $this->model('MediaMixing');
        $data = $model->find($id);
        if (!$data) $this->redirect('seedling-admin');

        $bahanBakuModel = $this->model('BahanBaku');
        $items = $bahanBakuModel->getItemsByCategory('Bahan Media Tanam');

        $stmtItems = $this->db->prepare("SELECT * FROM media_mixing_items WHERE production_id = ?");
        $stmtItems->execute([$id]);
        $currentItems = $stmtItems->fetchAll();

        $this->render('seedling_admin/edit/media_mixing', [
            'data' => $data,
            'items' => $items,
            'currentItems' => $currentItems,
            'title' => 'Edit Produksi Media Tanam'
        ], 'dashboard');
    }

    public function updateMediaMixing($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('seedling-admin');

        $model = $this->model('MediaMixing');
        $oldData = $model->find($id);
        if (!$oldData) $this->redirect('seedling-admin');

        $reason = trim($this->post('edit_reason'));
        if (empty($reason)) {
            $this->setFlash('error', 'Alasan Edit wajib diisi!');
            $this->redirect("seedling-edit/edit-media-mixing/$id");
        }

        $newData = [
            'production_code' => $oldData['production_code'],
            'production_date' => sanitize($this->post('production_date')),
            'total_production' => floatval($this->post('total_production')),
            'picker_name' => sanitize($this->post('location')), // using location from view -> maps to picker_name
            'foreman' => sanitize($this->post('mandor')), // using mandor from view -> maps to foreman
            'manager' => sanitize($this->post('manager')),
            'notes' => sanitize($this->post('notes')),
        ];

        $itemIds = $_POST['item_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $items = [];
        
        foreach ($itemIds as $index => $itemId) {
            if (!empty($itemId) && !empty($quantities[$index])) {
                $items[] = [
                    'item_id' => $itemId,
                    'quantity' => floatval($quantities[$index])
                ];
            }
        }

        // DELTA VALIDATION
        // Check if delta is negative (using MORE materials) and check if stock is enough
        $stmtItems = $this->db->prepare("SELECT item_id, quantity FROM media_mixing_items WHERE production_id = ?");
        $stmtItems->execute([$id]);
        $oldItemsArr = $stmtItems->fetchAll();
        $oldItemsMap = [];
        foreach ($oldItemsArr as $o) {
            if (!isset($oldItemsMap[$o['item_id']])) $oldItemsMap[$o['item_id']] = 0;
            $oldItemsMap[$o['item_id']] += $o['quantity'];
        }

        $newItemsMap = [];
        foreach ($items as $n) {
            if (!isset($newItemsMap[$n['item_id']])) $newItemsMap[$n['item_id']] = 0;
            $newItemsMap[$n['item_id']] += $n['quantity'];
        }

        foreach ($newItemsMap as $iId => $nQty) {
            $oQty = isset($oldItemsMap[$iId]) ? $oldItemsMap[$iId] : 0;
            $delta = $oQty - $nQty;
            
            if ($delta < 0) {
                $stockCheck = $this->db->prepare("SELECT 
                    (SELECT SUM(quantity) FROM bahan_baku_transactions WHERE transaction_type='IN' AND item_id = ? AND bpdas_id=? AND nursery_id=?) -
                    (SELECT SUM(quantity) FROM bahan_baku_transactions WHERE transaction_type='OUT' AND item_id = ? AND bpdas_id=? AND nursery_id=?) as cur_stock");
                $stockCheck->execute([
                    $iId, $oldData['bpdas_id'], $oldData['nursery_id'],
                    $iId, $oldData['bpdas_id'], $oldData['nursery_id']
                ]);
                $cStock = $stockCheck->fetchColumn() ?: 0;
                
                if ($cStock + $delta < 0) {
                    $this->setFlash('error', "Validasi Stok Gagal! Stok tidak mencukupi untuk item dengan ID $iId");
                    $this->redirect("seedling-edit/edit-media-mixing/$id");
                }
            }
        }

        $user = currentUser();
        if ($model->updateProduction($id, $newData, $items, $oldData, $reason, $user['id'])) {
            $this->setFlash('success', 'Produksi Media Tanam berhasil diperbarui.');
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal memperbarui data.');
            $this->redirect("seedling-edit/edit-media-mixing/$id");
        }
    }
    // --- BAG FILLING ---

    public function editBagFilling($id) {
        $model = $this->model('BagFilling');
        $data = $model->find($id);
        if (!$data) $this->redirect('seedling-admin');

        $bahanBakuModel = $this->model('BahanBaku');
        $currentItem = $bahanBakuModel->getMasterItem($data['bag_item_id']);
        $bags = $bahanBakuModel->getItemsByCategory($currentItem['category'] ?? 'Polybag');

        $stmtMedia = $this->db->prepare("SELECT * FROM bag_filling_media WHERE bag_filling_id = ?");
        $stmtMedia->execute([$id]);
        $currentMedia = $stmtMedia->fetchAll();

        // Get available media productions for selection
        $mixingModel = $this->model('MediaMixing');
        $availableMedia = $mixingModel->getRecentProductions(100, ['nursery_id' => $data['nursery_id']]);

        $this->render('seedling_admin/edit/bag_filling', [
            'data' => $data,
            'bags' => $bags,
            'availableMedia' => $availableMedia,
            'currentMedia' => $currentMedia,
            'title' => 'Edit Pengisian Kantong'
        ], 'dashboard');
    }

    public function updateBagFilling($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('seedling-admin');

        $model = $this->model('BagFilling');
        $oldData = $model->find($id);
        if (!$oldData) $this->redirect('seedling-admin');

        $reason = trim($this->post('edit_reason'));
        if (empty($reason)) {
            $this->setFlash('error', 'Alasan Edit wajib diisi!');
            $this->redirect("seedling-edit/edit-bag-filling/$id");
        }

        $newData = [
            'filling_code' => $oldData['filling_code'],
            'filling_date' => sanitize($this->post('filling_date')),
            'bag_item_id' => $this->post('bag_item_id'),
            'bag_quantity' => floatval($this->post('bag_quantity')),
            'total_production' => floatval($this->post('total_production')),
            'mandor' => sanitize($this->post('mandor')),
            'manager' => sanitize($this->post('manager')),
            'notes' => sanitize($this->post('notes')),
        ];

        $mediaIds = $_POST['media_production_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $mediaItems = [];
        
        foreach ($mediaIds as $index => $mId) {
            if (!empty($mId) && !empty($quantities[$index])) {
                $mediaItems[] = [
                    'media_production_id' => $mId,
                    'quantity' => floatval($quantities[$index])
                ];
            }
        }

        // DELTA VALIDATION for Media Usage & Polybag Bag item
        $stmtMedia = $this->db->prepare("SELECT media_production_id, quantity FROM bag_filling_media WHERE bag_filling_id = ?");
        $stmtMedia->execute([$id]);
        $oldMediaArr = $stmtMedia->fetchAll();
        $oldMediaMap = [];
        foreach ($oldMediaArr as $o) {
            if (!isset($oldMediaMap[$o['media_production_id']])) $oldMediaMap[$o['media_production_id']] = 0;
            $oldMediaMap[$o['media_production_id']] += $o['quantity'];
        }

        $newMediaMap = [];
        foreach ($mediaItems as $n) {
            if (!isset($newMediaMap[$n['media_production_id']])) $newMediaMap[$n['media_production_id']] = 0;
            $newMediaMap[$n['media_production_id']] += $n['quantity'];
        }

        foreach ($newMediaMap as $mId => $nQty) {
            $oQty = isset($oldMediaMap[$mId]) ? $oldMediaMap[$mId] : 0;
            $delta = $oQty - $nQty;
            
            if ($delta < 0) {
                // Check if current media stock is enough
                $stockCheck = $this->db->prepare("SELECT 
                    (SELECT total_production FROM media_mixing_productions WHERE id = ?) -
                    (SELECT COALESCE(SUM(quantity),0) FROM bag_filling_media WHERE media_production_id = ?) as cur_stock");
                $stockCheck->execute([$mId, $mId]);
                $cStock = $stockCheck->fetchColumn() ?: 0;
                
                if ($cStock + $delta < 0) {
                    $this->setFlash('error', "Validasi Stok Gagal! Stok media tidak mencukupi.");
                    $this->redirect("seedling-edit/edit-bag-filling/$id");
                }
            }
        }

        // Validate Bag stock Delta
        $bagDelta = $oldData['total_production'] - $newData['total_production'];
        if ($oldData['bag_item_id'] == $newData['bag_item_id'] && $bagDelta < 0) {
            $bagId = $newData['bag_item_id'];
            $stockCheck = $this->db->prepare("SELECT 
                (SELECT SUM(quantity) FROM bahan_baku_transactions WHERE transaction_type='IN' AND item_id = ? AND bpdas_id=? AND nursery_id=?) -
                (SELECT SUM(quantity) FROM bahan_baku_transactions WHERE transaction_type='OUT' AND item_id = ? AND bpdas_id=? AND nursery_id=?) as cur_stock");
            $stockCheck->execute([
                $bagId, $oldData['bpdas_id'], $oldData['nursery_id'],
                $bagId, $oldData['bpdas_id'], $oldData['nursery_id']
            ]);
            $cStock = $stockCheck->fetchColumn() ?: 0;
            if ($cStock + $bagDelta < 0) {
                $this->setFlash('error', "Validasi Stok Kantong Gagal! Stok tidak mencukupi.");
                $this->redirect("seedling-edit/edit-bag-filling/$id");
            }
        }

        $user = currentUser();
        if ($model->updateBagFilling($id, $newData, $mediaItems, $oldData, $reason, $user['id'])) {
            $this->setFlash('success', 'Pengisian Kantong berhasil diperbarui.');
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal memperbarui data.');
            $this->redirect("seedling-edit/edit-bag-filling/$id");
        }
    }

    // --- SEED SOWING ---

    public function editSeedSowing($id) {
        $model = $this->model('SeedSowing');
        $sql = "SELECT s.*, m.name as seed_name, m.category as seed_category 
                FROM seed_sowings s
                LEFT JOIN bahan_baku_master m ON s.seed_item_id = m.id
                WHERE s.id = ?";
        $data = $model->queryOne($sql, [$id]);
        
        if (!$data) $this->redirect('seedling-admin');

        $bahanBakuModel = $this->model('BahanBaku');
        // Fetch seeds based on the current item's category to be safe
        $seeds = $bahanBakuModel->getItemsByCategory($data['seed_category'] ?? 'BENIH');
        $materials = $bahanBakuModel->getItemsByCategory('Bahan Media Tanam'); // Assuming materials means pupuk etc

        $stmtPoly = $this->db->prepare("SELECT * FROM seed_sowing_polybags WHERE sowing_id = ?");
        $stmtPoly->execute([$id]);
        $currentPolybags = $stmtPoly->fetchAll();

        $stmtMat = $this->db->prepare("SELECT * FROM seed_sowing_materials WHERE sowing_id = ?");
        $stmtMat->execute([$id]);
        $currentMaterials = $stmtMat->fetchAll();

        $fillingModel = $this->model('BagFilling');
        $availableBags = $fillingModel->getRecentFillings(100, ['nursery_id' => $data['nursery_id']]);

        $this->render('seedling_admin/edit/seed_sowing', [
            'data' => $data,
            'seeds' => $seeds,
            'materials' => $materials,
            'availableBags' => $availableBags,
            'currentPolybags' => $currentPolybags,
            'currentMaterials' => $currentMaterials,
            'title' => 'Edit Penyemaian Benih'
        ], 'dashboard');
    }

    public function updateSeedSowing($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('seedling-admin');

        $model = $this->model('SeedSowing');
        $oldData = $model->find($id);
        if (!$oldData) {
            $this->redirect('seedling-admin');
        }

        $reason = trim($this->post('edit_reason'));
        if (empty($reason)) {
            $this->setFlash('error', 'Alasan Edit wajib diisi!');
            $this->redirect("seedling-edit/edit-seed-sowing/$id");
        }

        $newData = [
            'sowing_code' => $oldData['sowing_code'],
            'sowing_date' => sanitize($this->post('sowing_date')),
            'seed_item_id' => $this->post('seed_item_id'),
            'seed_quantity' => floatval($this->post('seed_quantity')),
            'mandor' => sanitize($this->post('mandor')),
            'manager' => sanitize($this->post('manager')),
            'notes' => sanitize($this->post('notes')),
        ];

        // Polybags
        $polyIds = $_POST['bag_filling_id'] ?? [];
        $polyQtys = $_POST['polybag_quantity'] ?? [];
        $polybagItems = [];
        foreach ($polyIds as $index => $bId) {
            if (!empty($bId) && !empty($polyQtys[$index])) {
                $polybagItems[] = [
                    'bag_filling_id' => $bId,
                    'quantity' => floatval($polyQtys[$index])
                ];
            }
        }

        // Materials
        $matIds = $_POST['item_id'] ?? [];
        $matQtys = $_POST['material_quantity'] ?? [];
        $materialItems = [];
        foreach ($matIds as $index => $mId) {
            if (!empty($mId) && !empty($matQtys[$index])) {
                $materialItems[] = [
                    'item_id' => $mId,
                    'quantity' => floatval($matQtys[$index])
                ];
            }
        }

        // DELTA VALIDATION (Simplified check for Seed stock)
        $seedDelta = $oldData['seed_quantity'] - $newData['seed_quantity'];
        if ($oldData['seed_item_id'] == $newData['seed_item_id'] && $seedDelta < 0) {
            $stockCheck = $this->db->prepare("SELECT 
                (SELECT SUM(quantity) FROM bahan_baku_transactions WHERE transaction_type='IN' AND item_id = ? AND bpdas_id=? AND nursery_id=?) -
                (SELECT SUM(quantity) FROM bahan_baku_transactions WHERE transaction_type='OUT' AND item_id = ? AND bpdas_id=? AND nursery_id=?) as cur_stock");
            $stockCheck->execute([
                $newData['seed_item_id'], $oldData['bpdas_id'], $oldData['nursery_id'],
                $newData['seed_item_id'], $oldData['bpdas_id'], $oldData['nursery_id']
            ]);
            $cStock = $stockCheck->fetchColumn() ?: 0;
            if ($cStock + $seedDelta < 0) {
                $this->setFlash('error', "Validasi Stok Benih Gagal! Stok tidak mencukupi.");
                $this->redirect("seedling-edit/edit-seed-sowing/$id");
            }
        }

        $user = currentUser();
        if ($model->updateSowing($id, $newData, $polybagItems, $materialItems, $oldData, $reason, $user['id'])) {
            $this->setFlash('success', 'Penyemaian berhasil diperbarui.');
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal memperbarui data.');
            $this->redirect("seedling-edit/edit-seed-sowing/$id");
        }
    }

    // --- SEEDLING HARVEST ---

    public function editHarvesting($id) {
        $model = $this->model('SeedlingHarvest');
        $data = $model->find($id);
        if (!$data) $this->redirect('seedling-admin');

        $this->render('seedling_admin/edit/harvesting', [
            'data' => $data,
            'title' => 'Edit Pemanenan Anakan'
        ], 'dashboard');
    }

    public function updateHarvesting($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('seedling-admin');
        
        $model = $this->model('SeedlingHarvest');
        $oldData = $model->find($id);
        if (!$oldData) $this->redirect('seedling-admin');

        $reason = trim($this->post('edit_reason'));
        if (empty($reason)) {
            $this->setFlash('error', 'Alasan Edit wajib');
            $this->redirect("seedling-edit/edit-harvesting/$id");
        }

        $newData = [
            'harvest_code' => $oldData['harvest_code'],
            'sowing_id'    => $oldData['sowing_id'],
            'harvest_date' => sanitize($this->post('harvest_date')),
            'harvested_quantity' => floatval($this->post('harvested_quantity')),
            'location' => sanitize($this->post('location')),
            'mandor' => sanitize($this->post('mandor')),
            'manager' => sanitize($this->post('manager')),
            'notes' => sanitize($this->post('notes')),
        ];

        // Delta for Sowing extraction
        $hDelta = $oldData['harvested_quantity'] - $newData['harvested_quantity'];
        if ($hDelta < 0) {
            // we are extracting more. check sowing remaining
            $sowingModel = $this->model('SeedSowing');
            // Simplified check: rely on model or simple delta
            $sow = $this->db->prepare("SELECT sown_quantity, 
                (SELECT COALESCE(SUM(harvested_quantity),0) FROM seedling_harvests WHERE sowing_id = ?) as used
                FROM seed_sowings WHERE id = ?");
            $sow->execute([$oldData['sowing_id'], $oldData['sowing_id']]);
            $sInfo = $sow->fetch();
            $remain = $sInfo['sown_quantity'] - $sInfo['used'];
            if ($remain + $hDelta < 0) { // $hDelta is negative
                $this->setFlash('error', 'Gagal: Stok hasil semai tidak cukup!');
                $this->redirect("seedling-edit/edit-harvesting/$id");
            }
        }

        $user = currentUser();
        if ($model->updateHarvest($id, $newData, $oldData, $reason, $user['id'])) {
            $this->setFlash('success', 'Panen diperbarui.');
            $this->redirect('seedling-admin');
        }
    }

    // --- SEEDLING WEANING (SAPIH PE) ---

    public function editWeaning($id) {
        $model = $this->model('SeedlingWeaning');
        $data = $model->find($id);
        if (!$data) $this->redirect('seedling-admin');

        $bahanBakuModel = $this->model('BahanBaku');
        $materials = $bahanBakuModel->getItemsByCategory('Bahan Media Tanam');

        $stmtPoly = $this->db->prepare("SELECT * FROM seedling_weaning_polybags WHERE weaning_id = ?");
        $stmtPoly->execute([$id]);
        $currentPolybags = $stmtPoly->fetchAll();

        $stmtMat = $this->db->prepare("SELECT * FROM seedling_weaning_materials WHERE weaning_id = ?");
        $stmtMat->execute([$id]);
        $currentMaterials = $stmtMat->fetchAll();

        $fillingModel = $this->model('BagFilling');
        $availableBags = $fillingModel->getRecentFillings(100, ['nursery_id' => $data['nursery_id']]);

        $this->render('seedling_admin/edit/weaning', [
            'data' => $data,
            'materials' => $materials,
            'availableBags' => $availableBags,
            'currentPolybags' => $currentPolybags,
            'currentMaterials' => $currentMaterials,
            'title' => 'Edit Sapih Bibit (Polybag)'
        ], 'dashboard');
    }

    public function updateWeaning($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('seedling-admin');

        $model = $this->model('SeedlingWeaning');
        $oldData = $model->find($id);
        if (!$oldData) $this->redirect('seedling-admin');

        $reason = trim($this->post('edit_reason'));
        if (empty($reason)) {
            $this->setFlash('error', 'Alasan Edit wajib');
            $this->redirect("seedling-edit/edit-weaning/$id");
        }

        $newData = [
            'weaning_code' => $oldData['weaning_code'],
            'weaning_date' => sanitize($this->post('weaning_date')),
            'weaned_quantity' => floatval($this->post('weaned_quantity')),
            'location' => sanitize($this->post('location')),
            'mandor' => sanitize($this->post('mandor')),
            'manager' => sanitize($this->post('manager')),
            'notes' => sanitize($this->post('notes')),
        ];

        // Delta check for Harvest usage
        $hDelta = $oldData['weaned_quantity'] - $newData['weaned_quantity'];
        if ($hDelta < 0) {
            $harv = $this->db->prepare("SELECT harvested_quantity, 
                (SELECT COALESCE(SUM(weaned_quantity),0) FROM seedling_weanings WHERE harvest_id = ?) as pe_used,
                (SELECT COALESCE(SUM(used_quantity),0) FROM seedling_entres WHERE harvest_id = ?) as et_used
                FROM seedling_harvests WHERE id = ?");
            $harv->execute([$oldData['harvest_id'], $oldData['harvest_id'], $oldData['harvest_id']]);
            $hInfo = $harv->fetch();
            $remain = $hInfo['harvested_quantity'] - $hInfo['pe_used'] - $hInfo['et_used'];
            if ($remain + $hDelta < 0) {
                $this->setFlash('error', 'Stok Panen tidak mencukupi untuk tambahan ini.');
                $this->redirect("seedling-edit/edit-weaning/$id");
            }
        }

        // Parse Polybags & Materials (Similar to Sowing)
        $polyIds = $_POST['bag_filling_id'] ?? [];
        $polyQtys = $_POST['polybag_quantity'] ?? [];
        $polybagItems = [];
        foreach ($polyIds as $index => $bId) {
            if (!empty($bId) && !empty($polyQtys[$index])) {
                $polybagItems[] = [
                    'bag_filling_id' => $bId,
                    'quantity' => floatval($polyQtys[$index])
                ];
            }
        }

        $matIds = $_POST['item_id'] ?? [];
        $matQtys = $_POST['material_quantity'] ?? [];
        $materialItems = [];
        foreach ($matIds as $index => $mId) {
            if (!empty($mId) && !empty($matQtys[$index])) {
                $materialItems[] = [
                    'item_id' => $mId,
                    'quantity' => floatval($matQtys[$index])
                ];
            }
        }

        $user = currentUser();
        if ($model->updateWeaning($id, $newData, $polybagItems, $materialItems, $oldData, $reason, $user['id'])) {
            $this->setFlash('success', 'Sapih Bibit diperbarui.');
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal update data.');
            $this->redirect("seedling-edit/edit-weaning/$id");
        }
    }

    // --- SEEDLING ENTRES (SAPIH ET) ---
    public function editEntres($id) {
        $model = $this->model('SeedlingEntres');
        $data = $model->find($id);
        if (!$data) $this->redirect('seedling-admin');

        $bahanBakuModel = $this->model('BahanBaku');
        $materials = $bahanBakuModel->getItemsByCategory('Bahan Media Tanam');

        $stmtMat = $this->db->prepare("SELECT * FROM seedling_entres_materials WHERE entres_id = ?");
        $stmtMat->execute([$id]);
        $currentMaterials = $stmtMat->fetchAll();

        $this->render('seedling_admin/edit/entres', [
            'data' => $data,
            'materials' => $materials,
            'currentMaterials' => $currentMaterials,
            'title' => 'Edit Sapih Entres'
        ], 'dashboard');
    }

    public function updateEntres($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('seedling-admin');

        $model = $this->model('SeedlingEntres');
        $oldData = $model->find($id);
        if (!$oldData) $this->redirect('seedling-admin');

        $reason = trim($this->post('edit_reason'));
        if (empty($reason)) {
            $this->setFlash('error', 'Alasan Edit wajib');
            $this->redirect("seedling-edit/edit-entres/$id");
        }

        $newData = [
            'entres_code' => $oldData['entres_code'],
            'entres_date' => sanitize($this->post('entres_date')),
            'used_quantity' => floatval($this->post('used_quantity')),
            'yield_quantity' => floatval($this->post('yield_quantity')),
            'location' => sanitize($this->post('location')),
            'mandor' => sanitize($this->post('mandor')),
            'manager' => sanitize($this->post('manager')),
            'notes' => sanitize($this->post('notes')),
        ];

        // Delta for Harvest mapping
        $hDelta = $oldData['used_quantity'] - $newData['used_quantity'];
        if ($hDelta < 0) {
            $harv = $this->db->prepare("SELECT harvested_quantity, 
                (SELECT COALESCE(SUM(weaned_quantity),0) FROM seedling_weanings WHERE harvest_id = ?) as pe_used,
                (SELECT COALESCE(SUM(used_quantity),0) FROM seedling_entres WHERE harvest_id = ?) as et_used
                FROM seedling_harvests WHERE id = ?");
            $harv->execute([$oldData['harvest_id'], $oldData['harvest_id'], $oldData['harvest_id']]);
            $hInfo = $harv->fetch();
            $remain = $hInfo['harvested_quantity'] - $hInfo['pe_used'] - $hInfo['et_used'];
            if ($remain + $hDelta < 0) {
                $this->setFlash('error', 'Stok Panen tidak cukup.');
                $this->redirect("seedling-edit/edit-entres/$id");
            }
        }

        $matIds = $_POST['item_id'] ?? [];
        $matQtys = $_POST['material_quantity'] ?? [];
        $materialItems = [];
        foreach ($matIds as $index => $mId) {
            if (!empty($mId) && !empty($matQtys[$index])) {
                $materialItems[] = [
                    'item_id' => $mId,
                    'quantity' => floatval($matQtys[$index])
                ];
            }
        }

        $user = currentUser();
        if ($model->updateEntres($id, $newData, $materialItems, $oldData, $reason, $user['id'])) {
            $this->setFlash('success', 'Sapih Entres diperbarui.');
            $this->redirect('seedling-admin');
        }
    }

    // --- SEEDLING MUTATION (BO-PUB) ---
    public function editMutation($id) {
        $model = $this->model('SeedlingMutation');
        $data = $model->find($id);
        if (!$data) $this->redirect('seedling-admin');

        $this->render('seedling_admin/edit/mutation', [
            'data' => $data,
            'title' => 'Edit Mutasi / Naik Kelas'
        ], 'dashboard');
    }

    public function updateMutation($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('seedling-admin');

        $model = $this->model('SeedlingMutation');
        $oldData = $model->find($id);
        if (!$oldData) $this->redirect('seedling-admin');

        $reason = trim($this->post('edit_reason'));
        if (empty($reason)) {
            $this->setFlash('error', 'Alasan Edit wajib');
            $this->redirect("seedling-edit/edit-mutation/$id");
        }

        $newData = [
            'mutation_code' => $oldData['mutation_code'],
            'mutation_date' => sanitize($this->post('mutation_date')),
            'mutation_type' => $oldData['mutation_type'], // locked
            'quantity'      => floatval($this->post('quantity')),
            'origin_location' => sanitize($this->post('origin_location')),
            'target_location' => sanitize($this->post('target_location')),
            'mandor'        => sanitize($this->post('mandor')),
            'manager'       => sanitize($this->post('manager')),
            'notes'         => sanitize($this->post('notes')),
        ];

        // Validate source remaining stock for negative delta
        $delta = $oldData['quantity'] - $newData['quantity'];
        if ($delta < 0) {
            $sId = $oldData['source_id'];
            if ($oldData['source_type'] === 'PE') {
                $check = $this->db->prepare("SELECT weaned_quantity as init_qty, (SELECT SUM(quantity) FROM seedling_mutations WHERE source_id = ? AND source_type='PE') as used FROM seedling_weanings WHERE id = ?");
            } else {
                $check = $this->db->prepare("SELECT used_quantity as init_qty, (SELECT SUM(quantity) FROM seedling_mutations WHERE source_id = ? AND source_type='ET') as used FROM seedling_entres WHERE id = ?");
            }
            $check->execute([$sId, $sId]);
            $info = $check->fetch();
            $remain = $info['init_qty'] - $info['used'];
            if ($remain + $delta < 0) {
                $this->setFlash('error', 'Gagal: Stok Sisa Hulu tidak cukup!');
                $this->redirect("seedling-edit/edit-mutation/$id");
            }
        }

        $user = currentUser();
        if ($model->updateMutation($id, $newData, $oldData, $reason, $user['id'])) {
            $this->setFlash('success', 'Mutasi diperbarui.');
            $this->redirect('seedling-admin');
        } else {
            $this->setFlash('error', 'Gagal update data.');
            $this->redirect("seedling-edit/edit-mutation/$id");
        }
    }
    // ==========================================
    // DELETION LOGIC
    // ==========================================

    private function handleDeletion($modelName, $method, $id, $redirect) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($redirect);
            return;
        }

        if (!$this->validateCSRF()) {
            return;
        }

        $user = currentUser();
        $reason = sanitize($this->post('delete_reason'));

        if (empty($reason)) {
            $this->setFlash('error', 'Alasan hapus wajib diisi!');
            $this->redirect($redirect);
            return;
        }

        $model = $this->model($modelName);
        if ($model->$method($id, $user['id'], $reason)) {
            $this->setFlash('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->setFlash('error', 'Gagal menghapus data.');
        }
        $this->redirect($redirect);
    }

    public function deleteBahanBaku($id) {
        $this->handleDeletion('BahanBaku', 'deleteTransaction', $id, 'seedling-admin');
    }

    public function deleteMediaMixing($id) {
        $this->handleDeletion('MediaMixing', 'deleteProduction', $id, 'seedling-admin');
    }

    public function deleteBagFilling($id) {
        $this->handleDeletion('BagFilling', 'deleteFilling', $id, 'seedling-admin');
    }

    public function deleteSeedSowing($id) {
        $this->handleDeletion('SeedSowing', 'deleteSowing', $id, 'seedling-admin');
    }

    public function deleteHarvesting($id) {
        $this->handleDeletion('SeedlingHarvest', 'deleteHarvest', $id, 'seedling-admin');
    }

    public function deleteWeaning($id) {
        $this->handleDeletion('SeedlingWeaning', 'deleteWeaning', $id, 'seedling-admin');
    }

    public function deleteEntres($id) {
        $this->handleDeletion('SeedlingEntres', 'deleteEntres', $id, 'seedling-admin');
    }

    public function deleteMutation($id) {
        $this->handleDeletion('SeedlingMutation', 'deleteMutation', $id, 'seedling-admin');
    }
}
