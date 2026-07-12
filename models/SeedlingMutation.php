<?php
/**
 * Seedling Mutation Model (BO)
 * Handles Deaths, Transfers, and Graduations (Naik Kelas)
 */
require_once CORE_PATH . 'Model.php';

class SeedlingMutation extends Model {
    protected $table = 'seedling_mutations';

    /**
     * Check if mutation_type is a NAIK KELAS variant
     */
    public static function isNaikKelas($type) {
        return in_array($type, [
            'NAIK KELAS',
            'NAIK KELAS (REGULER)',
            'NAIK KELAS (FOLU)',
            'NAIK KELAS (RHL)'
        ]);
    }

    /**
     * Map mutation_type to stock program_type
     * NAIK KELAS (REGULER) → 'Reguler'
     * NAIK KELAS (FOLU)    → 'FOLU'
     * NAIK KELAS (RHL)     → 'RHL'
     * Legacy 'NAIK KELAS'  → 'Reguler' (backward compat)
     */
    public static function getProgramTypeFromMutation($mutationType) {
        $map = [
            'NAIK KELAS (REGULER)' => 'Reguler',
            'NAIK KELAS (FOLU)'    => 'FOLU',
            'NAIK KELAS (RHL)'     => 'RHL',
            'NAIK KELAS'           => 'Reguler', // legacy fallback
        ];
        return $map[$mutationType] ?? 'Reguler';
    }

    public function generateMutationCode() {
        $prefix = 'BO-' . date('Ym');
        $sql = "SELECT mutation_code FROM {$this->table} 
                WHERE mutation_code LIKE ? AND mutation_code REGEXP ?
                ORDER BY mutation_code DESC LIMIT 1";
        $result = $this->query($sql, [$prefix . '%', '^BO-[0-9]{9}$']);
        
        if (empty($result)) {
            return $prefix . '001';
        }

        $lastCode = $result[0]['mutation_code'];
        $lastNumberStr = str_replace($prefix, '', $lastCode);
        $lastNumber = (int)$lastNumberStr;
        
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $newNumber;
    }

