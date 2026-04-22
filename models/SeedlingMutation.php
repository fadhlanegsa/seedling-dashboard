<?php
/**
 * Seedling Mutation Model (BO)
 * Handles Deaths, Transfers, and Graduations (Naik Kelas)
 */
require_once CORE_PATH . 'Model.php';

class SeedlingMutation extends Model {
    protected $table = 'seedling_mutations';

    public function generateMutationCode() {
        $prefix = 'BO-' . date('Ym');
        $sql = "SELECT mutation_code FROM {$this->table} WHERE mutation_code LIKE ? ORDER BY mutation_code DESC LIMIT 1";
        $result = $this->query($sql, [$prefix . '%']);
        
        if (empty($result)) {
            return $prefix . '001';
        }

        $lastCode = $result[0]['mutation_code'];
        $lastNumber = (int)substr($lastCode, -3);
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $newNumber;
    }

    public function saveMutation($data) {
        // === STEP 1: Save the mutation record ===
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO {$this->table} (
                        mutation_code, mutation_date, source_type, source_id, 
                        mutation_type, quantity, origin_location, target_location, 
                        mandor, manager, notes, bpdas_id, nursery_id, created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['mutation_code'], $data['mutation_date'], $data['source_type'], $data['source_id'],
                $data['mutation_type'], $data['quantity'], $data['origin_location'] ?? null, $data['target_location'] ?? null,
                $data['mandor'] ?? null, $data['manager'] ?? null, $data['notes'] ?? null, $data['bpdas_id'], 
                $data['nursery_id'], $data['created_by']
            ]);

