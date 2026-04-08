<?php
/**
 * Bag Filling Model
 * Handles recording the process of filling seedling bags with mixed media
 */

require_once CORE_PATH . 'Model.php';

class BagFilling extends Model {
    protected $table = 'bag_fillings';

    /**
     * Generate Auto Filling ID (Format: PB-YYYYMMXXX)
     * @return string
     */
    public function generateFillingID() {
        $prefix = "PB-" . date('Ym');
        $sql = "SELECT filling_code FROM {$this->table} 
                WHERE filling_code LIKE ? 
                ORDER BY filling_code DESC LIMIT 1";
        
        $last = $this->queryOne($sql, [$prefix . '%']);

        if (!$last) {
            return $prefix . "001";
        }

        $lastNumber = (int)substr($last['filling_code'], -3);
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $newNumber;
    }

    /**
     * Save Bag Filling with Media Components
     * @param array $fillingData
     * @param array $mediaItems array of [media_production_id, quantity]
     * @return int|bool Filling ID or false
     */
    public function saveBagFilling($fillingData, $mediaItems) {
        try {
            $this->beginTransaction();

            $fillingId = $this->create($fillingData);
            if (!$fillingId) {
                $this->rollback();
                return false;
            }

            $sqlMedia = "INSERT INTO bag_filling_media (bag_filling_id, media_production_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sqlMedia);

            foreach ($mediaItems as $item) {
                if (empty($item['media_production_id']) || empty($item['quantity'])) continue;
                $stmt->execute([$fillingId, $item['media_production_id'], $item['quantity']]);
            }

            $this->commit();
            return $fillingId;
        } catch (PDOException $e) {
            $this->rollback();
            logError("BagFilling Save Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recent fillings
     * @param int $limit
     * @param array $filters
     * @return array
     */
    public function getRecentFillings($limit = 10, $filters = []) {
        $sql = "SELECT f.*, m.name as bag_name, m.unit as bag_unit,
                (f.total_production - COALESCE(used.total_used, 0)) as remaining_stock
                FROM {$this->table} f
                JOIN bahan_baku_master m ON f.bag_item_id = m.id
                LEFT JOIN (
                    SELECT bag_filling_id, SUM(quantity) as total_used
                    FROM seed_sowing_polybags
                    GROUP BY bag_filling_id
                ) used ON f.id = used.bag_filling_id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "f.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $where[] = "f.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY f.created_at DESC LIMIT ?";
        $params[] = (int)$limit;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get available filled polybags (with remaining stock)
     * @param array $filters
     * @return array
     */
    public function getAvailableFilledBags($filters = []) {
        $sql = "SELECT f.*, m.name as bag_name, m.unit as bag_unit,
                (f.total_production - COALESCE(used.total_used, 0)) as remaining_stock
                FROM {$this->table} f
                JOIN bahan_baku_master m ON f.bag_item_id = m.id
                LEFT JOIN (
                    SELECT bag_filling_id, SUM(quantity) as total_used
                    FROM seed_sowing_polybags
                    GROUP BY bag_filling_id
                ) used ON f.id = used.bag_filling_id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "f.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $where[] = "f.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " HAVING remaining_stock > 0 ORDER BY f.filling_date DESC";

        return $this->query($sql, $params);
    }
}
