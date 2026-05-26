<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';
$_SERVER['SCRIPT_NAME'] = '/index.php';

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'wast6986_db_bibit');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $db = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

function checkTable($db, $tableName) {
    echo "\n--- Table: $tableName ---\n";
    try {
        $stmt = $db->query("DESCRIBE $tableName");
        if ($stmt) {
            print_r($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
        } else {
            echo "Failed to describe $tableName\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

checkTable($db, 'bag_fillings');
checkTable($db, 'seed_sowings');
checkTable($db, 'media_mixing_productions');
checkTable($db, 'bahan_baku_transactions');
checkTable($db, 'bahan_baku_master');
checkTable($db, 'media_mixing_items');
