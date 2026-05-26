<?php
$db = new PDO('mysql:host=localhost;dbname=wast6986_db_bibit', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT m.name, m.category, t.seed_source_id, t.nursery_id, t.bpdas_id, t.quantity 
        FROM bahan_baku_transactions t
        JOIN bahan_baku_master m ON t.item_id = m.id
        WHERE m.name LIKE '%Alpukat%' OR m.name LIKE '%Aren%'";

try {
    $stmt = $db->query($sql);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($res);
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
