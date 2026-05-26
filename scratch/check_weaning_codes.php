<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/scratch/check_weaning_codes.php';
require 'c:/xampp/htdocs/seedling-dashboard/seedling-dashboard/config/database.php';
$db = Database::getInstance()->getConnection();

$prefix = 'PE-202605';
$sql = "SELECT weaning_code FROM seedling_weanings WHERE weaning_code LIKE ? ORDER BY weaning_code DESC";
$stmt = $db->prepare($sql);
$stmt->execute([$prefix . '%']);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total records found with prefix $prefix: " . count($results) . "\n";
foreach ($results as $row) {
    echo "[" . $row['weaning_code'] . "]\n";
}
