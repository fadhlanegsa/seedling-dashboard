<?php
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();

try {
    echo "--- Updating Database for Mutation Fixes ---\n";
    
    // 1. Allow origin/target location to be null in mutations just in case
    $db->exec("ALTER TABLE seedling_mutations MODIFY origin_location varchar(255) NULL");
    $db->exec("ALTER TABLE seedling_mutations MODIFY target_location varchar(255) NULL");
    echo "Modified locations to be NULLable in seedling_mutations\n";

    // 2. Add 'bibitgratis' to program_type enum in stock table
    $db->exec("ALTER TABLE stock MODIFY program_type enum('Reguler', 'FOLU', 'bibitgratis') NOT NULL DEFAULT 'Reguler'");
    echo "Added 'bibitgratis' to stock program_type enum\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
