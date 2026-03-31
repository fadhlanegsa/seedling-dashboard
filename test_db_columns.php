<?php
define('APP_PATH', __DIR__);
require_once 'config/config.php';
require_once 'core/Database.php';

\ = Database::getInstance()->getConnection();
\ = \->query("DESCRIBE users");
\ = \->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r(\);
echo "</pre>";
?>
