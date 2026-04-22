<?php
/**
 * DEBUG: Mutation Naik Kelas Diagnostic
 * Upload ke hosting, akses via browser, DELETE setelah selesai
 */
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance()->getConnection();
$errors = [];
$infos = [];

echo "<pre style='font-family:monospace;padding:20px;'>";
echo "=== MUTATION NAIK KELAS DIAGNOSTIC ===\n\n";

// 1. Check stock table columns
echo "--- [1] STOCK TABLE COLUMNS ---\n";
try {
    $cols = $db->query("SHOW COLUMNS FROM stock")->fetchAll(PDO::FETCH_ASSOC);
    $colNames = array_column($cols, 'Field');
    echo "Columns: " . implode(', ', $colNames) . "\n";

    $required = ['program_type', 'source_type', 'last_update_date'];
    foreach ($required as $col) {
        if (in_array($col, $colNames)) {
            echo "  [OK] Column '$col' EXISTS\n";
        } else {
            echo "  [MISSING] Column '$col' NOT FOUND - NEED TO ALTER TABLE\n";
            $errors[] = "Missing column: stock.$col";
        }
    }

    // Check program_type enum values
    foreach ($cols as $col) {
        if ($col['Field'] === 'program_type') {
            echo "  program_type definition: " . $col['Type'] . "\n";
            if (strpos($col['Type'], 'bibitgratis') === false) {
                echo "  [MISSING] 'bibitgratis' NOT in program_type enum!\n";
                $errors[] = "'bibitgratis' not in stock.program_type enum";
            } else {
                echo "  [OK] 'bibitgratis' is in enum\n";
            }
        }
        if ($col['Field'] === 'source_type') {
            echo "  source_type definition: " . $col['Type'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "ERROR checking stock columns: " . $e->getMessage() . "\n";
    $errors[] = $e->getMessage();
}

// 2. Check UNIQUE KEY on stock
echo "\n--- [2] STOCK UNIQUE KEY ---\n";
try {
    $indexes = $db->query("SHOW INDEX FROM stock")->fetchAll(PDO::FETCH_ASSOC);
    $uniqueKeys = [];
    foreach ($indexes as $idx) {
        if ($idx['Non_unique'] == 0) {
            $uniqueKeys[$idx['Key_name']][] = $idx['Column_name'];
        }
    }
    foreach ($uniqueKeys as $name => $cols) {
        echo "  Unique key '$name': (" . implode(', ', $cols) . ")\n";
    }

    // Check if unique key has both program_type and source_type
    $hasGoodKey = false;
    foreach ($uniqueKeys as $name => $cols) {
        if (in_array('program_type', $cols) && in_array('source_type', $cols)) {
            $hasGoodKey = true;
            echo "  [OK] Unique key includes program_type + source_type\n";
        }
    }
    if (!$hasGoodKey) {
        echo "  [WARN] No unique key includes both program_type AND source_type\n";
        echo "  [WARN] This may cause duplicate inserts or conflicts\n";
        $errors[] = "UNIQUE KEY on stock does not include program_type + source_type";
    }
} catch (Exception $e) {
    echo "ERROR checking indexes: " . $e->getMessage() . "\n";
}

// 3. Check seedling_mutations columns
echo "\n--- [3] SEEDLING_MUTATIONS TABLE ---\n";
try {
    $cols = $db->query("SHOW COLUMNS FROM seedling_mutations")->fetchAll(PDO::FETCH_ASSOC);
    $colNames = array_column($cols, 'Field');
    echo "Columns: " . implode(', ', $colNames) . "\n";

    foreach ($cols as $col) {
        if (in_array($col['Field'], ['origin_location', 'target_location'])) {
            $nullable = ($col['Null'] === 'YES') ? '[OK] nullable' : '[WARN] NOT NULL - may fail if empty';
            echo "  {$col['Field']}: {$col['Type']} - $nullable\n";
            if ($col['Null'] === 'NO') {
                $errors[] = "seedling_mutations.{$col['Field']} is NOT NULL - need ALTER";
            }
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// 4. Try a test INSERT to stock (dry run - rollback)
echo "\n--- [4] TEST INSERT INTO STOCK (will ROLLBACK) ---\n";
try {
    $db->beginTransaction();
    
    // Get a valid bpdas_id and nursery_id
    $bpdas = $db->query("SELECT id FROM bpdas LIMIT 1")->fetch();
    $nursery = $db->query("SELECT id FROM nurseries LIMIT 1")->fetch();
    $seedlingType = $db->query("SELECT id FROM seedling_types LIMIT 1")->fetch();

    if (!$bpdas || !$seedlingType) {
        echo "  [SKIP] No bpdas or seedling_type found to test with\n";
        $db->rollBack();
    } else {
        $testSql = "INSERT INTO stock (bpdas_id, nursery_id, seedling_type_id, program_type, quantity, source_type, last_update_date, notes) 
                    VALUES (?, ?, ?, 'bibitgratis', 1, 'PUB', CURDATE(), 'DEBUG TEST - will be rolled back')
                    ON DUPLICATE KEY UPDATE quantity = quantity + 0";
        $stmt = $db->prepare($testSql);
        $stmt->execute([
            $bpdas['id'],
            $nursery['id'] ?? null,
            $seedlingType['id']
        ]);
        echo "  [OK] Test INSERT succeeded (rolling back)\n";
        $infos[] = "Stock INSERT syntax is valid";
        $db->rollBack();
    }
} catch (Exception $e) {
    $db->rollBack();
    echo "  [FAIL] INSERT failed: " . $e->getMessage() . "\n";
    $errors[] = "Test INSERT to stock failed: " . $e->getMessage();
}

// 5. Check if pub_audit_trails table exists
echo "\n--- [5] PUB_AUDIT_TRAILS TABLE ---\n";
try {
    $check = $db->query("SHOW TABLES LIKE 'pub_audit_trails'")->rowCount();
    if ($check > 0) {
        echo "  [OK] pub_audit_trails table EXISTS\n";
    } else {
        echo "  [MISSING] pub_audit_trails does NOT exist - create it!\n";
        $errors[] = "Table pub_audit_trails missing";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// 6. Check CSRF_TOKEN_NAME constant
echo "\n--- [6] CSRF TOKEN CONFIG ---\n";
echo "  CSRF_TOKEN_NAME constant: '" . CSRF_TOKEN_NAME . "'\n";
echo "  mutation_form.php uses hardcoded 'csrf_token'\n";
if (CSRF_TOKEN_NAME !== 'csrf_token') {
    echo "  [MISMATCH!] Form sends 'csrf_token' but config expects '" . CSRF_TOKEN_NAME . "'\n";
    $errors[] = "CSRF token name mismatch! Form='csrf_token', Config='" . CSRF_TOKEN_NAME . "'";
} else {
    echo "  [OK] CSRF token names match\n";
}

// === SUMMARY ===
echo "\n=== SUMMARY ===\n";
if (empty($errors)) {
    echo "[ALL OK] No issues found. Problem may be elsewhere.\n";
} else {
    echo "[ERRORS FOUND " . count($errors) . "]:\n";
    foreach ($errors as $i => $err) {
        echo "  " . ($i+1) . ". $err\n";
    }
}

echo "\n=== FIX SQL (run these if errors above) ===\n";
echo "
-- Run in phpMyAdmin if errors above:

-- Fix 1: Add source_type to stock
ALTER TABLE \`stock\` ADD COLUMN IF NOT EXISTS \`source_type\` VARCHAR(50) NULL DEFAULT NULL;

-- Fix 2: Add bibitgratis to enum
ALTER TABLE \`stock\` MODIFY COLUMN \`program_type\` ENUM('Reguler','FOLU','bibitgratis') NOT NULL DEFAULT 'Reguler';

-- Fix 3: Update unique key
ALTER TABLE \`stock\` DROP INDEX IF EXISTS \`unique_nursery_stock\`;
ALTER TABLE \`stock\` DROP INDEX IF EXISTS \`unique_stock\`;
ALTER TABLE \`stock\` ADD UNIQUE KEY \`unique_stock\` (nursery_id, seedling_type_id, program_type, source_type);

-- Fix 4: Mutations nullable locations
ALTER TABLE \`seedling_mutations\` MODIFY \`origin_location\` VARCHAR(255) NULL, MODIFY \`target_location\` VARCHAR(255) NULL;

-- Fix 5: Create audit trail table
CREATE TABLE IF NOT EXISTS \`pub_audit_trails\` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_type VARCHAR(100) NOT NULL,
    record_id INT NOT NULL,
    audit_data JSON NOT NULL,
    edited_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (edited_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_transaction (transaction_type, record_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

echo "\n=== END DIAGNOSTIC ===\n";
echo "</pre>";
