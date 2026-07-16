<?php
$_SERVER['HTTP_HOST'] = 'localhost';
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../core/Model.php';
require __DIR__ . '/../models/BahanBaku.php';

$m = new BahanBaku();
try {
    $res = $m->getSeedStockWithSource(['nursery_id' => null]);
    echo "getSeedStockWithSource OK! Total: " . count($res) . "\n";
    if (count($res) > 0) {
        print_r(array_slice($res, 0, 3));
    } else {
        echo "Tidak ada stok benih.\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
