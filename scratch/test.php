<?php
$_SERVER['HTTP_HOST'] = 'localhost';
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../core/Database.php';
require __DIR__ . '/../core/Model.php';
require __DIR__ . '/../models/BahanBaku.php';

$m = new BahanBaku();
try {
    $res = $m->getSeedStockWithSource(['nursery_id' => null]);
    print_r($res);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
