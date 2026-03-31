<?php
require_once __DIR__ . '/config/config.php';
require_once CORE_PATH . 'Model.php';
require_once MODELS_PATH . 'Stock.php';

$stockModel = new Stock();

echo "Testing Upsert API Logic...\n";
echo "BPDAS ID: 24 (Unda Anyar)\n";
echo "Nursery ID: 24\n";
echo "Seedling Type ID: 1\n";

$result = $stockModel->upsertApiStock(
    24, // BPDAS
    24, // Nursery
    1,  // Seedling Type ID (As per user's Postman)
    500, // Quantity
    "Test Debug",
    "2026-03-11 13:45:00"
);

echo "Upsert Result: " . ($result ? "Success" : "Failed") . "\n";

// Verify in database
$verify = $stockModel->findByNurseryAndSeedling(24, 1);
if ($verify) {
    echo "Found Record:\n";
    print_r($verify);
} else {
    echo "Record NOT FOUND after upsert!\n";
}
