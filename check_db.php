<?php
require 'config/config.php';
require 'core/Database.php';

$db = Database::getInstance();

echo "=== STOCK TABLE SCHEMA ===\n";
$stmt = $db->query('DESCRIBE stock');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) {
    echo $c['Field'] . ' | ' . $c['Type'] . ' | ' . $c['Null'] . "\n";
}

echo "\n=== STOCK DATA WITH NURSERY ===\n";
$stmt2 = $db->query('SELECT s.id, s.bpdas_id, s.nursery_id, s.seedling_type_id, s.quantity, st.name as seedling_name, n.name as nursery_name FROM stock s LEFT JOIN seedling_types st ON s.seedling_type_id = st.id LEFT JOIN nurseries n ON s.nursery_id = n.id LIMIT 10');
$data = $stmt2->fetchAll(PDO::FETCH_ASSOC);
foreach($data as $row) {
    echo "ID:{$row['id']} | BPDAS:{$row['bpdas_id']} | Nursery:{$row['nursery_id']} | NurseryName:" . ($row['nursery_name'] ?? 'NULL') . " | {$row['seedling_name']} | Qty:{$row['quantity']}\n";
}
