<?php
/**
 * Migration Script: Implement Nursery Hierarchy
 * 
 * 1. Create 'nurseries' table
 * 2. Add 'nursery_id' and 'role' enum update to 'users' table
 * 3. Add 'nursery_id' to 'stock' table
 * 4. Create default nurseries for existing BPDAS
 * 5. Migrate existing stock to default nurseries
 */

require_once dirname(__DIR__) . '/config/database.php';

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Starting Nursery Hierarchy Migration...\n";
    
    // 1. Create nurseries table
    echo "1. Creating 'nurseries' table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS nurseries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        bpdas_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        address TEXT,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (bpdas_id) REFERENCES bpdas(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sql);
    echo "   Done.\n";
    
    // 2. Add columns to users table
    echo "2. Updating 'users' table...\n";
    
    // Check if nursery_id column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'nursery_id'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN nursery_id INT NULL AFTER bpdas_id");
        $db->exec("ALTER TABLE users ADD CONSTRAINT fk_users_nursery FOREIGN KEY (nursery_id) REFERENCES nurseries(id) ON DELETE SET NULL");
        echo "   Added 'nursery_id' column.\n";
    } else {
        echo "   'nursery_id' column already exists.\n";
    }
    
    // Update role enum (Active record modification is tricky, usually done by modifying column definition)
    // We strictly assume the current enum is 'admin','bpdas','public'. We need to add 'operator_persemaian'
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'role'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $type = $row['Type'];
    
    if (strpos($type, 'operator_persemaian') === false) {
        // Extract current enums to append
        preg_match("/^enum\((.*)\)$/", $type, $matches);
        $enums = $matches[1];
        $newEnums = $enums . ",'operator_persemaian'";
        
        $db->exec("ALTER TABLE users MODIFY COLUMN role ENUM($newEnums) NOT NULL DEFAULT 'public'");
        echo "   Updated 'role' enum to include 'operator_persemaian'.\n";
    } else {
        echo "   'role' enum already contains 'operator_persemaian'.\n";
    }

    // 3. Add columns to stock table
    echo "3. Updating 'stock' table...\n";
    
    $stmt = $db->query("SHOW COLUMNS FROM stock LIKE 'nursery_id'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE stock ADD COLUMN nursery_id INT NULL AFTER bpdas_id");
        $db->exec("ALTER TABLE stock ADD CONSTRAINT fk_stock_nursery FOREIGN KEY (nursery_id) REFERENCES nurseries(id) ON DELETE CASCADE"); // Cascade deletion? Or Set Null? Analyzing... Cascade is better for consistency if nursery deleted.
        echo "   Added 'nursery_id' column.\n";
    } else {
        echo "   'nursery_id' column already exists.\n";
    }

    // 4. Migrate Data
    echo "4. Migrating Data...\n";
    $db->beginTransaction();
    
    // Get all BPDAS
    $stmt = $db->query("SELECT id, name FROM bpdas");
    $bpdasList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $migratedCount = 0;
    
    foreach ($bpdasList as $bpdas) {
        $bpdasId = $bpdas['id'];
        $nurseryName = "Persemaian " . str_replace('BPDAS ', '', $bpdas['name']) . " (Default)";
        
        // Check if default nursery exists
        $stmt = $db->prepare("SELECT id FROM nurseries WHERE bpdas_id = ? AND name = ? LIMIT 1");
        $stmt->execute([$bpdasId, $nurseryName]);
        $nursery = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$nursery) {
            // Create default nursery
            $stmt = $db->prepare("INSERT INTO nurseries (bpdas_id, name) VALUES (?, ?)");
            $stmt->execute([$bpdasId, $nurseryName]);
            $nurseryId = $db->lastInsertId();
            echo "   Created default nursery for {$bpdas['name']} (ID: $nurseryId)\n";
        } else {
            $nurseryId = $nursery['id'];
        }
        
        // Migrate Stock
        // Update stock entries for this BPDAS to point to the new Default Nursery
        // Only update if nursery_id is NULL to avoid overwriting future data
        $stmt = $db->prepare("UPDATE stock SET nursery_id = ? WHERE bpdas_id = ? AND nursery_id IS NULL");
        $stmt->execute([$nurseryId, $bpdasId]);
        
        $count = $stmt->rowCount();
        if ($count > 0) {
            echo "   -> Migrated $count stock records to Nursery ID $nurseryId\n";
            $migratedCount += $count;
        }
    }
    
    $db->commit();
    echo "Migration completed successfully! Total stock records migrated: $migratedCount\n";
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    die("Migration Failed: " . $e->getMessage() . "\n");
}
