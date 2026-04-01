<?php
$host = 'localhost';
$dbname = 'seedling_db';
$user = 'root';
$pass = '';

$db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

// get BPDAS id
$stmt = $db->prepare("SELECT id FROM bpdas WHERE name LIKE ?");
$stmt->execute(['%Unda Anyar%']);
$bpdasId = $stmt->fetchColumn();

echo "BPDAS ID: " . $bpdasId . "\n";

// get aggregated stock
$sql = "SELECT s.seedling_type_id, s.program_type, SUM(s.quantity) as quantity, 
        st.name as seedling_name, st.scientific_name, st.category
        FROM stock s
        INNER JOIN seedling_types st ON s.seedling_type_id = st.id
        WHERE s.bpdas_id = ?
        GROUP BY s.seedling_type_id, s.program_type, st.name, st.scientific_name, st.category
        ORDER BY st.name ASC, s.program_type ASC";

$stmt = $db->prepare($sql);
$stmt->execute([$bpdasId]);
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

print_r($res);
