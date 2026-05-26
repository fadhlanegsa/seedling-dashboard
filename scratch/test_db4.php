<?php
$db = new PDO('mysql:host=localhost;dbname=wast6986_db_bibit', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT id, name, category, code FROM bahan_baku_master WHERE name LIKE '%Alpukat%' OR name LIKE '%Aren%' OR name LIKE '%Durian%' OR name LIKE '%Gloh%'";

try {
    $stmt = $db->query($sql);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($res);
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
