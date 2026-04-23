<?php
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/models/SeedlingMutation.php';

$model = new SeedlingMutation();
$data = [
    'mutation_code' => 'TEST-001',
    'mutation_date' => date('Y-m-d'),
    'source_type' => 'PE',
    'source_id' => 1, // Assumes there's a weaning record with id=1
    'mutation_type' => 'NAIK KELAS',
    'quantity' => 5,
    'origin_location' => 'Test',
    'target_location' => 'Test Target',
    'mandor' => 'Test Mandor',
    'manager' => 'Test Manager',
    'notes' => 'Test Notes',
    'bpdas_id' => 1,
    'nursery_id' => 1,
    'created_by' => 1
];

echo "Saving mutation...\n";
$res = $model->saveMutation($data);
echo "Result: " . ($res ? "Success" : "Failed") . "\n";
