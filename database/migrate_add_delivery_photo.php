<?php
/**
 * Migration Script: Add delivery_photo_path column
 * Run this script to add delivery photo support
 */

require_once __DIR__ . '/../config/config.php';

try {
    // Create PDO connection directly
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Starting migration: Add delivery_photo_path column...\n";
    
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/add_delivery_photo.sql');
    
    // Execute migration
    $conn->exec($sql);
    
    echo "✓ Migration completed successfully!\n";
    echo "✓ Added delivery_photo_path column\n";
    echo "✓ Added index for delivery photo path\n";
    
} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
