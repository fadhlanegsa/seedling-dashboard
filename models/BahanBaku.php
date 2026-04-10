<?php
/**
 * Bahan Baku Model
 * Handles raw materials for seedling production
 */

require_once CORE_PATH . 'Model.php';

class BahanBaku extends Model {
    protected $table = 'bahan_baku_transactions';

    /**
     * Helper to execute non-SELECT queries
     */
    private function execute($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            logError("BahanBaku Execute Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all categories with codes from master table
     * @return array
     */
    public function getCategories() {
        $sql = "SELECT code as category_code, name as category FROM bahan_baku_categories ORDER BY code ASC";
        return $this->query($sql);
    }

    /**
     * Get all master data items
     * @return array
     */
    public function getAllMaster() {
        $sql = "SELECT * FROM bahan_baku_master ORDER BY category_code ASC, name ASC";
        return $this->query($sql);
    }

    /**
     * Save/Update master item
     * @param array $data
     * @param int|null $id
     * @return bool
     */
    public function saveMaster($data, $id = null) {
        if ($id) {
            $sql = "UPDATE bahan_baku_master SET 
                    category_code = ?, category = ?, code = ?, name = ?, 
                    scientific_name = ?, unit = ?, description = ? 
                    WHERE id = ?";
            return $this->execute($sql, [
                $data['category_code'], $data['category'], $data['code'], 
                $data['name'], $data['scientific_name'], $data['unit'], 
                $data['description'], $id
            ]);
        } else {
            $sql = "INSERT INTO bahan_baku_master 
                    (category_code, category, code, name, scientific_name, unit, description) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            return $this->execute($sql, [
                $data['category_code'], $data['category'], $data['code'], 
                $data['name'], $data['scientific_name'], $data['unit'], 
                $data['description']
            ]);
        }
    }

    /**
     * Delete master item
     * @param int $id
     * @return bool
     */
    public function deleteMaster($id) {
        $sql = "DELETE FROM bahan_baku_master WHERE id = ?";
        return $this->execute($sql, [$id]);
    }

    /**
     * Get items by category
     * @param string $category
     * @return array
     */
    public function getItemsByCategory($category) {
        $sql = "SELECT id, name, scientific_name, unit, code FROM bahan_baku_master WHERE category = ? ORDER BY name ASC";
        return $this->query($sql, [$category]);
    }

    /**
     * Get master item by ID
     * @param int $id
     * @return array|null
     */
    public function getMasterItem($id) {
        $sql = "SELECT * FROM bahan_baku_master WHERE id = ?";
        return $this->queryOne($sql, [$id]);
    }

    /**
     * Generate Auto Transaction ID (Format: BI-YYYYMMXXX)
     * @return string
     */
    public function generateTransactionID() {
        $prefix = "BI-" . date('Ym');
        $sql = "SELECT transaction_id FROM {$this->table} 
                WHERE transaction_id LIKE ? 
                ORDER BY transaction_id DESC LIMIT 1";
        $last = $this->queryOne($sql, [$prefix . '%']);

        if (!$last) {
            return $prefix . "001";
        }

        $lastNumber = (int)substr($last['transaction_id'], -3);
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $newNumber;
    }

    /**
     * Save transaction
     * @param array $data
     * @return bool|string
     */
    public function saveTransaction($data) {
        return $this->create($data);
    }

    /**
     * Get recent transactions
     * @param int $limit
     * @return array
     */
    public function getRecentTransactions($limit = 10, $filters = []) {
        $sql = "SELECT t.*, m.name as item_name, m.category as item_category, m.unit as item_unit
                FROM {$this->table} t
                JOIN bahan_baku_master m ON t.item_id = m.id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "t.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $where[] = "t.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY t.created_at DESC LIMIT ?";
        $params[] = (int)$limit;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    /**
     * Get stock balance for each material
     * @param int|null $nursery_id
     * @return array
     */
    public function getStockBalance($filters = []) {
        $nurseryId = $filters['nursery_id'] ?? null;
        $bpdasId = $filters['bpdas_id'] ?? null;

        $sql = "SELECT m.id, m.name, m.category, m.unit,
                COALESCE(trans_in.total_in, 0) as total_in,
                (COALESCE(mixing_out.total_out, 0) + 
                 COALESCE(filling_out.total_out, 0) + 
                 COALESCE(sowing_seed_out.total_out, 0) + 
                 COALESCE(sowing_mat_out.total_out, 0) + 
                 COALESCE(weaning_mat_out.total_out, 0) + 
                 COALESCE(entres_mat_out.total_out, 0)) as total_out,
                (COALESCE(trans_in.total_in, 0) - (
                 COALESCE(mixing_out.total_out, 0) + 
                 COALESCE(filling_out.total_out, 0) + 
                 COALESCE(sowing_seed_out.total_out, 0) + 
                 COALESCE(sowing_mat_out.total_out, 0) + 
                 COALESCE(weaning_mat_out.total_out, 0) + 
                 COALESCE(entres_mat_out.total_out, 0))) as current_stock
                FROM bahan_baku_master m
                -- Total IN from transactions
                LEFT JOIN (
                    SELECT item_id, SUM(quantity) as total_in 
                    FROM bahan_baku_transactions 
                    WHERE 1=1 " . 
                    ($nurseryId ? " AND nursery_id = $nurseryId" : "") . 
                    ($bpdasId ? " AND bpdas_id = $bpdasId" : "") . "
                    GROUP BY item_id
                ) trans_in ON m.id = trans_in.item_id
                -- Total OUT from mixing items (Ingredients)
                LEFT JOIN (
                    SELECT mi.item_id, SUM(mi.quantity) as total_out
                    FROM media_mixing_items mi
                    JOIN media_mixing_productions mp ON mi.production_id = mp.id
                    WHERE 1=1 " . 
                    ($nurseryId ? " AND mp.nursery_id = $nurseryId" : "") . 
                    ($bpdasId ? " AND mp.bpdas_id = $bpdasId" : "") . "
                    GROUP BY mi.item_id
                ) mixing_out ON m.id = mixing_out.item_id
                -- Total OUT from bag filling (The bags themselves)
                LEFT JOIN (
                    SELECT bag_item_id as item_id, SUM(bag_quantity) as total_out
                    FROM bag_fillings
                    WHERE 1=1 " . 
                    ($nurseryId ? " AND nursery_id = $nurseryId" : "") . 
                    ($bpdasId ? " AND bpdas_id = $bpdasId" : "") . "
                    GROUP BY item_id
                ) filling_out ON m.id = filling_out.item_id
                -- Total OUT from seed sowing (Main seed)
                LEFT JOIN (
                    SELECT seed_item_id as item_id, SUM(seed_quantity) as total_out
                    FROM seed_sowings
                    WHERE 1=1 " . 
                    ($nurseryId ? " AND nursery_id = $nurseryId" : "") . 
                    ($bpdasId ? " AND bpdas_id = $bpdasId" : "") . "
                    GROUP BY item_id
                ) sowing_seed_out ON m.id = sowing_seed_out.item_id
                -- Total OUT from seed sowing materials (Supporting materials)
                LEFT JOIN (
                    SELECT sm.item_id, SUM(sm.quantity) as total_out
                    FROM seed_sowing_materials sm
                    JOIN seed_sowings ss ON sm.sowing_id = ss.id
                    WHERE 1=1 " . 
                    ($nurseryId ? " AND ss.nursery_id = $nurseryId" : "") . 
                    ($bpdasId ? " AND ss.bpdas_id = $bpdasId" : "") . "
                    GROUP BY sm.item_id
                ) sowing_mat_out ON m.id = sowing_mat_out.item_id
                -- Total OUT from weaning materials (Supporting materials for PE)
                LEFT JOIN (
                    SELECT wm.item_id, SUM(wm.quantity) as total_out
                    FROM seedling_weaning_materials wm
                    JOIN seedling_weanings sw ON wm.weaning_id = sw.id
                    WHERE 1=1 " . 
                    ($nurseryId ? " AND sw.nursery_id = $nurseryId" : "") . 
                    ($bpdasId ? " AND sw.bpdas_id = $bpdasId" : "") . "
                    GROUP BY wm.item_id
                ) weaning_mat_out ON m.id = weaning_mat_out.item_id
                -- Total OUT from entres materials (Supporting materials for ET)
                LEFT JOIN (
                    SELECT em.item_id, SUM(em.quantity) as total_out
                    FROM seedling_entres_materials em
                    JOIN seedling_entres se ON em.entres_id = se.id
                    WHERE 1=1 " . 
                    ($nurseryId ? " AND se.nursery_id = $nurseryId" : "") . 
                    ($bpdasId ? " AND se.bpdas_id = $bpdasId" : "") . "
                    GROUP BY em.item_id
                ) entres_mat_out ON m.id = entres_mat_out.item_id
                HAVING current_stock > 0 OR total_in > 0
                ORDER BY m.category ASC, m.name ASC";
        
        return $this->query($sql);
    }
}
