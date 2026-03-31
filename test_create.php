<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';
$_SERVER['SCRIPT_NAME'] = '/seedling-dashboard/public/index.php';
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/models/User.php';

$userModel = new User();
$data = [
    'username'   => 'test_operator_2',
    'email'      => 'test_operator_2@example.com',
    'full_name'  => 'Test Operator 2',
    'phone'      => '08123456789',
    'role'       => 'operator_persemaian',
    'bpdas_id'   => null,
    'nursery_id' => 1,
    'is_active'  => 1,
    'password'   => 'password123'
];
$id = $userModel->register($data);
if ($id) {
    echo "Success: $id";
} else {
    echo "Fail!\n";
    $logFile = __DIR__ . '/logs/error_' . date('Y-m-d') . '.log';
    if (file_exists($logFile)) {
        echo file_get_contents($logFile);
    }
}
?>
