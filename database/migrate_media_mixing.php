<?php
/**
 * Migration Script: Pencampuran Media Tanam
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting Media Mixing Migration...\n";
    
    // 1. Create media_mixing_productions table
    echo "1. Creating 'media_mixing_productions' table...\n";
    $sqlProductions = "CREATE TABLE IF NOT EXISTS media_mixing_productions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        production_code VARCHAR(50) NOT NULL UNIQUE,
        production_date DATE NOT NULL,
        total_production DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
        picker_name VARCHAR(255) NULL,
        foreman VARCHAR(255) NULL,
        manager VARCHAR(255) NULL,
        notes TEXT NULL,
        bpdas_id INT NULL,
        nursery_id INT NULL,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (bpdas_id) REFERENCES bpdas(id) ON DELETE SET NULL,
        FOREIGN KEY (nursery_id) REFERENCES nurseries(id) ON DELETE SET NULL,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sqlProductions);
    echo "   Done.\n";
    
    // 2. Create media_mixing_items table
    echo "2. Creating 'media_mixing_items' table...\n";
    $sqlItems = "CREATE TABLE IF NOT EXISTS media_mixing_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        production_id INT NOT NULL,
        item_id INT NOT NULL,
        quantity DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
        FOREIGN KEY (production_id) REFERENCES media_mixing_productions(id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES bahan_baku_master(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sqlItems);
    echo "   Done.\n";
    
    echo "Migration completed successfully!\n";
    
} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage() . "\n");
}
