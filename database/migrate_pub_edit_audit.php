<?php
/**
 * Migration Script: PUB Edit & Audit Trail
 * Adds updated_at, updated_by to PUB tables.
 * Creates pub_audit_trails and pub_edit_requests.
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting PUB Edit & Audit Trail Migration...\n\n";

    $tables = [
        'bahan_baku_transactions',
        'media_mixing_productions',
        'bag_fillings',
        'seed_sowings',
        'seedling_weanings',
        'seedling_entres',
        'seedling_mutations',
        'seedling_harvests'
    ];

    echo "1. Adding updated_at and updated_by to 8 major tables...\n";
    foreach ($tables as $table) {
        try {
            // Check if table exists
            $check = $db->query("SHOW TABLES LIKE '$table'");
            if ($check->rowCount() > 0) {
                // Add columns if they don't exist
                $cols = $db->query("SHOW COLUMNS FROM `$table`");
                $existingCols = [];
                while ($row = $cols->fetch(PDO::FETCH_ASSOC)) {
                    $existingCols[] = $row['Field'];
                }

                $alterSql = "ALTER TABLE `$table` ";
                $addCols = [];
                
                if (!in_array('updated_at', $existingCols)) {
                    $addCols[] = "ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
                }
                if (!in_array('updated_by', $existingCols)) {
                    $addCols[] = "ADD COLUMN `updated_by` INT NULL";
                }

                if (!empty($addCols)) {
                    $alterSql .= implode(", ", $addCols);
                    $db->exec($alterSql);

                    // If updated_by was added, try to add FK if users table exists in scope
                    if (!in_array('updated_by', $existingCols)) {
                        $fkSql = "ALTER TABLE `$table` ADD CONSTRAINT `fk_{$table}_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL";
                        $db->exec($fkSql);
                    }
                    echo "   - Added to $table\n";
                } else {
                    echo "   - Columns already exist in $table\n";
                }
            } else {
                echo "   - Table $table does not exist yet (skipping)\n";
            }
        } catch (PDOException $e) {
            echo "   - Error altering $table: " . $e->getMessage() . "\n";
        }
    }
    
    echo "2. Creating 'pub_audit_trails' table...\n";
    $sqlAudit = "CREATE TABLE IF NOT EXISTS pub_audit_trails (
        id INT AUTO_INCREMENT PRIMARY KEY,
        transaction_type VARCHAR(100) NOT NULL,
        record_id INT NOT NULL,
        audit_data JSON NOT NULL,
        edited_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (edited_by) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_transaction (transaction_type, record_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sqlAudit);
    echo "   Done.\n";
    
    echo "3. Creating 'pub_edit_requests' table...\n";
    $sqlRequests = "CREATE TABLE IF NOT EXISTS pub_edit_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        transaction_type VARCHAR(100) NOT NULL,
        record_id INT NOT NULL,
        nursery_id INT NULL,
        bpdas_id INT NULL,
        requested_by INT NOT NULL,
        reviewed_by INT NULL,
        reviewed_at TIMESTAMP NULL,
        reason TEXT NOT NULL,
        status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
        admin_note TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (nursery_id) REFERENCES nurseries(id) ON DELETE SET NULL,
        FOREIGN KEY (bpdas_id) REFERENCES bpdas(id) ON DELETE SET NULL,
        INDEX idx_request_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sqlRequests);
    echo "   Done.\n";

    echo "\nMigration completed successfully!\n";
    
} catch (PDOException $e) {
    die("\nMigration Failed: " . $e->getMessage() . "\n");
}
