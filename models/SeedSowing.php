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
     * Update Sowing with Audit Trail
     */
    public function updateSowing($id, $sowingData, $polybagItems, $materialItems, $oldData, $editReason, $userId) {
        try {
            $this->beginTransaction();

            $this->update($id, $sowingData);

            // Re-insert Polybags
            $this->db->prepare("DELETE FROM seed_sowing_polybags WHERE sowing_id = ?")->execute([$id]);
            $sqlPolybags = "INSERT INTO seed_sowing_polybags (sowing_id, bag_filling_id, quantity) VALUES (?, ?, ?)";
            $stmtPoly = $this->db->prepare($sqlPolybags);
            foreach ($polybagItems as $poly) {
                if (empty($poly['bag_filling_id']) || empty($poly['quantity'])) continue;
                $stmtPoly->execute([$id, $poly['bag_filling_id'], $poly['quantity']]);
            }

            // Re-insert Materials
            $this->db->prepare("DELETE FROM seed_sowing_materials WHERE sowing_id = ?")->execute([$id]);
            $sqlMaterials = "INSERT INTO seed_sowing_materials (sowing_id, item_id, quantity) VALUES (?, ?, ?)";
            $stmtMat = $this->db->prepare($sqlMaterials);
            foreach ($materialItems as $mat) {
                if (empty($mat['item_id']) || empty($mat['quantity'])) continue;
                $stmtMat->execute([$id, $mat['item_id'], $mat['quantity']]);
            }

            $this->insertAuditTrail('seed_sowing', $id, $oldData, [
                'sowing' => $sowingData,
                'polybags' => $polybagItems,
                'materials' => $materialItems
            ], $editReason, $userId);

            $this->commit();
            return true;
        } catch (PDOException $e) {
            $this->rollback();
            logError("SeedSowing Update Error: " . $e->getMessage());
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
                (SELECT SUM(quantity) FROM seed_sowing_polybags WHERE sowing_id = s.id) as total_polybags,
                EXISTS(SELECT 1 FROM seedling_harvests WHERE sowing_id = s.id) as is_locked
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

    /**
     * Delete Sowing & Revert Stock
     */
    public function deleteSowing($id, $userId, $reason) {
        $oldData = $this->queryOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        if (!$oldData) return false;

        $polybags = $this->query("SELECT * FROM seed_sowing_polybags WHERE sowing_id = ?", [$id]);
        $materials = $this->query("SELECT * FROM seed_sowing_materials WHERE sowing_id = ?", [$id]);

        $this->beginTransaction();
        try {
            // Get all harvests for this sowing to handle deeper dependencies
            $harvests = $this->query("SELECT id FROM seedling_harvests WHERE sowing_id = ?", [$id]);
            foreach ($harvests as $h) {
                $hId = $h['id'];
                // Delete Mutations from Weanings/Entres of these harvests
                $this->db->prepare("DELETE FROM seedling_mutations WHERE source_id IN (SELECT id FROM seedling_weanings WHERE harvest_id = ?) AND source_type = 'PE'")->execute([$hId]);
                $this->db->prepare("DELETE FROM seedling_mutations WHERE source_id IN (SELECT id FROM seedling_entres WHERE harvest_id = ?) AND source_type = 'ET'")->execute([$hId]);
                
                // Delete Weaning items
                $this->db->prepare("DELETE FROM seedling_weaning_polybags WHERE weaning_id IN (SELECT id FROM seedling_weanings WHERE harvest_id = ?)")->execute([$hId]);
                $this->db->prepare("DELETE FROM seedling_weaning_materials WHERE weaning_id IN (SELECT id FROM seedling_weanings WHERE harvest_id = ?)")->execute([$hId]);
                
                // Delete Entres items
                $this->db->prepare("DELETE FROM seedling_entres_materials WHERE entres_id IN (SELECT id FROM seedling_entres WHERE harvest_id = ?)")->execute([$hId]);

                // Delete Weanings & Entres
                $this->db->prepare("DELETE FROM seedling_weanings WHERE harvest_id = ?")->execute([$hId]);
                $this->db->prepare("DELETE FROM seedling_entres WHERE harvest_id = ?")->execute([$hId]);
            }

            // Delete downstream dependencies (Harvests from this sowing)
            $this->db->prepare("DELETE FROM seedling_harvests WHERE sowing_id = ?")->execute([$id]);

            // Delete polybags & materials
            $this->db->prepare("DELETE FROM seed_sowing_polybags WHERE sowing_id = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM seed_sowing_materials WHERE sowing_id = ?")->execute([$id]);
            
            // Delete sowing record
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            if (!$stmt->execute([$id])) {
                $this->rollback();
                return false;
            }

            // Log Audit Trail
            $this->insertAuditTrail('Penaburan Benih (PC)', $id, [
                'sowing' => $oldData, 
                'polybags' => $polybags,
                'materials' => $materials
            ], null, $reason, $userId);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            logError("SeedSowing Delete Error: " . $e->getMessage());
            return false;
        }
    }
}
