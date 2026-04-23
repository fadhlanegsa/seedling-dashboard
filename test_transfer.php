<?php
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/models/SeedlingMutation.php';

$model = new SeedlingMutation();
$data = [
    'mutation_code' => 'TEST-002',
    'mutation_date' => date('Y-m-d'),
    'source_type' => 'PE',
    'source_id' => 1,
    'mutation_type' => 'TRANSFER',
    'quantity' => 2,
    'origin_location' => 'Test',
    'target_location' => 'New Area 51',
    'mandor' => 'Test Mandor',
    'manager' => 'Test Manager',
    'notes' => 'Test Transfer',
    'bpdas_id' => 1,
    'nursery_id' => 1,
    'created_by' => 1
];

echo "Saving TRANSFER mutation...\n";
$res = $model->saveMutation($data);
echo "Result: " . ($res ? "Success" : "Failed") . "\n";
