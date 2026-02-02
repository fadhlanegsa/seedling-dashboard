<?php
require_once __DIR__ . '/../config/config.php';

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents(__DIR__ . '/update_categories.sql');
    
    // Split by semicolon and execute each
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "Updating categories...\n";
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $rows = $db->exec($stmt);
            echo "Executed: " . substr($stmt, 0, 50) . "... (Affected: $rows)\n";
        }
    }
    echo "Done.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
