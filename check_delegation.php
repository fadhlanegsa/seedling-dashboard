<?php
$host = 'localhost';
$dbname = 'wast6986_db_bibit';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $stmt = $db->query("SELECT id, name, can_operator_approve FROM bpdas");
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($res);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
