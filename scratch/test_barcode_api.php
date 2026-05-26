<?php
$_SERVER['HTTP_HOST'] = 'localhost';

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/BarcodeHelper.php';

$db = Database::getInstance()->getConnection();

echo "=== DETAILED WEANING DATA FOR ID 12 ===\n";
$stmt = $db->prepare("
    SELECT w.*, st.name as seedling_name, st.id as seedling_type_id,
           s.sowing_date
    FROM seedling_weanings w
    JOIN seedling_types st ON w.result_item_id = st.id
    LEFT JOIN seedling_harvests h ON w.harvest_id = h.id
    LEFT JOIN seed_sowings s ON h.sowing_id = s.id
    WHERE w.id = ?
");
$stmt->execute([12]);
$weaning = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$weaning) {
    echo "Weaning ID 12 not found!\n";
    exit;
}

echo "Weaning ID: " . $weaning['id'] . "\n";
echo "Weaning Code: " . $weaning['weaning_code'] . "\n";
echo "BPDAS ID: " . $weaning['bpdas_id'] . "\n";
echo "Nursery ID: " . $weaning['nursery_id'] . "\n";
echo "Seed Source ID: " . $weaning['seed_source_id'] . "\n";
echo "Seedling Type ID: " . $weaning['seedling_type_id'] . " (" . $weaning['seedling_name'] . ")\n";
echo "Sowing Date: " . $weaning['sowing_date'] . "\n";
echo "Weaned Quantity: " . $weaning['weaned_quantity'] . "\n";

// Generate Smart Barcode for index 88
$smartBarcode = BarcodeHelper::generate(
    'PE',
    $weaning['id'],
    $weaning['bpdas_id'],
    $weaning['nursery_id'],
    $weaning['seed_source_id'],
    $weaning['seedling_type_id'],
    $weaning['sowing_date'],
    88
);

echo "\nGenerated Smart Barcode: " . $smartBarcode . "\n";

// Parse it back to verify
$parsed = BarcodeHelper::parse($smartBarcode);
echo "\n=== PARSED BARCODE ===\n";
print_r($parsed);

// Let's test the verification logic internally as well!
echo "\n=== VERIFYING BARCODE AGAINST DB ===\n";
$type = $parsed['type'];
$batchId = $parsed['batch_id'];
$bpdasId = $parsed['bpdas_id'];
$nurseryId = $parsed['nursery_id'];
$seedSourceId = $parsed['seed_source_id'];
$seedlingTypeId = $parsed['seedling_type_id'];
$sowingDateBarcode = $parsed['sowing_date'];
$index = $parsed['index'];

$isValid = true;
$mismatch = [];

if ((int)$weaning['bpdas_id'] !== $bpdasId) { $isValid = false; $mismatch[] = 'BPDAS ID'; }
if ((int)$weaning['nursery_id'] !== $nurseryId) { $isValid = false; $mismatch[] = 'Nursery ID'; }
if ((int)$weaning['seed_source_id'] !== $seedSourceId) { $isValid = false; $mismatch[] = 'Seed Source ID'; }
if ((int)$weaning['result_item_id'] !== $seedlingTypeId) { $isValid = false; $mismatch[] = 'Seedling Type ID'; }

$maxQty = (int)$weaning['weaned_quantity'];
if ($index < 1 || $index > $maxQty) {
    $isValid = false;
    $mismatch[] = 'Serial Number Out of Range';
}

if ($weaning['sowing_date']) {
    $dbDateFormatted = date('ymd', strtotime($weaning['sowing_date']));
    if ($dbDateFormatted !== $parsed['sowing_date_raw']) {
        $isValid = false;
        $mismatch[] = 'Sowing Date';
    }
}

if ($isValid) {
    echo "SUCCESS: Barcode is 100% Valid and verified!\n";
} else {
    echo "FAILED: Verification errors: " . implode(', ', $mismatch) . "\n";
}
