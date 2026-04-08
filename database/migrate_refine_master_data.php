<?php
/**
 * Migration Script: Refine Master Data Table
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Refining 'bahan_baku_master' table...\n";
    
    // 1. Add category_code column
    $stmt = $db->query("SHOW COLUMNS FROM bahan_baku_master LIKE 'category_code'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE bahan_baku_master ADD COLUMN category_code VARCHAR(10) AFTER id");
        echo "   Added 'category_code' column.\n";
    }

    // 2. Add description column
    $stmt = $db->query("SHOW COLUMNS FROM bahan_baku_master LIKE 'description'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE bahan_baku_master ADD COLUMN description TEXT AFTER unit");
        echo "   Added 'description' column.\n";
    }
    
    // 3. Re-seed with proper Category Codes
    echo "Updating existing items with Category Codes...\n";
    $db->exec("UPDATE bahan_baku_master SET category_code = 'A' WHERE category = 'Benih'");
    
    // 4. Seed initial specific items based on categories
    $items = [
        ['A', 'BENIH', 'A01', 'Benih Cempaka', 'Magnolia champaca', 'kg'],
        ['A', 'BENIH', 'A02', 'Benih Eboni', 'Diospyros celebica', 'kg'],
        ['B', 'MEDIA', 'B01', 'Media Tanam', '', 'm3'],
        ['C', 'KANTONG BIBIT', 'C01', 'Polybag 10x15', '', 'kg'],
        ['C', 'KANTONG BIBIT', 'C02', 'Polybag 15x15', '', 'kg'],
        ['D', 'PUPUK', 'D01', 'Pupuk Kompos', '', 'kg'],
        ['E', 'OBAT-OBATAN', 'E01', 'Insektisida', '', 'ml'],
        ['F', 'LAIN-LAIN', 'F01', 'Plastik PE', '', 'pcs']
    ];
    
    $stmt = $db->prepare("INSERT IGNORE INTO bahan_baku_master (category_code, category, code, name, scientific_name, unit) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmt->execute($item);
    }
    echo "   Seeded default specific items.\n";
    
    echo "Database refinement completed successfully!\n";
    
} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage() . "\n");
}
