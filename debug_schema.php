<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/debug_schema.php';
require 'c:/xampp/htdocs/seedling-dashboard/seedling-dashboard/config/database.php';
$db = Database::getInstance()->getConnection();

function checkTable($db, $tableName) {
    echo "\n--- Table: $tableName ---\n";
    try {
        $stmt = $db->query("DESCRIBE $tableName");
        if ($stmt) {
            print_r($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
        } else {
            echo "Failed to describe $tableName\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

checkTable($db, 'bag_fillings');
checkTable($db, 'seed_sowings');
checkTable($db, 'media_mixing_productions');
checkTable($db, 'bahan_baku_transactions');
checkTable($db, 'bahan_baku_master');
checkTable($db, 'seedling_weanings');