            $this->db->commit();

        } catch (Exception $e) {
            $this->db->rollBack();
            $errMsg = date('Y-m-d H:i:s') . " - saveMutation INSERT Error: " . $e->getMessage() . "\n";
            @file_put_contents(__DIR__ . '/../mutation_error.log', $errMsg, FILE_APPEND);
            error_log("saveMutation Error: " . $e->getMessage());
            return false;
        }

        // === STEP 2: Update Ready Stock (separate - non-blocking) ===
        if ($data['mutation_type'] === 'NAIK KELAS') {
            try {
                $this->updateReadyStock($data);
            } catch (Exception $e) {
                $errMsg = date('Y-m-d H:i:s') . " - updateReadyStock Error: " . $e->getMessage() . "\n";
                @file_put_contents(__DIR__ . '/../mutation_error.log', $errMsg, FILE_APPEND);
                error_log("updateReadyStock Error: " . $e->getMessage());
                // Don't return false - mutation is already saved
            }
        } elseif ($data['mutation_type'] === 'TRANSFER') {
            try {
                $this->splitTransferStock($data);
            } catch (Exception $e) {
                $errMsg = date('Y-m-d H:i:s') . " - splitTransferStock Error: " . $e->getMessage() . "\n";
                @file_put_contents(__DIR__ . '/../mutation_error.log', $errMsg, FILE_APPEND);
                error_log("splitTransferStock Error: " . $e->getMessage());
            }
        }

        return true;
    }

    /**
     * Update Mutation with Audit Trail
     */
    public function updateMutation($id, $data, $oldData, $editReason, $userId) {
        try {
            $this->beginTransaction();

            $this->update($id, $data);

            // Re-calculate Ready Stock difference if it's NAIK KELAS
            if ($oldData['mutation_type'] === 'NAIK KELAS') {
                $this->rollbackReadyStock($oldData);
            }
            if ($data['mutation_type'] === 'NAIK KELAS') {
                $this->updateReadyStock($data);
            }
            
            // Note: TRANSFER edits require complex split logic update, currently skipped for brevity or assumed handled in future enhancement if needed.

            $this->insertAuditTrail('seedling_mutation', $id, $oldData, $data, $editReason, $userId);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            logError("SeedlingMutation Update Error: " . $e->getMessage());
            return false;
        }
    }

    private function rollbackReadyStock($data) {
        $seedlingTypeId = null;
        if ($data['source_type'] === 'PE') {
            $sql = "SELECT result_item_id FROM seedling_weanings WHERE id = ?";
        } else {
            $sql = "SELECT result_item_id FROM seedling_entres WHERE id = ?";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$data['source_id']]);
        $row = $stmt->fetch();
        if (!$row) return;
        
        $seedlingTypeId = $row['result_item_id'];

        // Reduce stock
        $sqlCheck = "SELECT id, quantity FROM stock WHERE nursery_id = ? AND seedling_type_id = ? AND program_type = 'bibitgratis' AND source_type = 'PUB' LIMIT 1";
        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([$data['nursery_id'], $seedlingTypeId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = max(0, $existing['quantity'] - $data['quantity']);
            $sqlUpdate = "UPDATE stock SET quantity = ? WHERE id = ?";
            $this->db->prepare($sqlUpdate)->execute([$newQty, $existing['id']]);
        }
    }

    private function splitTransferStock($data) {
        if ($data['source_type'] === 'PE') {
            $stmt = $this->db->prepare("SELECT * FROM seedling_weanings WHERE id = ?");
            $stmt->execute([$data['source_id']]);
            $original = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($original) {
                // Generate a custom code showing it's a transfer split
                $newCode = $original['weaning_code'] . '-T' . time();
                if (strlen($newCode) > 50) $newCode = substr($newCode, 0, 50); // limit length
                
                $sql = "INSERT INTO seedling_weanings (
                            weaning_code, weaning_date, harvest_id, result_item_id, 
                            weaned_quantity, location, mandor, manager, notes, 
                            bpdas_id, nursery_id, created_by
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $this->db->prepare($sql)->execute([
                    $newCode, 
                    $data['mutation_date'], 
                    $original['harvest_id'], 
                    $original['result_item_id'],
                    $data['quantity'], 
                    $data['target_location'], 
                    $original['mandor'], 
                    $original['manager'], 
                    "Hasil Transfer dari " . $original['weaning_code'] . ". " . $data['notes'],
                    $data['bpdas_id'], 
                    $data['nursery_id'], 
                    $data['created_by']
                ]);
            }
        } else if ($data['source_type'] === 'ET') {
            $stmt = $this->db->prepare("SELECT * FROM seedling_entres WHERE id = ?");
            $stmt->execute([$data['source_id']]);
            $original = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($original) {
                $newCode = $original['entres_code'] . '-T' . time();
                if (strlen($newCode) > 50) $newCode = substr($newCode, 0, 50);
                
                $sql = "INSERT INTO seedling_entres (
                            entres_code, entres_date, harvest_id, weaning_id, result_item_id, 
                            used_quantity, yield_quantity, location, mandor, manager, notes, 
                            bpdas_id, nursery_id, created_by
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $this->db->prepare($sql)->execute([
                    $newCode, 
                    $data['mutation_date'], 
                    $original['harvest_id'],
                    $original['weaning_id'], 
                    $original['result_item_id'],
                    $data['quantity'], // Use quantity for both as it represents the batch volume
                    $data['quantity'], 
                    $data['target_location'], 
                    $original['mandor'], 
                    $original['manager'], 
                    "Hasil Transfer dari " . $original['entres_code'] . ". " . $data['notes'],
                    $data['bpdas_id'], 
                    $data['nursery_id'], 
                    $data['created_by']
                ]);
            }
        }
    }

    private function updateReadyStock($data) {
        $seedlingTypeId = null;
        if ($data['source_type'] === 'PE') {
            $sql = "SELECT result_item_id, weaning_code as code, location FROM seedling_weanings WHERE id = ?";
        } else {
            $sql = "SELECT result_item_id, entres_code as code, location FROM seedling_entres WHERE id = ?";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$data['source_id']]);
        $row = $stmt->fetch();
        if (!$row) return;
        
        $seedlingTypeId = $row['result_item_id'];
        $batchInfo = "Batch: " . $row['code'] . " (Lokasi: " . ($row['location'] ?: '-') . ")";

        // Resolve bpdas_id — fallback from nursery if null
        $bpdasId = $data['bpdas_id'] ?? null;
        $nurseryId = $data['nursery_id'] ?? null;

        if (empty($bpdasId) && !empty($nurseryId)) {
            $nRow = $this->db->prepare("SELECT bpdas_id FROM nurseries WHERE id = ? LIMIT 1");
            $nRow->execute([$nurseryId]);
            $nursery = $nRow->fetch();
            $bpdasId = $nursery['bpdas_id'] ?? null;
        }

        if (empty($bpdasId)) {
            // Last resort: get any bpdas_id available
            $bpdasRow = $this->db->query("SELECT id FROM bpdas LIMIT 1")->fetch();
            $bpdasId = $bpdasRow['id'] ?? null;
        }

        $newNotes = "Asal PUB [" . $batchInfo . "]. " . date('d/m/Y');

        // Check for existing stock row — query WITHOUT source_type first (for compatibility)
        // Then narrow down if source_type column exists
        $colCheck = $this->db->query("SHOW COLUMNS FROM stock LIKE 'source_type'")->rowCount();
        
        if ($colCheck > 0) {
            // source_type column exists — use full query
            $sqlCheck = "SELECT id, quantity, notes FROM stock 
                         WHERE nursery_id = ? AND seedling_type_id = ? AND program_type = 'bibitgratis' AND source_type = 'PUB'
                         LIMIT 1";
        } else {
            // source_type column missing — use basic query
            $sqlCheck = "SELECT id, quantity, notes FROM stock 
                         WHERE nursery_id = ? AND seedling_type_id = ? AND program_type = 'bibitgratis'
                         LIMIT 1";
        }

        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([$nurseryId, $seedlingTypeId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = $existing['quantity'] + $data['quantity'];
            $updatedNotes = $existing['notes'] . " | " . $newNotes;
            if (strlen($updatedNotes) > 255) $updatedNotes = substr($updatedNotes, 0, 252) . "...";

            $sqlUpdate = "UPDATE stock SET quantity = ?, notes = ?, last_update_date = CURDATE() WHERE id = ?";
            $this->db->prepare($sqlUpdate)->execute([$newQty, $updatedNotes, $existing['id']]);
        } else {
            if ($colCheck > 0) {
                // source_type column exists
                $sqlInsert = "INSERT INTO stock (bpdas_id, nursery_id, seedling_type_id, program_type, quantity, source_type, last_update_date, notes) 
                              VALUES (?, ?, ?, 'bibitgratis', ?, 'PUB', CURDATE(), ?)
                              ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), last_update_date = CURDATE()";
            } else {
                // source_type column does NOT exist — omit it
                $sqlInsert = "INSERT INTO stock (bpdas_id, nursery_id, seedling_type_id, program_type, quantity, last_update_date, notes) 
                              VALUES (?, ?, ?, 'bibitgratis', ?, CURDATE(), ?)
                              ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), last_update_date = CURDATE()";
            }
            $this->db->prepare($sqlInsert)->execute([
                $bpdasId, $nurseryId, $seedlingTypeId, $data['quantity'], $newNotes
            ]);
        }
    }

    public function getRecentMutations($limit = 10, $filters = []) {
        $sql = "SELECT m.*, 
                CASE 
                    WHEN m.source_type = 'PE' THEN (SELECT weaning_code FROM seedling_weanings WHERE id = m.source_id)
                    WHEN m.source_type = 'ET' THEN (SELECT entres_code FROM seedling_entres WHERE id = m.source_id)
                END as source_code,
                CASE 
                    WHEN m.source_type = 'PE' THEN (SELECT st.name FROM seedling_weanings w JOIN seedling_types st ON w.result_item_id = st.id WHERE w.id = m.source_id)
                    WHEN m.source_type = 'ET' THEN (SELECT st.name FROM seedling_entres e JOIN seedling_types st ON e.result_item_id = st.id WHERE e.id = m.source_id)
                END as seedling_name
                FROM {$this->table} m
                WHERE 1=1";
        
        $params = [];
        if (!empty($filters['nursery_id'])) {
            $sql .= " AND m.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        if (!empty($filters['bpdas_id'])) {
            $sql .= " AND m.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }
        
        $sql .= " ORDER BY m.created_at DESC LIMIT " . (int)$limit;
        
        return $this->query($sql, $params);
    }

    /**
     * Delete Mutation & Revert Stock
     */
    public function deleteMutation($id, $userId, $reason) {
        $oldData = $this->queryOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        if (!$oldData) return false;

        $this->beginTransaction();
        try {
            // Revert stock if NAIK KELAS
            if ($oldData['mutation_type'] === 'NAIK KELAS') {
                $this->rollbackReadyStock($oldData);
            }

            // Note: If it's a TRANSFER, the spawned Weaning record is left intact for manual deletion, 
            // but the source stock is automatically returned because this mutation is deleted.

            // Delete mutation record
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            if (!$stmt->execute([$id])) {
                $this->rollback();
                return false;
            }

            // Log Audit Trail
            $this->insertAuditTrail('Mutasi', $id, $oldData, null, $reason, $userId);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            logError("SeedlingMutation Delete Error: " . $e->getMessage());
            return false;
        }
    }
}
