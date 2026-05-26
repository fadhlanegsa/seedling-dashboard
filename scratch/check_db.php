<?php
require __DIR__ . '/../config/database.php';
$db = Database::getInstance()->getConnection();

$row = $db->query("SHOW COLUMNS FROM stock LIKE 'program_type'")->fetch();
echo 'Stock program_type: ' . $row['Type'] . "\n";

$row2 = $db->query("SHOW COLUMNS FROM seedling_mutations LIKE 'mutation_type'")->fetch();
echo 'Mutations mutation_type: ' . $row2['Type'] . "\n";

$mutations = $db->query("SELECT id, mutation_type FROM seedling_mutations ORDER BY id DESC LIMIT 5")->fetchAll();
echo "Recent mutations:\n";
print_r($mutations);

$stocks = $db->query("SELECT id, program_type, quantity FROM stock ORDER BY id DESC LIMIT 5")->fetchAll();
echo "Recent stocks:\n";
print_r($stocks);
