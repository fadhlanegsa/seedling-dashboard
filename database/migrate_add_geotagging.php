<?php
/**
 * Migration Script: Add Geotagging Columns
 * Run this script to add latitude and longitude columns to requests table
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting migration: Add geotagging columns...\n";
    
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/add_geotagging_columns.sql');
    
    // Execute SQL
    $db->exec($sql);
    
    echo "✓ Migration completed successfully!\n";
    echo "✓ Latitude and longitude columns added to requests table\n";
    
} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
