<?php
/**
 * Migration Script: Add Planting Address Column
 * Run this script to add planting_address column to requests table
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting migration: Add planting_address column...\n";
    
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/add_planting_address.sql');
    
    // Execute SQL
    $db->exec($sql);
    
    echo "✓ Migration completed successfully!\n";
    echo "✓ planting_address column added to requests table\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "⚠ Column planting_address already exists. Skipping.\n";
    } else {
        echo "✗ Migration failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}
