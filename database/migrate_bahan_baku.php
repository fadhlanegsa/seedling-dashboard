<?php
/**
 * Migration Script: Penatausahaan Bibit - Bahan Baku
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting Penatausahaan Bibit Migration...\n";
    
    // 1. Create bahan_baku_master table
    echo "1. Creating 'bahan_baku_master' table...\n";
    $sqlMaster = "CREATE TABLE IF NOT EXISTS bahan_baku_master (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(20) NOT NULL UNIQUE,
        category VARCHAR(100) NOT NULL,
        name VARCHAR(255) NOT NULL,
        scientific_name VARCHAR(255) NULL,
        unit VARCHAR(50) NOT NULL DEFAULT 'kg',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sqlMaster);
    echo "   Done.\n";
    
    // 2. Create bahan_baku_transactions table
    echo "2. Creating 'bahan_baku_transactions' table...\n";
    $sqlTransactions = "CREATE TABLE IF NOT EXISTS bahan_baku_transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        transaction_id VARCHAR(50) NOT NULL UNIQUE,
        transaction_date DATE NOT NULL,
        item_id INT NOT NULL,
        quantity DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
        notes TEXT NULL,
        sender VARCHAR(255) NULL,
        receiver VARCHAR(255) NULL,
        foreman VARCHAR(255) NULL,
        manager VARCHAR(255) NULL,
        bpdas_id INT NULL,
        nursery_id INT NULL,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (item_id) REFERENCES bahan_baku_master(id) ON DELETE CASCADE,
        FOREIGN KEY (bpdas_id) REFERENCES bpdas(id) ON DELETE SET NULL,
        FOREIGN KEY (nursery_id) REFERENCES nurseries(id) ON DELETE SET NULL,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sqlTransactions);
    echo "   Done.\n";
    
    // 3. Seed initial data if table is empty
    $check = $db->query("SELECT COUNT(*) FROM bahan_baku_master")->fetchColumn();
    if ($check == 0) {
        echo "3. Seeding 'bahan_baku_master' with initial data...\n";
        $seedData = [
            ['A1', 'Benih', 'Cempaka/Manglid', 'Magnolia champaca', 'kg'],
            ['A1', 'Benih', 'Eboni', 'Diospyros celebica', 'kg'],
            ['A1', 'Benih', 'Ekaliptus / Pelita', 'Eucalyptus pellita', 'kg'],
            ['A1', 'Benih', 'Gandaria / Ramania', 'Bouea macrophylla', 'kg'],
            ['A1', 'Benih', 'Gaharu', 'Aquilaria malaccensis', 'kg']
        ];
        
        $stmt = $db->prepare("INSERT IGNORE INTO bahan_baku_master (code, category, name, scientific_name, unit) VALUES (?, ?, ?, ?, ?)");
        foreach ($seedData as $row) {
            $stmt->execute($row);
        }
        echo "   Done.\n";
    } else {
        echo "   Seed skipped (table not empty).\n";
    }
    
    echo "Migration completed successfully!\n";
    
} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage() . "\n");
}
