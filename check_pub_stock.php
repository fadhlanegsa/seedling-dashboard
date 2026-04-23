<?php
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT id, seedling_type_id, program_type, source_type, notes FROM stock WHERE notes LIKE '%penatausahaan%' OR source_type = 'automated_pub'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
