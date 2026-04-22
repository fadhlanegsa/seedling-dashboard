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
     * Update Bag Filling with Audit Trail
     */
    public function updateBagFilling($id, $fillingData, $mediaItems, $oldData, $editReason, $userId) {
        try {
            $this->beginTransaction();

            $this->update($id, $fillingData);
            
            // Re-insert items
            $this->db->prepare("DELETE FROM bag_filling_media WHERE bag_filling_id = ?")->execute([$id]);
            
            $sqlMedia = "INSERT INTO bag_filling_media (bag_filling_id, media_production_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sqlMedia);

            foreach ($mediaItems as $item) {
                if (empty($item['media_production_id']) || empty($item['quantity'])) continue;
                $stmt->execute([$id, $item['media_production_id'], $item['quantity']]);
            }

            $this->insertAuditTrail('bag_filling', $id, $oldData, [
                'filling' => $fillingData,
                'media' => $mediaItems
            ], $editReason, $userId);

            $this->commit();
            return true;
        } catch (PDOException $e) {
            $this->rollback();
            logError("BagFilling Update Error: " . $e->getMessage());
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
                (f.total_production - COALESCE(used.total_used, 0)) as remaining_stock,
                (EXISTS(SELECT 1 FROM seed_sowing_polybags WHERE bag_filling_id = f.id) OR 
                 EXISTS(SELECT 1 FROM seedling_weaning_polybags WHERE bag_filling_id = f.id)) as is_locked
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

    /**
     * Delete Filling & Revert Stock
     */
    public function deleteFilling($id, $userId, $reason) {
        $oldData = $this->queryOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        if (!$oldData) return false;

        $media = $this->query("SELECT * FROM bag_filling_media WHERE filling_id = ?", [$id]);

        $this->beginTransaction();
        try {
            // Get all Sowing records that used these bags to handle deeper dependencies
            $sowings = $this->query("SELECT DISTINCT sowing_id FROM seed_sowing_polybags WHERE bag_filling_id = ?", [$id]);
            foreach ($sowings as $s) {
                $sId = $s['sowing_id'];
                // Delete down to Mutations
                $this->db->prepare("DELETE FROM seedling_mutations WHERE source_id IN (SELECT id FROM seedling_weanings WHERE harvest_id IN (SELECT id FROM seedling_harvests WHERE sowing_id = ?)) AND source_type = 'PE'")->execute([$sId]);
                $this->db->prepare("DELETE FROM seedling_mutations WHERE source_id IN (SELECT id FROM seedling_entres WHERE harvest_id IN (SELECT id FROM seedling_harvests WHERE sowing_id = ?)) AND source_type = 'ET'")->execute([$sId]);
                $this->db->prepare("DELETE FROM seedling_weanings WHERE harvest_id IN (SELECT id FROM seedling_harvests WHERE sowing_id = ?)")->execute([$sId]);
                $this->db->prepare("DELETE FROM seedling_entres WHERE harvest_id IN (SELECT id FROM seedling_harvests WHERE sowing_id = ?)")->execute([$sId]);
                $this->db->prepare("DELETE FROM seedling_harvests WHERE sowing_id = ?")->execute([$sId]);
                $this->db->prepare("DELETE FROM seed_sowing_polybags WHERE sowing_id = ?")->execute([$sId]);
                $this->db->prepare("DELETE FROM seed_sowing_materials WHERE sowing_id = ?")->execute([$sId]);
                $this->db->prepare("DELETE FROM seed_sowings WHERE id = ?")->execute([$sId]);
            }

            // Delete downstream links (backup)
            $this->db->prepare("DELETE FROM seed_sowing_polybags WHERE bag_filling_id = ?")->execute([$id]);

            // Delete media relations
            $this->db->prepare("DELETE FROM bag_filling_media WHERE bag_filling_id = ?")->execute([$id]);
            
            // Delete filling record
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            if (!$stmt->execute([$id])) {
                $this->rollback();
                return false;
            }

            // Log Audit Trail
            $this->insertAuditTrail('Pengisian Kantong (PB)', $id, ['filling' => $oldData, 'media' => $media], null, $reason, $userId);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            logError("BagFilling Delete Error: " . $e->getMessage());
            return false;
        }
    }
}
