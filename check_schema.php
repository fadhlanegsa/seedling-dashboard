<?php
require_once 'config/database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE requests");
echo "<pre>";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "</pre>";
?>
