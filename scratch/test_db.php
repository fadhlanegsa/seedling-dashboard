<?php
require 'config/config.php';
require 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT r.*, 
               u.full_name as recipient_name, u.nik as recipient_nik,
               st.name as seedling_name,
               b.name as bpdas_name,
               n.name as nursery_name,
               r.planting_address as address
        FROM requests r
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN seedling_types st ON r.seedling_type_id = st.id
        LEFT JOIN bpdas b ON r.bpdas_id = b.id
        LEFT JOIN nurseries n ON r.nursery_id = n.id
        WHERE r.id = ?
        LIMIT 1
    ");
    $stmt->execute([1]);
    var_dump($stmt->fetch(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
