<?php
/**
 * CSV Importer Utility
 * Imports data from CSV files (BPDAS, Stock, Seedling Types)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

class CSVImporter {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Import BPDAS data from CSV
     * CSV Format: name,province_code,address,phone,email,contact_person
     * 
     * @param string $filepath Path to CSV file
     * @return array Result with success count and errors
     */
    public function importBPDAS($filepath) {
        if (!file_exists($filepath)) {
            return ['success' => false, 'message' => 'File not found'];
        }
        
        $handle = fopen($filepath, 'r');
        if (!$handle) {
            return ['success' => false, 'message' => 'Cannot open file'];
        }
        
        $successCount = 0;
        $errors = [];
        $lineNumber = 0;
        
        // Skip header row
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== false) {
            $lineNumber++;
            
            if (count($data) < 6) {
                $errors[] = "Line $lineNumber: Insufficient columns";
                continue;
            }
            
            list($name, $provinceCode, $address, $phone, $email, $contactPerson) = $data;
            
            // Get province ID
            $stmt = $this->db->prepare("SELECT id FROM provinces WHERE code = ? LIMIT 1");
            $stmt->execute([$provinceCode]);
            $province = $stmt->fetch();
            
            if (!$province) {
                $errors[] = "Line $lineNumber: Province code '$provinceCode' not found";
                continue;
            }
            
            // Insert BPDAS
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO bpdas (name, province_id, address, phone, email, contact_person)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $name,
                    $province['id'],
                    $address,
                    $phone,
                    $email,
                    $contactPerson
                ]);
                
                $successCount++;
            } catch (PDOException $e) {
                $errors[] = "Line $lineNumber: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        
        return [
            'success' => true,
            'imported' => $successCount,
            'errors' => $errors,
            'total_lines' => $lineNumber
        ];
    }
    
    /**
     * Import Stock data from CSV
     * CSV Format: bpdas_name,seedling_name,quantity,last_update_date,notes
     * 
     * @param string $filepath Path to CSV file
     * @return array Result with success count and errors
     */
    public function importStock($filepath) {
        if (!file_exists($filepath)) {
            return ['success' => false, 'message' => 'File not found'];
        }
        
        $handle = fopen($filepath, 'r');
        if (!$handle) {
            return ['success' => false, 'message' => 'Cannot open file'];
        }
        
        $successCount = 0;
        $errors = [];
        $lineNumber = 0;
        
        // Skip header row
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== false) {
            $lineNumber++;
            
            if (count($data) < 4) {
                $errors[] = "Line $lineNumber: Insufficient columns";
                continue;
            }
            
            $bpdasName = $data[0];
            $seedlingName = $data[1];
            $quantity = $data[2];
            $lastUpdateDate = $data[3];
            $notes = $data[4] ?? null;
            
            // Get BPDAS ID
            $stmt = $this->db->prepare("SELECT id FROM bpdas WHERE name LIKE ? LIMIT 1");
            $stmt->execute(["%$bpdasName%"]);
            $bpdas = $stmt->fetch();
            
            if (!$bpdas) {
                $errors[] = "Line $lineNumber: BPDAS '$bpdasName' not found";
                continue;
            }
            
            // Get Seedling Type ID
            $stmt = $this->db->prepare("SELECT id FROM seedling_types WHERE name LIKE ? LIMIT 1");
            $stmt->execute(["%$seedlingName%"]);
            $seedlingType = $stmt->fetch();
            
            if (!$seedlingType) {
                $errors[] = "Line $lineNumber: Seedling type '$seedlingName' not found";
                continue;
            }
            
            // Insert or update stock
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO stock (bpdas_id, seedling_type_id, quantity, last_update_date, notes)
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                        quantity = VALUES(quantity),
                        last_update_date = VALUES(last_update_date),
                        notes = VALUES(notes)
                ");
                
                $stmt->execute([
                    $bpdas['id'],
                    $seedlingType['id'],
                    $quantity,
                    $lastUpdateDate,
                    $notes
                ]);
                
                $successCount++;
            } catch (PDOException $e) {
                $errors[] = "Line $lineNumber: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        
        return [
            'success' => true,
            'imported' => $successCount,
            'errors' => $errors,
            'total_lines' => $lineNumber
        ];
    }
    
    /**
     * Import Seedling Types from CSV
     * CSV Format: name,scientific_name,category,description
     * 
     * @param string $filepath Path to CSV file
     * @return array Result with success count and errors
     */
    public function importSeedlingTypes($filepath) {
        if (!file_exists($filepath)) {
            return ['success' => false, 'message' => 'File not found'];
        }
        
        $handle = fopen($filepath, 'r');
        if (!$handle) {
            return ['success' => false, 'message' => 'Cannot open file'];
        }
        
        $successCount = 0;
        $errors = [];
        $lineNumber = 0;
        
        // Skip header row
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== false) {
            $lineNumber++;
            
            if (count($data) < 3) {
                $errors[] = "Line $lineNumber: Insufficient columns";
                continue;
            }
            
            $name = $data[0];
            $scientificName = $data[1];
            $category = $data[2];
            $description = $data[3] ?? null;
            
            // Validate category
            if (!in_array($category, SEEDLING_CATEGORIES)) {
                $errors[] = "Line $lineNumber: Invalid category '$category'";
                continue;
            }
            
            // Insert seedling type
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO seedling_types (name, scientific_name, category, description)
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                        scientific_name = VALUES(scientific_name),
                        category = VALUES(category),
                        description = VALUES(description)
                ");
                
                $stmt->execute([
                    $name,
                    $scientificName,
                    $category,
                    $description
                ]);
                
                $successCount++;
            } catch (PDOException $e) {
                $errors[] = "Line $lineNumber: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        
        return [
            'success' => true,
            'imported' => $successCount,
            'errors' => $errors,
            'total_lines' => $lineNumber
        ];
    }
    
    /**
     * Export BPDAS data to CSV
     * 
     * @param string $filepath Output file path
     * @return bool
     */
    public function exportBPDAS($filepath) {
        $handle = fopen($filepath, 'w');
        if (!$handle) {
            return false;
        }
        
        // Write header
        fputcsv($handle, ['name', 'province_code', 'address', 'phone', 'email', 'contact_person']);
        
        // Get data
        $stmt = $this->db->query("
            SELECT b.name, p.code as province_code, b.address, b.phone, b.email, b.contact_person
            FROM bpdas b
            INNER JOIN provinces p ON b.province_id = p.id
            ORDER BY b.name
        ");
        
        while ($row = $stmt->fetch()) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        return true;
    }
    
    /**
     * Export Stock data to CSV
     * 
     * @param string $filepath Output file path
     * @return bool
     */
    public function exportStock($filepath) {
        $handle = fopen($filepath, 'w');
        if (!$handle) {
            return false;
        }
        
        // Write header
        fputcsv($handle, ['bpdas_name', 'seedling_name', 'quantity', 'last_update_date', 'notes']);
        
        // Get data
        $stmt = $this->db->query("
            SELECT b.name as bpdas_name, st.name as seedling_name, 
                   s.quantity, s.last_update_date, s.notes
            FROM stock s
            INNER JOIN bpdas b ON s.bpdas_id = b.id
            INNER JOIN seedling_types st ON s.seedling_type_id = st.id
            ORDER BY b.name, st.name
        ");
        
        while ($row = $stmt->fetch()) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        return true;
    }
}

// CLI Usage
if (php_sapi_name() === 'cli') {
    if ($argc < 3) {
        echo "Usage: php CSVImporter.php [import|export] [bpdas|stock|seedlings] [filepath]\n";
        echo "Examples:\n";
        echo "  php CSVImporter.php import bpdas data/bpdas.csv\n";
        echo "  php CSVImporter.php import stock data/stock.csv\n";
        echo "  php CSVImporter.php import seedlings data/seedlings.csv\n";
        echo "  php CSVImporter.php export bpdas output/bpdas.csv\n";
        exit(1);
    }
    
    $action = $argv[1];
    $type = $argv[2];
    $filepath = $argv[3];
    
    $importer = new CSVImporter();
    
    if ($action === 'import') {
        echo "Importing $type from $filepath...\n";
        
        switch ($type) {
            case 'bpdas':
                $result = $importer->importBPDAS($filepath);
                break;
            case 'stock':
                $result = $importer->importStock($filepath);
                break;
            case 'seedlings':
                $result = $importer->importSeedlingTypes($filepath);
                break;
            default:
                echo "Invalid type. Use: bpdas, stock, or seedlings\n";
                exit(1);
        }
        
        if ($result['success']) {
            echo "Import completed!\n";
            echo "Imported: {$result['imported']} / {$result['total_lines']} records\n";
            
            if (!empty($result['errors'])) {
                echo "\nErrors:\n";
                foreach ($result['errors'] as $error) {
                    echo "  - $error\n";
                }
            }
        } else {
            echo "Import failed: {$result['message']}\n";
        }
    } elseif ($action === 'export') {
        echo "Exporting $type to $filepath...\n";
        
        switch ($type) {
            case 'bpdas':
                $result = $importer->exportBPDAS($filepath);
                break;
            case 'stock':
                $result = $importer->exportStock($filepath);
                break;
            default:
                echo "Invalid type. Use: bpdas or stock\n";
                exit(1);
        }
        
        if ($result) {
            echo "Export completed successfully!\n";
        } else {
            echo "Export failed!\n";
        }
    } else {
        echo "Invalid action. Use: import or export\n";
        exit(1);
    }
}
?>
