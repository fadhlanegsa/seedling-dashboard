<?php
/**
 * SeedlingEntres Model
 * Handles the creation of 'Entres' seedlings from base harvests
 */

require_once CORE_PATH . 'Model.php';

class SeedlingEntres extends Model {
    protected $table = 'seedling_entres';

    /**
     * Generate next Entres Code (ET-YYYYMMXXX)
     * @return string
     */
    public function generateEntresCode() {
        $prefix = 'ET-' . date('Ym');
        $sql = "SELECT entres_code FROM {$this->table} 
                WHERE entres_code LIKE '{$prefix}%' 
                ORDER BY entres_code DESC LIMIT 1";
        $result = $this->query($sql);
        
        if (empty($result)) {
            return $prefix . '001';
        }
        
        $lastCode = $result[0]['entres_code'];
        $lastNumber = intval(substr($lastCode, -3));
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $nextNumber;
    }

    /**
     * Get or Create Seedling Type for Entres
     * Looks for "Name - Entres", creates if missing
     * @param string $baseName Base name of the seedling (e.g. SENGON)
     * @param string $scientificName Base scientific name
     * @param string $category Category logic (optional)
     * @return int Seedling Type ID
     */
    public function getOrCreateEntresType($baseName, $scientificName) {
        $entresName = trim($baseName) . ' - ENTRES';
        
        $sql = "SELECT id FROM seedling_types WHERE name = ?";
        $existing = $this->queryOne($sql, [$entresName]);
        
        if ($existing) {
            return $existing['id'];
        }
        
        // Use 'Pohon Hutan' as default valid ENUM value for the seedling_types table
        $insertSql = "INSERT INTO seedling_types (name, scientific_name, category) VALUES (?, ?, 'Pohon Hutan')";
        $stmt = $this->db->prepare($insertSql);
        $stmt->execute([$entresName, $scientificName]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Save Entres transaction with Materials
     */
    public function saveEntres($entresData, $materialItems) {
        try {
            $this->db->beginTransaction();

            // 1. Insert Master Entres
            $sql = "INSERT INTO {$this->table} 
                    (entres_code, entres_date, harvest_id, result_item_id, used_quantity, location, mandor, manager, notes, bpdas_id, nursery_id, created_by)
                    VALUES (:entres_code, :entres_date, :harvest_id, :result_item_id, :used_quantity, :location, :mandor, :manager, :notes, :bpdas_id, :nursery_id, :created_by)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($entresData);
            $entresId = $this->db->lastInsertId();

            // 2. Insert Supporting Materials
            if (!empty($materialItems)) {
                $sqlMat = "INSERT INTO seedling_entres_materials (entres_id, item_id, quantity) VALUES (?, ?, ?)";
                $stmtMat = $this->db->prepare($sqlMat);
                foreach ($materialItems as $mat) {
                    $stmtMat->execute([$entresId, $mat['item_id'], $mat['quantity']]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error saving seedling entres: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update Entres with Audit Trail
     */
    public function updateEntres($id, $entresData, $materialItems, $oldData, $editReason, $userId) {
        try {
            $this->beginTransaction();

            $this->update($id, $entresData);

            // Re-insert materials
            $this->db->prepare("DELETE FROM seedling_entres_materials WHERE entres_id = ?")->execute([$id]);
            if (!empty($materialItems)) {
                $sqlMat = "INSERT INTO seedling_entres_materials (entres_id, item_id, quantity) VALUES (?, ?, ?)";
                $stmtMat = $this->db->prepare($sqlMat);
                foreach ($materialItems as $mat) {
                    $stmtMat->execute([$id, $mat['item_id'], $mat['quantity']]);
                }
            }

            $this->insertAuditTrail('seedling_entres', $id, $oldData, [
                'entres' => $entresData,
                'materials' => $materialItems
            ], $editReason, $userId);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            logError("SeedlingEntres Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recent entres for dashboard
     */
    public function getRecentEntres($limit = 10, $filters = []) {
        $sql = "SELECT e.*, w.weaning_code, st.name as result_name,
                (e.used_quantity - COALESCE(m.mutation_stock, 0)) as remaining_stock,
                EXISTS(SELECT 1 FROM seedling_mutations WHERE source_id = e.id AND source_type = 'ET') as is_locked
                FROM {$this->table} e
                JOIN seedling_weanings w ON e.harvest_id = w.id
                JOIN seedling_types st ON e.result_item_id = st.id
                LEFT JOIN (
                    SELECT source_id, SUM(quantity) as mutation_stock
                    FROM seedling_mutations
                    WHERE source_type = 'ET'
                    GROUP BY source_id
                ) m ON e.id = m.source_id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "e.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $where[] = "e.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY e.created_at DESC LIMIT ?";
        $params[] = (int)$limit;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get available entres for Mutation (BO)
     */
    public function getAvailableEntres($filters = []) {
        $sql = "SELECT e.id, e.entres_code, e.entres_date, e.used_quantity as total_initial,
                e.location, st.name as seed_name, 'pcs' as seed_unit,
                COALESCE(m.mutation_stock, 0) as used_mutation,
                (e.used_quantity - COALESCE(m.mutation_stock, 0)) as remaining_stock
                FROM {$this->table} e
                JOIN seedling_types st ON e.result_item_id = st.id
                LEFT JOIN (
                    SELECT source_id, SUM(quantity) as mutation_stock
                    FROM seedling_mutations
                    WHERE source_type = 'ET'
                    GROUP BY source_id
                ) m ON e.id = m.source_id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "e.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " HAVING remaining_stock > 0 ORDER BY e.entres_date ASC";

        return $this->query($sql, $params);
    }

    /**
     * Delete Entres & Revert Stock
     */
    public function deleteEntres($id, $userId, $reason) {
        $oldData = $this->queryOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        if (!$oldData) return false;

        $materials = $this->query("SELECT * FROM seedling_entres_materials WHERE entres_id = ?", [$id]);

        $this->beginTransaction();
        try {
            // Delete downstream dependencies (Mutations from this entres)
            $this->db->prepare("DELETE FROM seedling_mutations WHERE source_id = ? AND source_type = 'ET'")->execute([$id]);

            // Delete materials
            $this->db->prepare("DELETE FROM seedling_entres_materials WHERE entres_id = ?")->execute([$id]);
            
            // Delete entres record
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            if (!$stmt->execute([$id])) {
                $this->rollback();
                return false;
            }

            // Log Audit Trail
            $this->insertAuditTrail('Sapih ET', $id, [
                'entres' => $oldData, 
                'materials' => $materials
            ], null, $reason, $userId);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            logError("SeedlingEntres Delete Error: " . $e->getMessage());
            return false;
        }
    }
}
