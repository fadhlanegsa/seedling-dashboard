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
     * Update Production with Audit Trail
     */
    public function updateProduction($id, $productionData, $items, $oldData, $editReason, $userId) {
        try {
            $this->beginTransaction();

            $this->update($id, $productionData);
            
            // Re-insert items
            $this->db->prepare("DELETE FROM media_mixing_items WHERE production_id = ?")->execute([$id]);
            
            $sqlItem = "INSERT INTO media_mixing_items (production_id, item_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sqlItem);

            foreach ($items as $item) {
                if (empty($item['item_id']) || empty($item['quantity'])) continue;
                $stmt->execute([$id, $item['item_id'], $item['quantity']]);
            }

            $this->insertAuditTrail('media_mixing', $id, $oldData, [
                'production' => $productionData,
                'items' => $items
            ], $editReason, $userId);

            $this->commit();
            return true;
        } catch (PDOException $e) {
            $this->rollback();
            logError("MediaMixing Update Error: " . $e->getMessage());
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
        $sql = "SELECT p.*, u.full_name as creator_name,
                EXISTS(SELECT 1 FROM bag_filling_media WHERE media_production_id = p.id) as is_locked
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

    /**
     * Delete Production & Revert Stock
     */
    public function deleteProduction($id, $userId, $reason) {
        $oldData = $this->queryOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        if (!$oldData) return false;

        $items = $this->query("SELECT * FROM media_mixing_items WHERE production_id = ?", [$id]);
        
        $this->beginTransaction();
        try {
            // Get all Bag Fillings that used this media to handle deeper dependencies
            $fillings = $this->query("SELECT DISTINCT bag_filling_id FROM bag_filling_media WHERE media_production_id = ?", [$id]);
            foreach ($fillings as $f) {
                $fId = $f['bag_filling_id'];
                // Delete Sowing records that used these bags (recursive-like)
                $sowings = $this->query("SELECT DISTINCT sowing_id FROM seed_sowing_polybags WHERE bag_filling_id = ?", [$fId]);
                foreach ($sowings as $s) {
                    $this->db->prepare("DELETE FROM seedling_mutations WHERE source_id IN (SELECT id FROM seedling_weanings WHERE harvest_id IN (SELECT id FROM seedling_harvests WHERE sowing_id = ?)) AND source_type = 'PE'")->execute([$s['sowing_id']]);
                    $this->db->prepare("DELETE FROM seedling_mutations WHERE source_id IN (SELECT id FROM seedling_entres WHERE harvest_id IN (SELECT id FROM seedling_harvests WHERE sowing_id = ?)) AND source_type = 'ET'")->execute([$s['sowing_id']]);
                    $this->db->prepare("DELETE FROM seedling_weanings WHERE harvest_id IN (SELECT id FROM seedling_harvests WHERE sowing_id = ?)")->execute([$s['sowing_id']]);
                    $this->db->prepare("DELETE FROM seedling_entres WHERE harvest_id IN (SELECT id FROM seedling_harvests WHERE sowing_id = ?)")->execute([$s['sowing_id']]);
                    $this->db->prepare("DELETE FROM seedling_harvests WHERE sowing_id = ?")->execute([$s['sowing_id']]);
                    $this->db->prepare("DELETE FROM seed_sowing_polybags WHERE sowing_id = ?")->execute([$s['sowing_id']]);
                    $this->db->prepare("DELETE FROM seed_sowing_materials WHERE sowing_id = ?")->execute([$s['sowing_id']]);
                    $this->db->prepare("DELETE FROM seed_sowings WHERE id = ?")->execute([$s['sowing_id']]);
                }
                // Delete the Bag Filling itself and its links
                $this->db->prepare("DELETE FROM seed_sowing_polybags WHERE bag_filling_id = ?")->execute([$fId]);
                $this->db->prepare("DELETE FROM bag_filling_media WHERE bag_filling_id = ?")->execute([$fId]);
                $this->db->prepare("DELETE FROM bag_fillings WHERE id = ?")->execute([$fId]);
            }

            // Delete downstream dependencies (Bag Filling Media usage) - backup in case some orphaned links exist
            $this->db->prepare("DELETE FROM bag_filling_media WHERE media_production_id = ?")->execute([$id]);

            // Delete items (ingredients)
            $this->db->prepare("DELETE FROM media_mixing_items WHERE production_id = ?")->execute([$id]);
            
            // Delete production
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            if (!$stmt->execute([$id])) {
                $this->rollback();
                return false;
            }

            // Log Audit Trail
            $this->insertAuditTrail('Mixing Media (MT)', $id, ['production' => $oldData, 'items' => $items], null, $reason, $userId);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            logError("MediaMixing Delete Error: " . $e->getMessage());
            return false;
        }
    }
}
