<?php
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();

try {
    // Get all automated_pub rows still labeled as Reguler
    $stmt = $db->query("SELECT id, nursery_id, seedling_type_id, quantity FROM stock WHERE source_type = 'automated_pub' AND program_type = 'Reguler'");
    $regulerRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($regulerRows) . " Reguler rows to migrate\n";

    foreach ($regulerRows as $row) {
        // Check if a bibitgratis row already exists for same nursery+seedling
        $check = $db->prepare("SELECT id, quantity FROM stock WHERE nursery_id = ? AND seedling_type_id = ? AND program_type = 'bibitgratis' AND source_type = 'automated_pub' LIMIT 1");
        $check->execute([$row['nursery_id'], $row['seedling_type_id']]);
        $existing = $check->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Merge quantity into existing bibitgratis row
            $newQty = $existing['quantity'] + $row['quantity'];
            $db->prepare("UPDATE stock SET quantity = ? WHERE id = ?")->execute([$newQty, $existing['id']]);
            // Delete the old Reguler row
            $db->prepare("DELETE FROM stock WHERE id = ?")->execute([$row['id']]);
            echo "Merged row ID {$row['id']} (qty {$row['quantity']}) into bibitgratis ID {$existing['id']} -> new qty: {$newQty}\n";
        } else {
            // No duplicate, just relabel it
            $db->prepare("UPDATE stock SET program_type = 'bibitgratis' WHERE id = ?")->execute([$row['id']]);
            echo "Relabeled row ID {$row['id']} to bibitgratis\n";
        }
    }

    echo "Done!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
