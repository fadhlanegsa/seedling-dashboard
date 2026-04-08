<?php
/**
 * Media Mixing Model
 * Handles production records for mixed seedling media
 */

require_once CORE_PATH . 'Model.php';

class MediaMixing extends Model {
    protected $table = 'media_mixing_productions';

    /**
     * Generate Auto Production ID (Format: MT-YYYYMMXXX)
     * @return string
     */
    public function generateProductionID() {
        $prefix = "MT-" . date('Ym');
        $sql = "SELECT production_code FROM {$this->table} 
                WHERE production_code LIKE ? 
                ORDER BY production_code DESC LIMIT 1";
        
        $last = $this->queryOne($sql, [$prefix . '%']);

        if (!$last) {
            return $prefix . "001";
        }

        $lastNumber = (int)substr($last['production_code'], -3);
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $newNumber;
    }

    /**
     * Save Production with Items (Transaction Safe)
     * @param array $productionData
     * @param array $items array of [item_id, quantity]
     * @return int|bool Production ID or false
     */
    public function saveProduction($productionData, $items) {
        try {
            $this->beginTransaction();

            $productionId = $this->create($productionData);
            if (!$productionId) {
                $this->rollback();
                return false;
            }

            $sqlItem = "INSERT INTO media_mixing_items (production_id, item_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sqlItem);

            foreach ($items as $item) {
                if (empty($item['item_id']) || empty($item['quantity'])) continue;
                $stmt->execute([$productionId, $item['item_id'], $item['quantity']]);
            }

            $this->commit();
            return $productionId;
        } catch (PDOException $e) {
            $this->rollback();
            logError("MediaMixing Save Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recent productions
     * @param int $limit
     * @param array $filters
     * @return array
     */
    public function getRecentProductions($limit = 10, $filters = []) {
        $sql = "SELECT p.*, u.full_name as creator_name
                FROM {$this->table} p
                JOIN users u ON p.created_by = u.id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "p.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $where[] = "p.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT ?";
        $params[] = (int)$limit;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get available mixed media productions (with remaining stock)
     * @param array $filters
     * @return array
     */
    public function getAvailableMediaStock($filters = []) {
        $sql = "SELECT p.*, 
                (p.total_production - COALESCE(used.total_used, 0)) as remaining_stock,
                (SELECT GROUP_CONCAT(CONCAT(m.name, ' ', mi.quantity, ' ', m.unit) SEPARATOR ', ')
                 FROM media_mixing_items mi 
                 JOIN bahan_baku_master m ON mi.item_id = m.id
                 WHERE mi.production_id = p.id) as ingredients
                FROM {$this->table} p
                LEFT JOIN (
                    SELECT media_production_id, SUM(quantity) as total_used
                    FROM bag_filling_media
                    GROUP BY media_production_id
                ) used ON p.id = used.media_production_id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "p.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $where[] = "p.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " HAVING remaining_stock > 0 ORDER BY p.production_date DESC";

        return $this->query($sql, $params);
    }
}
