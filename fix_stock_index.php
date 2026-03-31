<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Fixing unique_stock index...\n";
    
    // Debug existing indexes
    $stmt = $db->query("SHOW INDEX FROM stock");
    $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $uniqueStockExists = false;
    foreach ($indexes as $index) {
        if ($index['Key_name'] === 'unique_stock') {
            $uniqueStockExists = true;
            break;
        }
    }
    
    if ($uniqueStockExists) {
        $db->exec("ALTER TABLE stock DROP INDEX unique_stock");
        echo "Dropped old unique_stock index.\n";
    }

    // Add the new index
    $db->exec("ALTER TABLE stock ADD UNIQUE KEY unique_stock (nursery_id, seedling_type_id)");
    echo "Added new unique_stock index (nursery_id, seedling_type_id).\n";

    echo "Fix applied successfully.\n";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
