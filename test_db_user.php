<?php
// Script to check DB
error_reporting(E_ALL); ini_set('display_errors', 1);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Database.php';
\ = Database::getInstance()->getConnection();
\ = \->query("SELECT id, username, role, nursery_id, user_type FROM users ORDER BY id DESC LIMIT 5");
\ = \->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>"; print_r(\); echo "</pre>";
?>
