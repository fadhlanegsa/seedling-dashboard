<?php
// Simulate POST request to /api/trace/verify
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/seedling-dashboard/seedling-dashboard/public/api/trace/verify';
$_SERVER['SCRIPT_NAME'] = '/seedling-dashboard/seedling-dashboard/public/index.php';
$_SERVER['HTTP_HOST'] = 'localhost';

// Mock raw POST input payload
$payload = [
    'codes' => [
        'PE-12-37-40-1-128-260519-88',  // Valid
        'PE-12-37-40-1-128-260519-999', // Invalid (index > qty 800)
        'PE-12-99-99-9-99-260519-88',   // Invalid (metadata mismatch)
        'PE-INVALID'                    // Invalid format
    ]
];

$GLOBALS['_TEST_PAYLOAD'] = $payload;

// We need to override getJsonBody inside ApiController in index.php context by replacing the php://input read.
// Since we wrote controllers/ApiTraceController.php using parent's getJsonBody(), let's make sure our mock payload is parsed.
// Let's modify the ApiController method dynamically or just override inside our mock script, but wait!
// The getJsonBody() in ApiController reads file_get_contents('php://input').
// Since php://input is read-only in PHP CLI, we can temporarily modify ApiController's getJsonBody()
// to look for a global override variable first! This makes CLI testing super clean and robust.

echo "=== SIMULATING BATCH VERIFY POST REQUEST ===\n";

// Catch output buffer
ob_start();
require_once __DIR__ . '/../public/index.php';
$output = ob_get_clean();

echo "=== SIMULATED BATCH RESPONSE ===\n";
echo $output . "\n";
