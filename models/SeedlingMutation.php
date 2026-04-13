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
                $data['mutation_type'], $data['quantity'], $data['origin_location'], $data['target_location'],
                $data['mandor'], $data['manager'], $data['notes'], $data['bpdas_id'], 
                $data['nursery_id'], $data['created_by']
            ]);

            // If NAIK KELAS, increase the Ready Stock in 'stock' table
            if ($data['mutation_type'] === 'NAIK KELAS') {
                $this->updateReadyStock($data);
            } elseif ($data['mutation_type'] === 'TRANSFER') {
                $this->splitTransferStock($data);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            file_put_contents(__DIR__ . '/../mutation_error.log', date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);
            error_log("Error saving mutation: " . $e->getMessage());
            return false;
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
        // Find which seedling_type_id and batch info to carry over
        $seedlingTypeId = null;
        $batchInfo = '';
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

        // Upsert into stock table
        // We use program_type = 'bibitgratis' and source_type = 'PUB'
        $sqlCheck = "SELECT id, quantity, notes FROM stock 
                     WHERE nursery_id = ? AND seedling_type_id = ? AND program_type = 'bibitgratis' AND source_type = 'PUB'
                     LIMIT 1";
        
        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([$data['nursery_id'], $seedlingTypeId]);
        $existing = $stmt->fetch();

        $newNotes = "Asal PUB [" . $batchInfo . "]. " . date('d/m/Y');

        if ($existing) {
            $newQty = $existing['quantity'] + $data['quantity'];
            // Append info to notes if not already there, but keep it concise
            $updatedNotes = $existing['notes'] . " | " . $newNotes;
            if (strlen($updatedNotes) > 255) $updatedNotes = substr($updatedNotes, 0, 252) . "...";

            $sqlUpdate = "UPDATE stock SET quantity = ?, notes = ?, updated_at = CURRENT_TIMESTAMP, last_update_date = CURDATE() WHERE id = ?";
            $this->db->prepare($sqlUpdate)->execute([$newQty, $updatedNotes, $existing['id']]);
        } else {
            $sqlInsert = "INSERT INTO stock (bpdas_id, nursery_id, seedling_type_id, program_type, quantity, source_type, last_update_date, notes) 
                          VALUES (?, ?, ?, 'bibitgratis', ?, 'PUB', CURDATE(), ?)";
            $this->db->prepare($sqlInsert)->execute([
                $data['bpdas_id'], $data['nursery_id'], $seedlingTypeId, $data['quantity'], $newNotes
            ]);
        }
    }

    public function getRecentMutations($limit = 10, $nurseryId = null) {
        $sql = "SELECT m.*, 
                CASE 
                    WHEN m.source_type = 'PE' THEN (SELECT weaning_code FROM seedling_weanings WHERE id = m.source_id)
                    WHEN m.source_type = 'ET' THEN (SELECT entres_code FROM seedling_entres WHERE id = m.source_id)
                END as source_code,
                CASE 
                    WHEN m.source_type = 'PE' THEN (SELECT st.name FROM seedling_weanings w JOIN seedling_types st ON w.result_item_id = st.id WHERE w.id = m.source_id)
                    WHEN m.source_type = 'ET' THEN (SELECT st.name FROM seedling_entres e JOIN seedling_types st ON e.result_item_id = st.id WHERE e.id = m.source_id)
                END as seedling_name
                FROM {$this->table} m";
        
        if ($nurseryId) {
            $sql .= " WHERE m.nursery_id = " . (int)$nurseryId;
        }
        
        $sql .= " ORDER BY m.created_at DESC LIMIT " . (int)$limit;
        return $this->query($sql);
    }
}
