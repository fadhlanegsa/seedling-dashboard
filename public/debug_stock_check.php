<?php
define('APP_PATH', dirname(__DIR__));
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

$requestId = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 362;
$nurseryId = isset($_GET['nursery_id']) ? (int)$_GET['nursery_id'] : null;

echo "<h2>Debug Stock Check for Request ID: $requestId</h2>";

// 1. Get request details
$stmt = $db->prepare("SELECT r.*, n.name as nursery_name FROM requests r LEFT JOIN nurseries n ON r.nursery_id = n.id WHERE r.id = ?");
$stmt->execute([$requestId]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    die("Request ID $requestId not found.");
}

echo "<h3>Request Details:</h3>";
echo "<pre>";
print_r($request);
echo "</pre>";

if (empty($nurseryId)) {
    $nurseryId = $request['nursery_id'];
    echo "<p>Using nursery_id from Request: <strong>" . ($nurseryId ?? 'NULL') . "</strong></p>";
} else {
    echo "<p>Using nursery_id from GET parameter: <strong>$nurseryId</strong></p>";
}

if (empty($nurseryId)) {
    echo "<p style='color: orange;'>⚠️ Nursery ID is not determined in the request. Please pass nursery_id in URL, e.g., ?request_id=$requestId&nursery_id=YOUR_NURSERY_ID</p>";
}

// 1.5. List all nurseries under this BPDAS to help identify IDs
$stmt = $db->prepare("SELECT id, name FROM nurseries WHERE bpdas_id = ?");
$stmt->execute([$request['bpdas_id']]);
$nurseriesUnderBPDAS = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<h3>Nurseries under BPDAS (ID: {$request['bpdas_id']}):</h3>";
echo "<ul>";
foreach ($nurseriesUnderBPDAS as $n) {
    echo "<li><strong>ID: {$n['id']}</strong> - {$n['name']}</li>";
}
echo "</ul>";

if (!empty($nurseryId)) {
    // 2. Get nursery details
    $stmt = $db->prepare("SELECT * FROM nurseries WHERE id = ?");
    $stmt->execute([$nurseryId]);
    $nursery = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h3>Selected Nursery:</h3>";
    echo "<pre>";
    print_r($nursery);
    echo "</pre>";
}

// 3. Get request items
$stmt = $db->prepare("SELECT ri.*, st.name as seedling_name FROM request_items ri JOIN seedling_types st ON ri.seedling_type_id = st.id WHERE ri.request_id = ?");
$stmt->execute([$requestId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Request Items:</h3>";
echo "<pre>";
print_r($items);
echo "</pre>";

// 4. Check stock for each item
if (!empty($nurseryId)) {
    echo "<h3>Stock Check Simulation:</h3>";
    foreach ($items as $item) {
        $programType = $item['program_type'] ?? 'Reguler';
        echo "Checking stock for item: <strong>{$item['seedling_name']}</strong> (ID: {$item['seedling_type_id']}), Program: <strong>$programType</strong>, Required: <strong>{$item['quantity']}</strong><br>";
        
        $stmt = $db->prepare("SELECT * FROM stock WHERE nursery_id = ? AND seedling_type_id = ? AND program_type = ?");
        $stmt->execute([$nurseryId, $item['seedling_type_id'], $programType]);
        $stock = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($stock) {
            echo "Found Stock Record: ID: {$stock['id']}, Quantity: <strong>{$stock['quantity']}</strong>, Program: {$stock['program_type']}<br>";
            if ($stock['quantity'] < $item['quantity']) {
                echo "<span style='color: red;'>❌ FAILED: Stock quantity is less than required!</span><br><br>";
            } else {
                echo "<span style='color: green;'>✓ SUCCESS: Stock is sufficient.</span><br><br>";
            }
        } else {
            echo "<span style='color: red;'>❌ FAILED: Stock record not found for nursery_id = $nurseryId, seedling_type_id = {$item['seedling_type_id']}, program_type = '$programType'.</span><br><br>";
            
            // Find other stocks for this seedling type in the nursery to help debug
            $stmt = $db->prepare("SELECT s.*, n.name as nursery_name FROM stock s LEFT JOIN nurseries n ON s.nursery_id = n.id WHERE s.seedling_type_id = ?");
            $stmt->execute([$item['seedling_type_id']]);
            $allStocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "Other stock records in database for this seedling type (any nursery/program):<br>";
            echo "<pre>";
            print_r($allStocks);
            echo "</pre>";
        }
    }
}
