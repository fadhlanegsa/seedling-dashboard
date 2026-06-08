<?php
require __DIR__ . '/../config/database.php';
$db = Database::getInstance()->getConnection();

echo "=== CHECK POLYBAG STOCK ===\n";

// 1. Check raw counts in seed_sowing_polybags and seedling_weaning_polybags
$sowingPolyCount = $db->query("SELECT SUM(quantity) FROM seed_sowing_polybags")->fetchColumn() ?: 0;
$weaningPolyCount = $db->query("SELECT SUM(quantity) FROM seedling_weaning_polybags")->fetchColumn() ?: 0;

echo "Total used in sowing (seed_sowing_polybags): " . $sowingPolyCount . "\n";
echo "Total used in weaning (seedling_weaning_polybags): " . $weaningPolyCount . "\n";

// 2. Query using the new UNION ALL query logic
$sql = "SELECT f.id, f.filling_code, f.total_production, (f.total_production - COALESCE(used.total_used, 0)) as remaining_stock
        FROM bag_fillings f
        LEFT JOIN (
            SELECT bag_filling_id, SUM(quantity) as total_used
            FROM (
                SELECT bag_filling_id, quantity FROM seed_sowing_polybags
                UNION ALL
                SELECT bag_filling_id, quantity FROM seedling_weaning_polybags
            ) combined_used
            GROUP BY bag_filling_id
        ) used ON f.id = used.bag_filling_id
        ORDER BY f.filling_date DESC";

$stmt = $db->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "\n=== BAG FILLINGS REMAINING STOCK ===\n";
foreach ($results as $row) {
    echo "ID: {$row['id']} | Code: {$row['filling_code']} | Production: {$row['total_production']} | Remaining Stock: {$row['remaining_stock']}\n";
}
