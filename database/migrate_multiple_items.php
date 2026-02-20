<?php
/**
 * Database Migration: Multiple Items Per Request
 * Run this file ONCE to migrate database
 * URL: http://localhost/seedling-dashboard/public/../database/migrate_multiple_items.php
 */

// Prevent direct access from web
if (php_sapi_name() === 'cli') {
    // CLI is OK
} else {
    // Check for admin authentication
    session_start();
    require_once __DIR__ . '/../config/config.php';
    
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        die('Unauthorized. Admin access required.');
    }
}

require_once __DIR__ . '/../config/config.php';

echo "=== Database Migration: Multiple Items Per Request ===\n\n";

try {
    $db = db();
    
    // Step 1: Create request_items table
    echo "Step 1: Creating request_items table...\n";
    
    $sql = "CREATE TABLE IF NOT EXISTS `request_items` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `request_id` INT NOT NULL,
      `seedling_type_id` INT NOT NULL,
      `quantity` INT NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`request_id`) REFERENCES `requests`(`id`) ON DELETE CASCADE,
      FOREIGN KEY (`seedling_type_id`) REFERENCES `seedling_types`(`id`),
      INDEX `idx_request` (`request_id`),
      INDEX `idx_seedling_type` (`seedling_type_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
    echo "✓ request_items table created successfully\n\n";
    
    // Step 2: Check if migration needed
    echo "Step 2: Checking for existing data to migrate...\n";
    
    $checkSql = "SELECT COUNT(*) as count 
                 FROM requests 
                 WHERE seedling_type_id IS NOT NULL 
                   AND quantity IS NOT NULL 
                   AND quantity > 0";
    
    $stmt = $db->query($checkSql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $recordsToMigrate = $result['count'];
    
    echo "Found $recordsToMigrate requests to migrate\n\n";
    
    if ($recordsToMigrate > 0) {
        // Step 3: Migrate existing data
        echo "Step 3: Migrating existing requests...\n";
        
        $migrateSql = "INSERT INTO `request_items` (`request_id`, `seedling_type_id`, `quantity`, `created_at`)
                       SELECT 
                         `id` as request_id,
                         `seedling_type_id`,
                         `quantity`,
                         `created_at`
                       FROM `requests`
                       WHERE `seedling_type_id` IS NOT NULL 
                         AND `quantity` IS NOT NULL
                         AND `quantity` > 0
                         AND NOT EXISTS (
                           SELECT 1 FROM `request_items` ri WHERE ri.request_id = requests.id
                         )";
        
        $migratedCount = $db->exec($migrateSql);
        echo "✓ Migrated $migratedCount records to request_items\n\n";
    } else {
        echo "No records to migrate\n\n";
    }
    
    // Step 4: Verify migration
    echo "Step 4: Verifying migration...\n";
    
    $verifySql = "SELECT 
                    r.id,
                    r.request_number,
                    COUNT(ri.id) as item_count,
                    SUM(ri.quantity) as total_quantity
                  FROM requests r
                  LEFT JOIN request_items ri ON r.id = ri.request_id
                  GROUP BY r.id
                  ORDER BY r.created_at DESC
                  LIMIT 5";
    
    $stmt = $db->query($verifySql);
    $verifyResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Sample of migrated data:\n";
    echo str_pad('Request #', 20) . str_pad('Items', 10) . "Total Qty\n";
    echo str_repeat('-', 40) . "\n";
    
    foreach ($verifyResults as $row) {
        echo str_pad($row['request_number'] ?? 'N/A', 20) . 
             str_pad($row['item_count'] ?? 0, 10) . 
             ($row['total_quantity'] ?? 0) . " bibit\n";
    }
    
    echo "\n";
    echo "✅ Migration completed successfully!\n\n";
    echo "NEXT STEPS:\n";
    echo "1. Update your code to use request_items table\n";
    echo "2. Test the new multi-item functionality\n";
    echo "3. After verification, you can optionally drop old columns:\n";
    echo "   ALTER TABLE requests DROP COLUMN seedling_type_id, DROP COLUMN quantity;\n\n";
    echo "⚠️  IMPORTANT: Backup your database before dropping columns!\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
