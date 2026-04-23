<?php
class SeedlingWeaning extends Model {
    protected $table = 'seedling_weanings';

    /**
     * Generate next Weaning Code (PE-YYYYMMXXX)
     * @return string
     */
    public function generateWeaningCode() {
        $prefix = 'PE-' . date('Ym');
        $sql = "SELECT weaning_code FROM {$this->table} 
                WHERE weaning_code LIKE ? 
                ORDER BY LENGTH(weaning_code) DESC, weaning_code DESC LIMIT 1";
        $result = $this->query($sql, [$prefix . '%']);
        
        if (empty($result)) {
            return $prefix . '001';
        }
        
        $lastCode = $result[0]['weaning_code'];
        $lastNumberStr = str_replace($prefix, '', $lastCode);
        $lastNumber = (int)$lastNumberStr;
        
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $newNumber;
    }

    /**
     * Save Weaning transaction with Polybags and Materials
     */
    public function saveWeaning($weaningData, $polybagItems, $materialItems) {
        try {
            $this->db->beginTransaction();

            // Double check for duplicate ID
            if (!empty($weaningData['weaning_code'])) {
                $existing = $this->queryOne("SELECT id FROM {$this->table} WHERE weaning_code = ? LIMIT 1", [$weaningData['weaning_code']]);
                if ($existing) {
                    $weaningData['weaning_code'] = $this->generateWeaningCode();
                }
            } else {
                $weaningData['weaning_code'] = $this->generateWeaningCode();
            }

            // 1. Insert Master Weaning
            $sql = "INSERT INTO {$this->table} 
                    (weaning_code, weaning_date, harvest_id, result_item_id, weaned_quantity, location, mandor, manager, notes, bpdas_id, nursery_id, created_by)
                    VALUES (:weaning_code, :weaning_date, :harvest_id, :result_item_id, :weaned_quantity, :location, :mandor, :manager, :notes, :bpdas_id, :nursery_id, :created_by)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($weaningData);
            $weaningId = $this->db->lastInsertId();

            // 2. Insert Polybags
            if (!empty($polybagItems)) {
                $sqlPB = "INSERT INTO seedling_weaning_polybags (weaning_id, bag_filling_id, quantity) VALUES (?, ?, ?)";
                $stmtPB = $this->db->prepare($sqlPB);
                foreach ($polybagItems as $pb) {
                    $stmtPB->execute([$weaningId, $pb['bag_filling_id'], $pb['quantity']]);
                }
            }

            // 3. Insert Supporting Materials
            if (!empty($materialItems)) {
                $sqlMat = "INSERT INTO seedling_weaning_materials (weaning_id, item_id, quantity) VALUES (?, ?, ?)";
                $stmtMat = $this->db->prepare($sqlMat);
                foreach ($materialItems as $mat) {
                    $stmtMat->execute([$weaningId, $mat['item_id'], $mat['quantity']]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error saving seedling weaning: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update Weaning with Audit Trail
     */
    public function updateWeaning($id, $weaningData, $polybagItems, $materialItems, $oldData, $editReason, $userId) {
        try {
            $this->beginTransaction();

            $this->update($id, $weaningData);

            // Re-insert polybags
            $this->db->prepare("DELETE FROM seedling_weaning_polybags WHERE weaning_id = ?")->execute([$id]);
            if (!empty($polybagItems)) {
                $sqlPB = "INSERT INTO seedling_weaning_polybags (weaning_id, bag_filling_id, quantity) VALUES (?, ?, ?)";
                $stmtPB = $this->db->prepare($sqlPB);
                foreach ($polybagItems as $pb) {
                    $stmtPB->execute([$id, $pb['bag_filling_id'], $pb['quantity']]);
                }
            }

            // Re-insert materials
            $this->db->prepare("DELETE FROM seedling_weaning_materials WHERE weaning_id = ?")->execute([$id]);
            if (!empty($materialItems)) {
                $sqlMat = "INSERT INTO seedling_weaning_materials (weaning_id, item_id, quantity) VALUES (?, ?, ?)";
                $stmtMat = $this->db->prepare($sqlMat);
                foreach ($materialItems as $mat) {
                    $stmtMat->execute([$id, $mat['item_id'], $mat['quantity']]);
                }
            }

            $this->insertAuditTrail('seedling_weanings', $id, $oldData, [
                'weaning' => $weaningData,
                'polybags' => $polybagItems,
                'materials' => $materialItems
            ], $editReason, $userId);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            logError("SeedlingWeaning Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recent weanings for dashboard
     */
    public function getRecentWeanings($limit = 10, $filters = []) {
        $sql = "SELECT w.*, h.harvest_code, st.name as result_name,
                (w.weaned_quantity - COALESCE(e.entres_stock, 0) - COALESCE(m.mutation_stock, 0)) as remaining_stock,
                (EXISTS(SELECT 1 FROM seedling_mutations WHERE source_id = w.id AND source_type = 'PE') OR
                 EXISTS(SELECT 1 FROM seedling_entres WHERE harvest_id = w.harvest_id)) as is_locked
                FROM {$this->table} w
                JOIN seedling_harvests h ON w.harvest_id = h.id
                JOIN seedling_types st ON w.result_item_id = st.id
                LEFT JOIN (
                    SELECT harvest_id, SUM(used_quantity) as entres_stock
                    FROM seedling_entres
                    GROUP BY harvest_id
                ) e ON w.id = e.harvest_id
                LEFT JOIN (
                    SELECT source_id, SUM(quantity) as mutation_stock
                    FROM seedling_mutations
                    WHERE source_type = 'PE'
                    GROUP BY source_id
                ) m ON w.id = m.source_id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "w.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $where[] = "w.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY w.created_at DESC LIMIT ?";
        $params[] = (int)$limit;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    /**
     * Get available weanings (sisa stok bibit di polybag) for Entres
     * @param array $filters
     * @return array
     */
    public function getAvailableWeanings($filters = []) {
        $sql = "SELECT w.id, w.weaning_code, w.weaning_date, w.weaned_quantity as total_initial,
                w.location, st.name as seed_name, 'pcs' as seed_unit,
                (COALESCE(e.entres_stock, 0) + COALESCE(m.mutation_stock, 0)) as used_total,
                (w.weaned_quantity - COALESCE(e.entres_stock, 0) - COALESCE(m.mutation_stock, 0)) as remaining_stock
                FROM {$this->table} w
                JOIN seedling_types st ON w.result_item_id = st.id
                LEFT JOIN (
                    SELECT harvest_id, SUM(used_quantity) as entres_stock
                    FROM seedling_entres
                    GROUP BY harvest_id
                ) e ON w.id = e.harvest_id
                LEFT JOIN (
                    SELECT source_id, SUM(quantity) as mutation_stock
                    FROM seedling_mutations
                    WHERE source_type = 'PE'
                    GROUP BY source_id
                ) m ON w.id = m.source_id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "w.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $where[] = "w.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " HAVING remaining_stock > 0 ORDER BY w.weaning_date ASC";

        return $this->query($sql, $params);
    }

    /**
     * Delete Weaning & Revert Stock
     */
    public function deleteWeaning($id, $userId, $reason) {
        $oldData = $this->queryOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        if (!$oldData) return false;

        $polybags = $this->query("SELECT * FROM seedling_weaning_polybags WHERE weaning_id = ?", [$id]);
        $materials = $this->query("SELECT * FROM seedling_weaning_materials WHERE weaning_id = ?", [$id]);

        $this->beginTransaction();
        try {
            // Delete downstream dependencies (Mutations from this weaning)
            $this->db->prepare("DELETE FROM seedling_mutations WHERE source_id = ? AND source_type = 'PE'")->execute([$id]);

            // Delete polybags & materials
            $this->db->prepare("DELETE FROM seedling_weaning_polybags WHERE weaning_id = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM seedling_weaning_materials WHERE weaning_id = ?")->execute([$id]);
            
            // Delete weaning record
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            if (!$stmt->execute([$id])) {
                $this->rollback();
                return false;
            }

            // Log Audit Trail
            $this->insertAuditTrail('Sapih PE', $id, [
                'weaning' => $oldData, 
                'polybags' => $polybags,
                'materials' => $materials
            ], null, $reason, $userId);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            logError("SeedlingWeaning Delete Error: " . $e->getMessage());
            return false;
        }
    }
}
