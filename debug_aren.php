<?php
require 'c:/xampp/htdocs/seedling-dashboard/seedling-dashboard/config/database.php';
$db = Database::getInstance()->getConnection();

$itemName = 'Benih Aren';
$item = $db->query("SELECT id FROM bahan_baku_master WHERE name = '$itemName'")->fetch();

if (!$item) {
    die("Item not found");
}

$id = $item['id'];
echo "Item ID for $itemName: $id\n";

// Check trans_in
$res = $db->query("SELECT SUM(quantity) as total FROM bahan_baku_transactions WHERE item_id = $id")->fetch();
echo "Bahan Baku IN: " . ($res['total'] ?? 0) . "\n";

// Check mixing_out
$res = $db->query("SELECT SUM(mi.quantity) as total FROM media_mixing_items mi WHERE mi.item_id = $id")->fetch();
echo "Mixing Out: " . ($res['total'] ?? 0) . "\n";

// Check filling_out
$res = $db->query("SELECT SUM(bag_quantity) as total FROM bag_fillings WHERE bag_item_id = $id")->fetch();
echo "Filling Out: " . ($res['total'] ?? 0) . "\n";

// Check sowing_seed_out
$res = $db->query("SELECT SUM(seed_quantity) as total FROM seed_sowings WHERE seed_item_id = $id")->fetch();
echo "Sowing Seed Out: " . ($res['total'] ?? 0) . "\n";

// Check sowing_mat_out
$res = $db->query("SELECT SUM(quantity) as total FROM seed_sowing_materials WHERE item_id = $id")->fetch();
echo "Sowing Material Out: " . ($res['total'] ?? 0) . "\n";

// Check weaning_mat_out
$res = $db->query("SELECT SUM(quantity) as total FROM seedling_weaning_materials WHERE item_id = $id")->fetch();
echo "Weaning Material Out: " . ($res['total'] ?? 0) . "\n";

// Check entres_mat_out
$res = $db->query("SELECT SUM(quantity) as total FROM seedling_entres_materials WHERE item_id = $id")->fetch();
echo "Entres Material Out: " . ($res['total'] ?? 0) . "\n";
