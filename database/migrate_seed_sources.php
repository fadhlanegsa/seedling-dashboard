<?php
/**
 * Migration Script: Create Seed Sources Table
 * Run this file to create the seed_sources table for Direktori Sumber Benih Nasional
 * 
 * Usage: php database/migrate_seed_sources.php
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

echo "=====================================\n";
echo "Seed Sources Table Migration\n";
echo "=====================================\n\n";

try {
    // Get database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "Reading migration file...\n";
    
    // Read SQL file
    $sqlFile = __DIR__ . '/seed_sources_migration.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Migration file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    echo "Executing migration...\n";
    
    // Execute the SQL
    $conn->exec($sql);
    
    echo "\n✓ SUCCESS: seed_sources table created successfully!\n\n";
    
    // Verify table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'seed_sources'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Table verification passed\n";
        
        // Show table structure
        echo "\nTable Structure:\n";
        echo "================\n";
        $stmt = $conn->query("DESCRIBE seed_sources");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo sprintf("%-30s %-20s %s\n", 
                $column['Field'], 
                $column['Type'], 
                $column['Null'] === 'NO' ? 'NOT NULL' : 'NULL'
            );
        }
        
        echo "\n✓ Migration completed successfully!\n";
        echo "=====================================\n";
    } else {
        throw new Exception("Table verification failed - table not found after creation");
    }
    
} catch (PDOException $e) {
    echo "\n✗ ERROR: Database error occurred\n";
    echo "Error Message: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n\n";
    logError("Seed Sources Migration Error: " . $e->getMessage());
    exit(1);
} catch (Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n\n";
    logError("Seed Sources Migration Error: " . $e->getMessage());
    exit(1);
}
