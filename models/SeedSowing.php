<?php
/**
 * Seed Sowing Model
 * Handles recording the process of sowing seeds into filled polybags
 */

require_once CORE_PATH . 'Model.php';

class SeedSowing extends Model {
    protected $table = 'seed_sowings';

    /**
     * Generate Auto Sowing ID (Format: PC-YYYYMMXXX)
     * @return string
     */
    public function generateSowingID() {
        $prefix = "PC-" . date('Ym');
        $sql = "SELECT sowing_code FROM {$this->table} 
                WHERE sowing_code LIKE ? 
                ORDER BY sowing_code DESC LIMIT 1";
        
        $last = $this->queryOne($sql, [$prefix . '%']);

        if (!$last) {
            return $prefix . "001";
        }

        $lastNumber = (int)substr($last['sowing_code'], -3);
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $newNumber;
    }

    /**
     * Save Seed Sowing with Polybags and Materials
     * @param array $sowingData Main transaction data
     * @param array $polybagItems Array of [bag_filling_id, quantity]
     * @param array $materialItems Array of [item_id, quantity]
     * @return int|bool Sowing ID or false
     */
    public function saveSowing($sowingData, $polybagItems, $materialItems) {
        try {
            $this->beginTransaction();

            // 1. Create main sowing record
            $sowingId = $this->create($sowingData);
            if (!$sowingId) {
                $this->rollback();
                return false;
            }

            // 2. Insert used Polybags
            $sqlPolybags = "INSERT INTO seed_sowing_polybags (sowing_id, bag_filling_id, quantity) VALUES (?, ?, ?)";
            $stmtPoly = $this->db->prepare($sqlPolybags);
            foreach ($polybagItems as $poly) {
                if (empty($poly['bag_filling_id']) || empty($poly['quantity'])) continue;
                $stmtPoly->execute([$sowingId, $poly['bag_filling_id'], $poly['quantity']]);
            }

            // 3. Insert used Supporting Materials
            $sqlMaterials = "INSERT INTO seed_sowing_materials (sowing_id, item_id, quantity) VALUES (?, ?, ?)";
            $stmtMat = $this->db->prepare($sqlMaterials);
            foreach ($materialItems as $mat) {
                if (empty($mat['item_id']) || empty($mat['quantity'])) continue;
                $stmtMat->execute([$sowingId, $mat['item_id'], $mat['quantity']]);
            }

            $this->commit();
            return $sowingId;
        } catch (PDOException $e) {
            $this->rollback();
            logError("SeedSowing Save Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recent seed sowings
     * @param int $limit
     * @param array $filters
     * @return array
     */
    public function getRecentSowings($limit = 10, $filters = []) {
        $sql = "SELECT s.*, m.name as seed_name, m.unit as seed_unit,
                (SELECT SUM(quantity) FROM seed_sowing_polybags WHERE sowing_id = s.id) as total_polybags
                FROM {$this->table} s
                JOIN bahan_baku_master m ON s.seed_item_id = m.id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "s.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $where[] = "s.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY s.created_at DESC LIMIT ?";
        $params[] = (int)$limit;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    /**
     * Get available seed sowings for harvesting
     * @param array $filters
     * @return array
     */
    public function getAvailableSowings($filters = []) {
        $sql = "SELECT s.id, s.sowing_code, s.sowing_date, s.seed_quantity,
                m.name as seed_name, m.unit as seed_unit
                FROM {$this->table} s
                JOIN bahan_baku_master m ON s.seed_item_id = m.id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "s.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $where[] = "s.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY s.sowing_date DESC, s.id DESC";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
