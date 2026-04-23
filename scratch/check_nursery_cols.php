<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/scratch/check_nursery_cols.php';

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

$tables = [
    'bahan_baku_transactions', 
    'media_mixing_productions', 
    'bag_fillings', 
    'seed_sowings', 
    'seedling_harvests', 
    'seedling_weanings', 
    'seedling_entres', 
    'seedling_mutations'
];

foreach ($tables as $table) {
    try {
        $cols = $db->query("SHOW COLUMNS FROM $table")->fetchAll(PDO::FETCH_ASSOC);
        $hasNursery = false;
        foreach($cols as $c) {
            if ($c['Field'] === 'nursery_id') {
                $hasNursery = true;
                break;
            }
        }
        if (!$hasNursery) {
            echo "Altering $table to add nursery_id...\n";
            $db->exec("ALTER TABLE $table ADD COLUMN nursery_id INT NULL AFTER id");
        } else {
            echo "$table already has nursery_id.\n";
        }
    } catch(Exception $e) {
        echo "Error checking/altering $table: " . $e->getMessage() . "\n";
    }
}
