<?php
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();

function checkTable($db, $table) {
    echo "### SCHEMA $table ###\n";
    $stmt = $db->query("DESCRIBE $table");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "\n";
}

checkTable($db, 'stock');
checkTable($db, 'seedling_weanings');
checkTable($db, 'seedling_entres');
checkTable($db, 'seedling_types');
