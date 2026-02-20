<?php
$_SERVER['HTTP_HOST'] = 'localhost';
require __DIR__ . '/config/config.php';
require __DIR__ . '/core/Database.php';

$db = Database::getInstance();

echo "<h2>STOCK TABLE SCHEMA</h2><pre>";
$stmt = $db->query('DESCRIBE stock');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) {
    echo $c['Field'] . ' | ' . $c['Type'] . ' | Null:' . $c['Null'] . "\n";
}
echo "</pre>";

echo "<h2>STOCK DATA (checking nursery_id)</h2><pre>";
try {
    $stmt2 = $db->query('SELECT s.id, s.bpdas_id, s.nursery_id, s.seedling_type_id, s.quantity, st.name as seedling_name FROM stock s LEFT JOIN seedling_types st ON s.seedling_type_id = st.id LIMIT 10');
    $data = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    foreach($data as $row) {
        echo "ID:{$row['id']} | BPDAS_ID:{$row['bpdas_id']} | NURSERY_ID:" . ($row['nursery_id'] ?? 'NULL') . " | {$row['seedling_name']} | Qty:{$row['quantity']}\n";
    }
    if (empty($data)) echo "No data found\n";
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Maybe nursery_id column doesn't exist!\n";
}
echo "</pre>";
