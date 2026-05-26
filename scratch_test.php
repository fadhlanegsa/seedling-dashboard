<?php
require 'c:/xampp/htdocs/seedling-dashboard/seedling-dashboard/config/database.php';
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$stmt = $db->query('SELECT * FROM seed_sowings ORDER BY id DESC LIMIT 5');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt2 = $db->query("SELECT * FROM bahan_baku_transactions WHERE item_id = (SELECT id FROM bahan_baku_master WHERE name = 'Benih Aren') ORDER BY id DESC LIMIT 5");
echo "\nTransactions:\n";
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));

$stmt3 = $db->query("SELECT * FROM bahan_baku_master WHERE name = 'Benih Aren'");
echo "\nMaster:\n";
print_r($stmt3->fetchAll(PDO::FETCH_ASSOC));
