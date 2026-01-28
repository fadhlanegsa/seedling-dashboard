<?php
/**
 * Migration: Add address column to users table
 * Run this file once to add the missing address column
 */

require_once __DIR__ . '/../config/config.php';

try {
    // Create database connection
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking if 'address' column exists in 'users' table...\n";
    
    // Check if column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'address'");
    $columnExists = $stmt->rowCount() > 0;
    
    if ($columnExists) {
        echo "✓ Column 'address' already exists. No migration needed.\n";
    } else {
        echo "Adding 'address' column to 'users' table...\n";
        
        // Add the column
        $sql = "ALTER TABLE users ADD COLUMN address TEXT NULL AFTER nik";
        $pdo->exec($sql);
        
        echo "✓ Successfully added 'address' column to 'users' table!\n";
    }
    
    echo "\nMigration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
