<?php
// Simulate GET request to /api/trace/PE-12-37-40-1-128-260519-88
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/seedling-dashboard/seedling-dashboard/public/api/trace/PE-12-37-40-1-128-260519-88';
$_SERVER['SCRIPT_NAME'] = '/seedling-dashboard/seedling-dashboard/public/index.php';
$_SERVER['HTTP_HOST'] = 'localhost';

// Catch output buffer
ob_start();
require_once __DIR__ . '/../public/index.php';
$output = ob_get_clean();

echo "=== SIMULATED API RESPONSE ===\n";
echo $output . "\n";
