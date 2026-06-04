<?php
/**
 * API Trace Controller
 * Handles trace requests and batch verification for individual seedling Smart Barcodes.
 * Public endpoint (no API Key required) to support easy 3rd party integration.
 */

require_once CORE_PATH . 'ApiController.php';
require_once UTILS_PATH . 'BarcodeHelper.php';

class ApiTraceController extends ApiController {
    
    public function __construct() {
        parent::__construct();
        // Set content type to JSON
        header('Content-Type: application/json');
    }
    
    /**
     * Endpoint: GET /api/trace/{code}
     * Menerima kode seperti: PE-45-3-12-7-42-260415-88
     */
    public function show($code = '') {
        if (empty($code)) {
            $this->json(['success' => false, 'message' => 'Kode barcode tidak boleh kosong'], 400);
        }
        
        $parseResult = BarcodeHelper::parse($code);
        if (!$parseResult) {
            $this->json(['success' => false, 'message' => 'Format Smart Barcode tidak valid'], 400);
        }
        
        $db = Database::getInstance()->getConnection();
        
        $type = $parseResult['type'];
        $batchId = $parseResult['batch_id'];
        $bpdasId = $parseResult['bpdas_id'];
        $nurseryId = $parseResult['nursery_id'];
        $seedSourceId = $parseResult['seed_source_id'];
        $seedlingTypeId = $parseResult['seedling_type_id'];
        $sowingDateBarcode = $parseResult['sowing_date']; // YYYY-MM-DD
        $index = $parseResult['index'];
        
        try {
            // 1. Fetch Batch Record
            if ($type === 'PE') {
                $sqlBatch = "SELECT w.*, st.name as seedling_name, st.scientific_name,
                                    n.name as nursery_name, b.name as bpdas_name
                             FROM seedling_weanings w
                             JOIN seedling_types st ON w.result_item_id = st.id
                             LEFT JOIN nurseries n ON w.nursery_id = n.id
                             LEFT JOIN bpdas b ON w.bpdas_id = b.id
                             WHERE w.id = ?";
            } else {
                $sqlBatch = "SELECT e.*, st.name as seedling_name, st.scientific_name,
                                    n.name as nursery_name, b.name as bpdas_name
                             FROM seedling_entres e
                             JOIN seedling_types st ON e.result_item_id = st.id
                             LEFT JOIN nurseries n ON e.nursery_id = n.id
                             LEFT JOIN bpdas b ON e.bpdas_id = b.id
                             WHERE e.id = ?";
            }
            
            $stmt = $db->prepare($sqlBatch);
            $stmt->execute([$batchId]);
            $batch = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$batch) {
                $this->json([
                    'success' => true,
                    'valid' => false,
                    'message' => 'Gagal verifikasi: Batch bibit tidak terdaftar di sistem.'
                ], 200);
            }
            
            // 2. Fetch sowing date to cross check
            if ($type === 'PE') {
                $sqlSowing = "SELECT s.sowing_date 
                              FROM seedling_weanings w
                              LEFT JOIN seedling_harvests h ON w.harvest_id = h.id
                              LEFT JOIN seed_sowings s ON h.sowing_id = s.id
                              WHERE w.id = ? LIMIT 1";
            } else {
                $sqlSowing = "SELECT s.sowing_date 
                              FROM seedling_entres e
                              LEFT JOIN seedling_harvests h ON e.harvest_id = h.id
                              LEFT JOIN seed_sowings s ON h.sowing_id = s.id
                              WHERE e.id = ? LIMIT 1";
            }
            
            $stmtSowing = $db->prepare($sqlSowing);
            $stmtSowing->execute([$batchId]);
            $sowingRow = $stmtSowing->fetch(PDO::FETCH_ASSOC);
            $dbSowingDate = $sowingRow ? $sowingRow['sowing_date'] : null;
            
            // 3. Perform Cross-Verification
            $isVerified = true;
            $mismatchFields = [];
            
            if ((int)$batch['bpdas_id'] !== $bpdasId) {
                $isVerified = false;
                $mismatchFields[] = 'BPDAS ID';
            }
            if ((int)$batch['nursery_id'] !== $nurseryId) {
                $isVerified = false;
                $mismatchFields[] = 'Persemaian ID';
            }
            if ((int)$batch['seed_source_id'] !== $seedSourceId) {
                $isVerified = false;
                $mismatchFields[] = 'Sumber Benih ID';
            }
            if ((int)$batch['result_item_id'] !== $seedlingTypeId) {
                $isVerified = false;
                $mismatchFields[] = 'Jenis Bibit ID';
            }
            
            // Check index range limit
            $maxQty = $type === 'PE' ? (int)$batch['weaned_quantity'] : (int)$batch['used_quantity'];
            if ($index < 1 || $index > $maxQty) {
                $isVerified = false;
                $mismatchFields[] = "Nomor Seri Bibit (Indeks {$index} berada di luar kapasitas batch yaitu {$maxQty} batang)";
            }
            
            // Compare sowing dates (formatted)
            if ($dbSowingDate) {
                $dbDateFormatted = date('ymd', strtotime($dbSowingDate));
                if ($dbDateFormatted !== $parseResult['sowing_date_raw']) {
                    $isVerified = false;
                    $mismatchFields[] = 'Tanggal Tabur';
                }
            } else {
                if ($parseResult['sowing_date_raw'] !== '000000') {
                    $isVerified = false;
                    $mismatchFields[] = 'Data Penaburan Induk';
                }
            }
            
            if (!$isVerified) {
                $this->json([
                    'success' => true,
                    'valid' => false,
                    'message' => 'Verifikasi silang gagal. Komponen barcode tidak cocok dengan record database asli.',
                    'details' => [
                        'mismatches' => $mismatchFields,
                        'reason' => 'Ada kemungkinan barcode ini telah dimanipulasi atau disalin secara ilegal.'
                    ]
                ], 200);
            }
            
            // 4. Retrieve Full Traceability Data
            $mutationModel = $this->model('SeedlingMutation');
            $traceData = $mutationModel->getBatchTraceability($type, $batchId);
            
            // Construct premium json structure
            $response = [
                'success' => true,
                'valid' => true,
                'message' => 'Verifikasi berhasil. Data bibit individual sah dan terdaftar.',
                'seedling_details' => [
                    'unique_code' => $code,
                    'prefix' => $type,
                    'serial_number' => $index,
                    'total_batch_quantity' => $maxQty,
                    'seedling_name' => $batch['seedling_name'],
                    'scientific_name' => $batch['scientific_name'] ?: '-',
                    'nursery_name' => $batch['nursery_name'] ?: 'Persemaian Default',
                    'bpdas_name' => $batch['bpdas_name'] ?: 'BPDAS Default',
                    'nursery_block_location' => $batch['location'] ?: 'Belum ditentukan',
                    'mandor' => $batch['mandor'] ?: '-',
                    'manager' => $batch['manager'] ?: '-',
                    'notes' => $batch['notes'] ?: '-'
                ],
                'traceability' => [
                    'seed_source' => $traceData['seed_source'] ? [
                        'name' => $traceData['seed_source']['name'],
                        'location' => $traceData['seed_source']['kabupaten'] ?: '-',
                        'certificate_number' => $traceData['seed_source']['sertifikat'] ?: '-',
                        'vendor_owner' => $traceData['seed_source']['vendor'] ?: '-'
                    ] : null,
                    'sowing' => $traceData['sowing'] ? [
                        'sowing_code' => $traceData['sowing']['code'],
                        'sowing_date' => $traceData['sowing']['date'],
                        'seed_name' => $traceData['sowing']['seed_name'],
                        'seed_quantity' => (int)$traceData['sowing']['seed_quantity'],
                        'seed_unit' => $traceData['sowing']['seed_unit']
                    ] : null,
                    'weaning_or_entres' => $traceData['weaning'] ? [
                        'batch_code' => $traceData['weaning']['code'],
                        'date' => $traceData['weaning']['date'],
                        'nursery_location' => $traceData['weaning']['location'] ?: '-',
                        'batch_quantity' => (int)$traceData['weaning']['quantity']
                    ] : null,
                    'media_mixing' => $traceData['media'] ? [
                        'mixing_code' => $traceData['media']['code'],
                        'composition' => array_map(function($m) {
                            return [
                                'ingredient_name' => $m['name'],
                                'quantity' => (float)$m['quantity'],
                                'unit' => $m['unit']
                            ];
                        }, $traceData['media']['items'])
                    ] : null
                ]
            ];
            
            $this->json($response, 200);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Endpoint: POST /api/trace/verify
     * Menerima payload JSON: { "codes": ["PE-45-...", "PE-45-...", "ET-12-..."] }
     */
    public function verify() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method Not Allowed. Use POST.'], 405);
        }
        
