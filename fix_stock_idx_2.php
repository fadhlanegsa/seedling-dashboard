<?php
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();

try {
    $db->exec("SET FOREIGN_KEY_CHECKS=0;");
    
    // Find if there is an FK dependent on unique_stock
    echo "Checking FKs...\n";
    $db->exec("ALTER TABLE stock DROP INDEX unique_stock");
    echo "Dropped unique_stock\n";
    
    $db->exec("ALTER TABLE stock ADD UNIQUE KEY unique_stock (nursery_id, seedling_type_id, program_type, source_type)");
    echo "Added new unique_stock\n";
    
    $db->exec("SET FOREIGN_KEY_CHECKS=1;");
} catch (Exception $e) {
    echo "Err: " . $e->getMessage() . "\n";
}
