<?php
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();

try {
    echo "--- Fixing bpdas_id Null Constraints ---\n";
    $db->exec("ALTER TABLE seedling_mutations MODIFY bpdas_id INT(11) NULL");
    echo "Modified bpdas_id to be NULLable in seedling_mutations\n";

    $db->exec("ALTER TABLE stock MODIFY bpdas_id INT(11) NULL");
    echo "Modified bpdas_id to be NULLable in stock\n";

    $db->exec("ALTER TABLE seedling_mutations MODIFY nursery_id INT(11) NULL");
    echo "Modified nursery_id to be NULLable in seedling_mutations\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
