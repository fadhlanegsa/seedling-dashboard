<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$_SERVER['REQUEST_URI'] = '/seedling-dashboard/seedling-dashboard/public/admin/nurseries/create';
$_SERVER['REQUEST_METHOD'] = 'GET';

session_start();
$_SESSION['user'] = [
    'id' => 1, 
    'role' => 'admin', 
    'full_name' => 'Admin', 
    'username' => 'admin', 
    'email' => 'admin@test.com'
];

ob_start();
require 'public/index.php';
$output = ob_get_clean();

// Check for errors or warnings in output
if (preg_match_all('/(Warning|Notice|Error|Fatal|Deprecated).*$/m', $output, $matches)) {
    echo "=== PHP ERRORS FOUND ===\n";
    foreach ($matches[0] as $m) {
        echo $m . "\n";
    }
}

// Check if form elements exist
echo "\n=== FORM FIELD CHECK ===\n";
echo "Has 'card': " . (strpos($output, 'card') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'form-control': " . (strpos($output, 'form-control') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'Nama Persemaian': " . (strpos($output, 'Nama Persemaian') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'bpdas_id': " . (strpos($output, 'bpdas_id') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'dashboard-sidebar': " . (strpos($output, 'dashboard-sidebar') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'Kelola Persemaian': " . (strpos($output, 'Kelola Persemaian') !== false ? 'YES' : 'NO') . "\n";

echo "\nOutput length: " . strlen($output) . " bytes\n";

// Show first 2000 chars
echo "\n=== FIRST 2000 CHARS ===\n";
echo substr($output, 0, 2000);
