<?php
/**
 * Migration Script: Add delivered status
 * Run this script to add delivered status to requests
 */

require_once __DIR__ . '/../config/config.php';

try {
    // Create PDO connection directly
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Starting migration: Add delivered status...\n";
    
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/add_delivered_status.sql');
    
    // Execute migration
    $conn->exec($sql);
    
    echo "✓ Migration completed successfully!\n";
    echo "✓ Added 'delivered' status to requests table\n";
    
} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