    public function saveMutation($data) {
        // === STEP 1: Save the mutation record ===
        try {
            $this->db->beginTransaction();

            // Double check for duplicate ID
            if (!empty($data['mutation_code'])) {
                $existing = $this->queryOne("SELECT id FROM {$this->table} WHERE mutation_code = ? LIMIT 1", [$data['mutation_code']]);
                if ($existing) {
                    $data['mutation_code'] = $this->generateMutationCode();
                }
            } else {
                $data['mutation_code'] = $this->generateMutationCode();
            }

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
        if (self::isNaikKelas($data['mutation_type'])) {
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
            if (self::isNaikKelas($oldData['mutation_type'])) {
                $this->rollbackReadyStock($oldData);
            }
            if (self::isNaikKelas($data['mutation_type'])) {
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

        // program_type is authoritatively determined by the mutation_type dropdown
        // (NAIK KELAS REGULER/FOLU/RHL), overriding whatever the source record carries
        $programType = self::getProgramTypeFromMutation($data['mutation_type'] ?? 'NAIK KELAS');

        $sqlCheck = "SELECT id, quantity FROM stock WHERE nursery_id = ? AND seedling_type_id = ? AND program_type = ? AND source_type = 'PUB' LIMIT 1";
        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([$data['nursery_id'], $seedlingTypeId, $programType]);
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

        // program_type is authoritatively determined by the mutation_type dropdown
        // (NAIK KELAS REGULER/FOLU/RHL), overriding the 'Reguler' default carried over
        // from the sowing/harvest/weaning chain
        $programType = self::getProgramTypeFromMutation($data['mutation_type']);

        $newNotes = "Asal PUB [" . $batchInfo . "]. " . date('d/m/Y');

        // Check for existing stock row — query WITHOUT source_type first (for compatibility)
        // Then narrow down if source_type column exists
        $colCheck = $this->db->query("SHOW COLUMNS FROM stock LIKE 'source_type'")->rowCount();
        
        if ($colCheck > 0) {
            // source_type column exists — use full query
            $sqlCheck = "SELECT id, quantity, notes FROM stock 
                         WHERE nursery_id = ? AND seedling_type_id = ? AND program_type = ? AND source_type = 'PUB'
                         LIMIT 1";
        } else {
            // source_type column missing — use basic query
            $sqlCheck = "SELECT id, quantity, notes FROM stock 
                         WHERE nursery_id = ? AND seedling_type_id = ? AND program_type = ?
                         LIMIT 1";
        }

        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([$nurseryId, $seedlingTypeId, $programType]);
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
                              VALUES (?, ?, ?, ?, ?, 'PUB', CURDATE(), ?)
                              ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), last_update_date = CURDATE()";
            } else {
                // source_type column does NOT exist — omit it
                $sqlInsert = "INSERT INTO stock (bpdas_id, nursery_id, seedling_type_id, program_type, quantity, last_update_date, notes) 
                              VALUES (?, ?, ?, ?, ?, CURDATE(), ?)
                              ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), last_update_date = CURDATE()";
            }
            $this->db->prepare($sqlInsert)->execute([
                $bpdasId, $nurseryId, $seedlingTypeId, $programType, $data['quantity'], $newNotes
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
     * Paginate mutations
     */
    public function paginateMutations($page = 1, $perPage = 10, $filters = []) {
        $offset = ($page - 1) * $perPage;
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
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} m WHERE 1=1";
        $params = [];
        if (!empty($filters['nursery_id'])) {
            $sql .= " AND m.nursery_id = ?";
            $countSql .= " AND m.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        if (!empty($filters['bpdas_id'])) {
            $sql .= " AND m.bpdas_id = ?";
            $countSql .= " AND m.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }
        
        $sql .= " ORDER BY m.created_at DESC LIMIT ? OFFSET ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k + 1, $v);
            }
            $stmt->bindValue(count($params) + 1, (int)$perPage, PDO::PARAM_INT);
            $stmt->bindValue(count($params) + 2, (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll();

            $countStmt = $this->db->prepare($countSql);
            foreach ($params as $k => $v) {
                $countStmt->bindValue($k + 1, $v);
            }
            $countStmt->execute();
            $total = (int)$countStmt->fetchColumn();

            return [
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => ceil($total / $perPage)
            ];
        } catch (PDOException $e) {
            logError("SeedlingMutation Paginate Error: " . $e->getMessage());
            return [
                'data' => [],
                'total' => 0,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => 0
            ];
        }
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
            if (self::isNaikKelas($oldData['mutation_type'])) {
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

    /**
     * Get Batch Traceability Data
     * Telusuri riwayat bibit dari Mutasi (PE/ET) ke sumber benih & komposisi media
     */
    public function getBatchTraceability($sourceType, $sourceId) {
        $result = [
            'seed_source' => null,
            'sowing'      => null,
            'weaning'     => null,
            'media'       => null
        ];

        try {
            // Query 1: Rantai Utama (Weaning/Entres -> Harvest -> Sowing -> Seed Source)
            if ($sourceType === 'PE') {
                $sqlMain = "SELECT 
                                w.weaning_code, w.weaning_date, w.location as weaning_location, w.weaned_quantity,
                                h.harvest_code, h.harvest_date, h.location as harvest_location,
                                s.id as sowing_id, s.sowing_code, s.sowing_date, s.seed_quantity, 
                                m.name as seed_name, m.scientific_name, m.unit as seed_unit,
                                ss.seed_source_name, ss.location as seed_location, ss.certificate_number, ss.owner_name
                            FROM seedling_weanings w
                            LEFT JOIN seedling_harvests h ON w.harvest_id = h.id
                            LEFT JOIN seed_sowings s ON h.sowing_id = s.id
                            LEFT JOIN bahan_baku_master m ON s.seed_item_id = m.id
                            LEFT JOIN seed_sources ss ON s.seed_source_id = ss.id
                            WHERE w.id = ?";
            } else {
                $sqlMain = "SELECT 
                                e.entres_code as weaning_code, e.entres_date as weaning_date, e.location as weaning_location, e.used_quantity as weaned_quantity,
                                h.harvest_code, h.harvest_date, h.location as harvest_location,
                                s.id as sowing_id, s.sowing_code, s.sowing_date, s.seed_quantity, 
                                m.name as seed_name, m.scientific_name, m.unit as seed_unit,
                                ss.seed_source_name, ss.location as seed_location, ss.certificate_number, ss.owner_name
                            FROM seedling_entres e
                            LEFT JOIN seedling_harvests h ON e.harvest_id = h.id
                            LEFT JOIN seed_sowings s ON h.sowing_id = s.id
                            LEFT JOIN bahan_baku_master m ON s.seed_item_id = m.id
                            LEFT JOIN seed_sources ss ON s.seed_source_id = ss.id
                            WHERE e.id = ?";
            }

            $mainData = $this->queryOne($sqlMain, [$sourceId]);

            if ($mainData) {
                $result['weaning'] = [
                    'code' => $mainData['weaning_code'],
                    'date' => $mainData['weaning_date'],
                    'location' => $mainData['weaning_location'],
                    'quantity' => $mainData['weaned_quantity']
                ];
                $result['sowing'] = [
                    'code' => $mainData['sowing_code'],
                    'date' => $mainData['sowing_date'],
                    'seed_name' => $mainData['seed_name'],
                    'seed_quantity' => $mainData['seed_quantity'],
                    'seed_unit' => $mainData['seed_unit']
                ];
                if ($mainData['seed_source_name']) {
                    $result['seed_source'] = [
                        'name' => $mainData['seed_source_name'],
                        'kabupaten' => $mainData['seed_location'],
                        'sertifikat' => $mainData['certificate_number'],
                        'vendor' => $mainData['owner_name']
                    ];
                }

                if (empty($mainData['sowing_id']) && $sourceType === 'PE') {
                    // Direct seed weaning: Fetch seed info from seedling_weaning_seeds
                    $directSeedSql = "SELECT ws.quantity, m.name as seed_name, m.scientific_name, m.unit as seed_unit
                                      FROM seedling_weaning_seeds ws
                                      JOIN bahan_baku_master m ON ws.item_id = m.id
                                      WHERE ws.weaning_id = ? LIMIT 1";
                    $directSeedInfo = $this->queryOne($directSeedSql, [$sourceId]);
                    if ($directSeedInfo) {
                        $result['sowing'] = [
                            'code' => 'Penyapihan Langsung',
                            'date' => $mainData['weaning_date'],
                            'seed_name' => $directSeedInfo['seed_name'],
                            'seed_quantity' => $directSeedInfo['quantity'],
                            'seed_unit' => $directSeedInfo['seed_unit']
                        ];
                    }
                }

                // Query 2: Komposisi Media (Sowing -> Sowing Polybags -> Bag Filling -> Media Mixing -> Items)
                $bagFillingId = null;
                if (!empty($mainData['sowing_id'])) {
                    // Cari bag_filling_id dari seed_sowing_polybags
                    $polybagSql = "SELECT sp.bag_filling_id 
                                   FROM seed_sowing_polybags sp
                                   WHERE sp.sowing_id = ? LIMIT 1";
                    $polybagInfo = $this->queryOne($polybagSql, [$mainData['sowing_id']]);
                    if ($polybagInfo && !empty($polybagInfo['bag_filling_id'])) {
                        $bagFillingId = $polybagInfo['bag_filling_id'];
                    }
                } elseif ($sourceType === 'PE') {
                    // Direct seed weaning: Get bag_filling_id from seedling_weaning_polybags
                    $polybagSql = "SELECT wp.bag_filling_id 
                                   FROM seedling_weaning_polybags wp
                                   WHERE wp.weaning_id = ? LIMIT 1";
                    $polybagInfo = $this->queryOne($polybagSql, [$sourceId]);
                    if ($polybagInfo && !empty($polybagInfo['bag_filling_id'])) {
                        $bagFillingId = $polybagInfo['bag_filling_id'];
                    }
                }
                
                if (!empty($bagFillingId)) {
                    // Trace back to Media Mixing
                    $mediaMixingSql = "SELECT mp.id, mp.production_code 
                                       FROM bag_filling_media bfm
                                       JOIN media_mixing_productions mp ON bfm.media_production_id = mp.id
                                       WHERE bfm.bag_filling_id = ? LIMIT 1";
                    
                    $mixingData = $this->queryOne($mediaMixingSql, [$bagFillingId]);

                    if ($mixingData) {
                        $result['media'] = [
                            'code' => $mixingData['production_code'],
                            'items' => []
                        ];

                        // Get ingredients
                        $itemsSql = "SELECT mmi.quantity, bbm.name, bbm.unit
                                     FROM media_mixing_items mmi
                                     JOIN bahan_baku_master bbm ON mmi.item_id = bbm.id
                                     WHERE mmi.production_id = ?";
                        
                        $result['media']['items'] = $this->query($itemsSql, [$mixingData['id']]);
                    }
                }
            }

            return $result;
        } catch (PDOException $e) {
            logError("getBatchTraceability Error: " . $e->getMessage());
            return $result;
        }
    }
}
