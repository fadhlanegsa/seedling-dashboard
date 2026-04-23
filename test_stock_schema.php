<?php
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE stock");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
