<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/';
$_SERVER['HTTP_HOST'] = 'localhost';
$_GET['type'] = 'PE';
require 'config/config.php';
require CORE_PATH . 'Model.php';
require CORE_PATH . 'View.php';
require CORE_PATH . 'Controller.php';
require 'controllers/SeedlingAdminController.php';

session_start();
$_SESSION['user'] = [
    'id' => 1,
    'role' => 'admin_pusat',
    'nursery_id' => null
];

try {
    echo "DEBUG: Before class\n";
    $c = new SeedlingAdminController();
    echo "DEBUG: Before method\n";
    $c->getMutationSourcesAjax();
    echo "DEBUG: After method\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
} catch (Error $e) {
    echo "Error: " . $e->getMessage();
}
