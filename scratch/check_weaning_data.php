<?php
// Mock HTTP_HOST to prevent notice in config.php when running via CLI
$_SERVER['HTTP_HOST'] = 'localhost';

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance();

echo "=== SEEDLING WEANINGS DATA ===\n";
try {
    // Check if table seedling_weanings exists first by querying it
    $stmt = $db->getConnection()->query('SELECT w.*, st.name as seedling_name FROM seedling_weanings w JOIN seedling_types st ON w.result_item_id = st.id LIMIT 5');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        echo "No weaning data found.\n";
    } else {
        foreach ($rows as $r) {
            echo "ID: {$r['id']} | Code: {$r['weaning_code']} | Qty: {$r['weaned_quantity']} | Type: {$r['seedling_name']} | BPDAS: {$r['bpdas_id']} | Nursery: {$r['nursery_id']} | SeedSource: {$r['seed_source_id']}\n";
        }
    }
} catch (Exception $e) {
    echo "Error querying weaning: " . $e->getMessage() . "\n";
}

echo "\n=== SEED SOWINGS DATA ===\n";
try {
    $stmt = $db->getConnection()->query('SELECT s.* FROM seed_sowings s LIMIT 5');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        echo "No sowing data found.\n";
    } else {
        foreach ($rows as $r) {
            echo "ID: {$r['id']} | Code: {$r['sowing_code']} | Date: {$r['sowing_date']} | SeedItem: {$r['seed_item_id']}\n";
        }
    }
} catch (Exception $e) {
    echo "Error querying sowing: " . $e->getMessage() . "\n";
}
