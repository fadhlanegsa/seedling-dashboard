<?php
define('BASE_PATH', 'c:/xampp/htdocs/seedling-dashboard/seedling-dashboard/');
define('CORE_PATH', BASE_PATH . 'core/');
require_once BASE_PATH . 'config/database.php';

$db = Database::getInstance()->getConnection();
$res = $db->query("DESCRIBE media_mixing_productions");
print_r($res->fetchAll(PDO::FETCH_ASSOC));
