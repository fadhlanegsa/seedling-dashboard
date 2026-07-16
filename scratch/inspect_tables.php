<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';
$_SERVER['SCRIPT_NAME'] = '/index.php';

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$db = Database::getInstance();

echo "=== STOCK ITEMS ===\n";
$rows = $db->query("SELECT s.*, st.name as seedling_name, n.name as nursery_name, b.name as bpdas_name 
                    FROM stock s 
                    LEFT JOIN seedling_types st ON s.seedling_type_id = st.id
                    LEFT JOIN nurseries n ON s.nursery_id = n.id
                    LEFT JOIN bpdas b ON s.bpdas_id = b.id
                    LIMIT 20");
print_r($rows);
