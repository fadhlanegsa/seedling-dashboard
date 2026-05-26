<?php
$db = new PDO('mysql:host=localhost;dbname=wast6986_db_bibit', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT m.name, t.nursery_id, m.category FROM bahan_baku_master m LEFT JOIN bahan_baku_transactions t ON t.item_id = m.id WHERE m.category = 'BENIH'";

try {
    $stmt = $db->query($sql);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($res);
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