        $body = $this->getJsonBody();
        $codes = isset($body['codes']) ? $body['codes'] : null;
        
        if (!is_array($codes)) {
            $this->json(['success' => false, 'message' => 'Payload JSON valid dengan array "codes" diperlukan'], 400);
        }
        
        $results = [];
        $db = Database::getInstance()->getConnection();
        
        foreach ($codes as $code) {
            $code = trim($code);
            if (empty($code)) {
                $results[] = [
                    'code' => $code,
                    'valid' => false,
                    'message' => 'Kode kosong'
                ];
                continue;
            }
            
            $parseResult = BarcodeHelper::parse($code);
            if (!$parseResult) {
                $results[] = [
                    'code' => $code,
                    'valid' => false,
                    'message' => 'Format Smart Barcode tidak valid'
                ];
                continue;
            }
            
            $type = $parseResult['type'];
            $batchId = $parseResult['batch_id'];
            $bpdasId = $parseResult['bpdas_id'];
            $nurseryId = $parseResult['nursery_id'];
            $seedSourceId = $parseResult['seed_source_id'];
            $seedlingTypeId = $parseResult['seedling_type_id'];
            $sowingDateBarcode = $parseResult['sowing_date'];
            $index = $parseResult['index'];
            
            try {
                // Fetch Batch
                if ($type === 'PE') {
                    $sql = "SELECT w.bpdas_id, w.nursery_id, w.seed_source_id, w.result_item_id, w.weaned_quantity,
                                   st.name as seedling_name
                            FROM seedling_weanings w
                            JOIN seedling_types st ON w.result_item_id = st.id
                            WHERE w.id = ?";
                } else {
                    $sql = "SELECT e.bpdas_id, e.nursery_id, e.seed_source_id, e.result_item_id, e.used_quantity,
                                   st.name as seedling_name
                            FROM seedling_entres e
                            JOIN seedling_types st ON e.result_item_id = st.id
                            WHERE e.id = ?";
                }
                
                $stmt = $db->prepare($sql);
                $stmt->execute([$batchId]);
                $batch = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$batch) {
                    $results[] = [
                        'code' => $code,
                        'valid' => false,
                        'message' => 'Batch tidak terdaftar'
                    ];
                    continue;
                }
                
                // Fetch Sowing Date
                if ($type === 'PE') {
                    $sqlSowing = "SELECT s.sowing_date 
                                  FROM seedling_weanings w
                                  LEFT JOIN seedling_harvests h ON w.harvest_id = h.id
                                  LEFT JOIN seed_sowings s ON h.sowing_id = s.id
                                  WHERE w.id = ? LIMIT 1";
                } else {
                    $sqlSowing = "SELECT s.sowing_date 
                                  FROM seedling_entres e
                                  LEFT JOIN seedling_harvests h ON e.harvest_id = h.id
                                  LEFT JOIN seed_sowings s ON h.sowing_id = s.id
                                  WHERE e.id = ? LIMIT 1";
                }
                $stmtSowing = $db->prepare($sqlSowing);
                $stmtSowing->execute([$batchId]);
                $sowingRow = $stmtSowing->fetch(PDO::FETCH_ASSOC);
                $dbSowingDate = $sowingRow ? $sowingRow['sowing_date'] : null;
                
                // Verification
                $isValid = true;
                $reason = '';
                
                if ((int)$batch['bpdas_id'] !== $bpdasId) {
                    $isValid = false;
                    $reason .= 'BPDAS ID mismatch. ';
                }
                if ((int)$batch['nursery_id'] !== $nurseryId) {
                    $isValid = false;
                    $reason .= 'Persemaian ID mismatch. ';
                }
                if ((int)$batch['seed_source_id'] !== $seedSourceId) {
                    $isValid = false;
                    $reason .= 'Sumber Benih ID mismatch. ';
                }
                if ((int)$batch['result_item_id'] !== $seedlingTypeId) {
                    $isValid = false;
                    $reason .= 'Jenis Bibit ID mismatch. ';
                }
                
                $maxQty = $type === 'PE' ? (int)$batch['weaned_quantity'] : (int)$batch['used_quantity'];
                if ($index < 1 || $index > $maxQty) {
                    $isValid = false;
                    $reason .= "Nomor seri out of range (1-{$maxQty}). ";
                }
                
                if ($dbSowingDate) {
                    $dbDateFormatted = date('ymd', strtotime($dbSowingDate));
                    if ($dbDateFormatted !== $parseResult['sowing_date_raw']) {
                        $isValid = false;
                        $reason .= 'Tanggal Tabur mismatch. ';
                    }
                } else {
                    if ($parseResult['sowing_date_raw'] !== '000000') {
                        $isValid = false;
                        $reason .= 'Data Penaburan induk missing. ';
                    }
                }
                
                if ($isValid) {
                    $results[] = [
                        'code' => $code,
                        'valid' => true,
                        'message' => 'Verifikasi sukses',
                        'seedling_name' => $batch['seedling_name'],
                        'serial_number' => $index,
                        'total_batch_quantity' => $maxQty
                    ];
                } else {
                    $results[] = [
                        'code' => $code,
                        'valid' => false,
                        'message' => 'Verifikasi silang gagal: ' . trim($reason)
                    ];
                }
                
            } catch (Exception $e) {
                $results[] = [
                    'code' => $code,
                    'valid' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ];
            }
        }
        
        $this->json([
            'success' => true,
            'results' => $results
        ], 200);
    }
}
