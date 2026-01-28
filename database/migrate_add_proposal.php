<?php
/**
 * Migration Script: Add proposal_file_path column
 * Run this script to add proposal upload support
 */

require_once __DIR__ . '/../config/config.php';

try {
    // Create PDO connection directly
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Starting migration: Add proposal_file_path column...\n";
    
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/add_proposal_column.sql');
    
    // Execute migration
    $conn->exec($sql);
    
    echo "✓ Migration completed successfully!\n";
    echo "✓ Added proposal_file_path column\n";
    echo "✓ Updated land_area to NOT NULL with 3 decimal places\n";
    echo "✓ Added index for proposal file path\n";
    
} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

