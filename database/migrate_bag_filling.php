<?php
/**
 * Migration Script: Pengisian Kantong Bibit
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting Bag Filling Migration...\n";
    
    // 1. Create bag_fillings table
    echo "1. Creating 'bag_fillings' table...\n";
    $sqlFillings = "CREATE TABLE IF NOT EXISTS bag_fillings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filling_code VARCHAR(50) NOT NULL UNIQUE,
        filling_date DATE NOT NULL,
        bag_item_id INT NOT NULL,
        bag_quantity DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
        total_production DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
        mandor VARCHAR(255) NULL,
        manager VARCHAR(255) NULL,
        notes TEXT NULL,
        bpdas_id INT NULL,
        nursery_id INT NULL,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (bag_item_id) REFERENCES bahan_baku_master(id) ON DELETE RESTRICT,
        FOREIGN KEY (bpdas_id) REFERENCES bpdas(id) ON DELETE SET NULL,
        FOREIGN KEY (nursery_id) REFERENCES nurseries(id) ON DELETE SET NULL,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sqlFillings);
    echo "   Done.\n";
    
    // 2. Create bag_filling_media table (Ingredient links)
    echo "2. Creating 'bag_filling_media' table...\n";
    $sqlMedia = "CREATE TABLE IF NOT EXISTS bag_filling_media (
        id INT AUTO_INCREMENT PRIMARY KEY,
        bag_filling_id INT NOT NULL,
        media_production_id INT NOT NULL,
        quantity DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
        FOREIGN KEY (bag_filling_id) REFERENCES bag_fillings(id) ON DELETE CASCADE,
        FOREIGN KEY (media_production_id) REFERENCES media_mixing_productions(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sqlMedia);
    echo "   Done.\n";
    
    echo "Migration completed successfully!\n";
    
} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage() . "\n");
}
