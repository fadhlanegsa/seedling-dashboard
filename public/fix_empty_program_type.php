<?php
/**
 * Fix Empty/NULL Program Type in Stock Table
 * Merges stock quantity to 'Reguler' program type if a record already exists,
 * or updates it to 'Reguler' if it doesn't.
 */
define('APP_PATH', dirname(__DIR__));
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Simple security check (only allow admin/localhost or custom token, or simply render status)
// We will print information safely.

$db = Database::getInstance()->getConnection();

echo "<h1>Fixing Empty/NULL program_type values in stock table</h1>";
echo "<pre>";

try {
    // 1. Find all stock records with empty or null program_type
    $stmt = $db->query("SELECT s.*, n.name as nursery_name, st.name as seedling_name 
                        FROM stock s 
                        LEFT JOIN nurseries n ON s.nursery_id = n.id 
                        LEFT JOIN seedling_types st ON s.seedling_type_id = st.id
                        WHERE s.program_type IS NULL OR s.program_type = ''");
    $invalidRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($invalidRows) . " records with empty/NULL program_type.\n\n";

    $mergedCount = 0;
    $updatedCount = 0;
    $deletedCount = 0;

    foreach ($invalidRows as $row) {
        $nurseryId = $row['nursery_id'];
        $seedlingTypeId = $row['seedling_type_id'];
        $sourceType = $row['source_type'];
        $quantity = (int)$row['quantity'];
        $notes = $row['notes'];
        
        echo "Processing Stock ID {$row['id']}: Nursery: {$row['nursery_name']} (ID: $nurseryId), Seedling: {$row['seedling_name']} (ID: $seedlingTypeId), Qty: $quantity, Source: " . ($sourceType ?? 'NULL') . "\n";

        // Check if there is an existing record with program_type = 'Reguler' for the same unique key
        if ($nurseryId !== null) {
            $checkStmt = $db->prepare("SELECT * FROM stock 
                                       WHERE nursery_id = ? 
                                         AND seedling_type_id = ? 
                                         AND program_type = 'Reguler' 
                                         AND (source_type = ? OR (source_type IS NULL AND ? IS NULL))
                                       LIMIT 1");
            $checkStmt->execute([$nurseryId, $seedlingTypeId, $sourceType, $sourceType]);
        } else {
            $checkStmt = $db->prepare("SELECT * FROM stock 
                                       WHERE bpdas_id = ? 
                                         AND nursery_id IS NULL
                                         AND seedling_type_id = ? 
                                         AND program_type = 'Reguler' 
                                         AND (source_type = ? OR (source_type IS NULL AND ? IS NULL))
                                       LIMIT 1");
            $checkStmt->execute([$row['bpdas_id'], $seedlingTypeId, $sourceType, $sourceType]);
        }
        
        $existingReguler = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingReguler) {
            // Merge quantity and notes
            $newQuantity = $existingReguler['quantity'] + $quantity;
            $newNotes = $existingReguler['notes'];
            if (!empty($notes)) {
                $newNotes = empty($newNotes) ? $notes : $newNotes . " | " . $notes;
            }
            if (strlen($newNotes) > 255) {
                $newNotes = substr($newNotes, 0, 252) . "...";
            }

            // Update existing reguler record
            $updateStmt = $db->prepare("UPDATE stock SET quantity = ?, notes = ?, last_update_date = CURDATE() WHERE id = ?");
            $updateStmt->execute([$newQuantity, $newNotes, $existingReguler['id']]);

            // Delete the invalid record
            $deleteStmt = $db->prepare("DELETE FROM stock WHERE id = ?");
            $deleteStmt->execute([$row['id']]);

            echo "  -> [MERGED] Quantity added to existing Reguler record (Stock ID: {$existingReguler['id']}). New quantity: $newQuantity. Deleted ID: {$row['id']}.\n";
            $mergedCount++;
            $deletedCount++;
        } else {
            // Simply update program_type to 'Reguler'
            $updateStmt = $db->prepare("UPDATE stock SET program_type = 'Reguler', last_update_date = CURDATE() WHERE id = ?");
            $updateStmt->execute([$row['id']]);

            echo "  -> [UPDATED] program_type updated to 'Reguler' directly.\n";
            $updatedCount++;
        }
    }

    echo "\n--- Summary ---\n";
    echo "Total records processed: " . count($invalidRows) . "\n";
    echo "Records merged & deleted: $mergedCount\n";
    echo "Records updated directly: $updatedCount\n";
    echo "Done!\n";

} catch (Exception $e) {
    echo "\nError: " . $e->getMessage() . "\n";
}

echo "</pre>";
