<?php
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();

try {
    echo "--- Fixing stock indices ---\n";
    $db->exec("ALTER TABLE stock DROP INDEX unique_nursery_stock");
    echo "Dropped unique_nursery_stock\n";
} catch (Exception $e) {
    echo "unique_nursery_stock already dropped or err: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE stock DROP INDEX unique_stock");
    echo "Dropped unique_stock\n";
} catch (Exception $e) {
    echo "unique_stock already dropped or err: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE stock ADD UNIQUE KEY unique_stock (nursery_id, seedling_type_id, program_type, source_type)");
    echo "Added new unique_stock\n";
} catch (Exception $e) {
    echo "Err adding new unique_stock: " . $e->getMessage() . "\n";
}
