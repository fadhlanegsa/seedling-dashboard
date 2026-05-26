<?php
$db = new PDO('mysql:host=localhost;dbname=wast6986_db_bibit', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT 
            m.id as item_id, 
            m.name as item_name, 
            m.unit, 
            t.seed_source_id, 
            ss.seed_source_name,
            SUM(t.quantity) as total_in,
            (
                SELECT COALESCE(SUM(seed_quantity), 0) 
                FROM seed_sowings s 
                WHERE s.seed_item_id = m.id 
                AND (s.seed_source_id = t.seed_source_id OR (s.seed_source_id IS NULL AND t.seed_source_id IS NULL))
            ) as total_out
        FROM bahan_baku_transactions t
        JOIN bahan_baku_master m ON t.item_id = m.id
        LEFT JOIN seed_sources ss ON t.seed_source_id = ss.id
        WHERE m.category = 'BENIH' 
        GROUP BY m.id, m.name, m.unit, t.seed_source_id, ss.seed_source_name
        HAVING (total_in - total_out) > 0
        ORDER BY m.name ASC, ss.seed_source_name ASC";

try {
    $stmt = $db->query($sql);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($res);
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
