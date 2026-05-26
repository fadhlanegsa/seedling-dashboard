<?php
$db = new PDO('mysql:host=localhost;dbname=wast6986_db_bibit', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $stmt = $db->query("SELECT (SELECT COALESCE(SUM(id), 0) FROM seed_sources WHERE id = -99) as val");
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($res);
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
