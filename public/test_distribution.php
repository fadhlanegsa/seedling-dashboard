<?php
// Test script for distribution stats
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/Request.php';

try {
    // Mock DB Connection for Model
    // Note: Model constructor usually handles this if we instantiate it properly or manually set DB
    
    // Quick hack to instantiate Config/DB if needed, but Model extends Model which creates connection
    // Let's rely on standard Model instantiation
    
    $requestModel = new Request();
    
    echo "Testing getMonthlyDistributionStats()...\n";
    $stats = $requestModel->getMonthlyDistributionStats(2026);
    
    echo "Found " . count($stats) . " records.\n";
    print_r($stats);
    
    // Also test with current year if different
    if (date('Y') != '2026') {
        echo "Testing current year (" . date('Y') . ")...\n";
        $statsCurrent = $requestModel->getMonthlyDistributionStats(date('Y'));
        print_r($statsCurrent);
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
