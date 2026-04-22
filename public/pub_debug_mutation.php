<?php
/**
 * DEBUG: Mutation Naik Kelas Diagnostic
 * LETAKKAN file ini di folder PUBLIC/ bukan root app
 * Akses via: yourdomain.com/pub_debug_mutation.php
 * HAPUS setelah selesai diagnosa!
 */

// Path dari public/ ke root app
$appRoot = dirname(__DIR__);
define('APP_PATH', $appRoot . '/');

// Load config
$configFile = $appRoot . '/config/config.php';
$dbFile = $appRoot . '/config/database.php';

if (!file_exists($configFile)) {
    die("<pre>ERROR: Config file not found at: $configFile\nMake sure this file is inside the 'public/' folder.</pre>");
}

require_once $configFile;
require_once $dbFile;

$db = Database::getInstance()->getConnection();
$errors = [];

echo "<pre style='font-family:monospace;background:#1a1a2e;color:#eee;padding:20px;line-height:1.6;'>";
echo "<b style='color:#00d4ff;font-size:16px;'>=== MUTATION NAIK KELAS DIAGNOSTIC ===</b>\n\n";

// 1. Check stock table columns
echo "<b style='color:#ffd700;'>--- [1] STOCK TABLE COLUMNS ---</b>\n";
try {
    $cols = $db->query("SHOW COLUMNS FROM stock")->fetchAll(PDO::FETCH_ASSOC);
    $colNames = array_column($cols, 'Field');
    echo "All Columns: " . implode(', ', $colNames) . "\n\n";

    $required = ['program_type', 'source_type', 'last_update_date'];
    foreach ($required as $col) {
        if (in_array($col, $colNames)) {
            echo "<span style='color:#00ff88;'>  [OK]</span> Column '$col' EXISTS\n";
        } else {
            echo "<span style='color:#ff4444;'>  [MISSING]</span> Column '$col' NOT FOUND — NEED ALTER TABLE!\n";
            $errors[] = "Missing column: stock.$col";
        }
    }

    foreach ($cols as $col) {
        if ($col['Field'] === 'program_type') {
            echo "\n  program_type type: " . $col['Type'] . "\n";
            if (strpos($col['Type'], 'bibitgratis') === false) {
                echo "<span style='color:#ff4444;'>  [MISSING]</span> 'bibitgratis' NOT in enum!\n";
                $errors[] = "'bibitgratis' not in stock.program_type enum";
            } else {
                echo "<span style='color:#00ff88;'>  [OK]</span> 'bibitgratis' is in enum\n";
            }
        }
        if ($col['Field'] === 'source_type') {
            echo "  source_type type: " . $col['Type'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "<span style='color:#ff4444;'>  [ERROR]</span> " . $e->getMessage() . "\n";
}

// 2. Check UNIQUE KEY
echo "\n<b style='color:#ffd700;'>--- [2] STOCK UNIQUE KEYS ---</b>\n";
try {
    $indexes = $db->query("SHOW INDEX FROM stock")->fetchAll(PDO::FETCH_ASSOC);
    $uniqueKeys = [];
    foreach ($indexes as $idx) {
        if ($idx['Non_unique'] == 0) {
            $uniqueKeys[$idx['Key_name']][] = $idx['Column_name'];
        }
    }
    foreach ($uniqueKeys as $name => $cols2) {
        echo "  Key '$name': (" . implode(', ', $cols2) . ")\n";
    }
    $hasGoodKey = false;
    foreach ($uniqueKeys as $name => $cols2) {
        if (in_array('program_type', $cols2) && in_array('source_type', $cols2)) {
            $hasGoodKey = true;
            echo "<span style='color:#00ff88;'>  [OK]</span> Unique key includes program_type + source_type\n";
        }
    }
    if (!$hasGoodKey) {
        echo "<span style='color:#ff8c00;'>  [WARN]</span> No unique key with program_type + source_type — may cause duplicate issues\n";
    }
} catch (Exception $e) {
    echo "<span style='color:#ff4444;'>  [ERROR]</span> " . $e->getMessage() . "\n";
}

// 3. Check seedling_mutations nullable
echo "\n<b style='color:#ffd700;'>--- [3] SEEDLING_MUTATIONS NULLABLE CHECK ---</b>\n";
try {
    $cols = $db->query("SHOW COLUMNS FROM seedling_mutations")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        if (in_array($col['Field'], ['origin_location', 'target_location'])) {
            $ok = ($col['Null'] === 'YES');
            $icon = $ok ? "<span style='color:#00ff88;'>  [OK]</span>" : "<span style='color:#ff4444;'>  [NOT NULL]</span>";
            echo "$icon {$col['Field']}: {$col['Type']} — Null={$col['Null']}\n";
            if (!$ok) $errors[] = "seedling_mutations.{$col['Field']} is NOT NULL";
        }
    }
} catch (Exception $e) {
    echo "<span style='color:#ff4444;'>  [ERROR]</span> " . $e->getMessage() . "\n";
}

// 4. Test INSERT to stock (rollback)
echo "\n<b style='color:#ffd700;'>--- [4] TEST INSERT INTO STOCK (dry run) ---</b>\n";
try {
    $db->beginTransaction();
    $bpdas = $db->query("SELECT id FROM bpdas LIMIT 1")->fetch();
    $nursery = $db->query("SELECT id FROM nurseries LIMIT 1")->fetch();
    $seedlingType = $db->query("SELECT id FROM seedling_types LIMIT 1")->fetch();

    if (!$bpdas || !$seedlingType) {
        echo "  [SKIP] No test data found\n";
    } else {
        $testSql = "INSERT INTO stock (bpdas_id, nursery_id, seedling_type_id, program_type, quantity, source_type, last_update_date, notes) 
                    VALUES (?, ?, ?, 'bibitgratis', 999, 'PUB', CURDATE(), 'DEBUG - rollback test')
                    ON DUPLICATE KEY UPDATE quantity = quantity + 0";
        $stmt = $db->prepare($testSql);
        $stmt->execute([$bpdas['id'], $nursery['id'] ?? null, $seedlingType['id']]);
        echo "<span style='color:#00ff88;'>  [OK]</span> Test INSERT succeeded (rolling back — no data saved)\n";
    }
    $db->rollBack();
} catch (Exception $e) {
    $db->rollBack();
    echo "<span style='color:#ff4444;'>  [FAIL]</span> INSERT failed: " . $e->getMessage() . "\n";
    $errors[] = "Test INSERT to stock failed: " . $e->getMessage();
}

// 5. Check pub_audit_trails
echo "\n<b style='color:#ffd700;'>--- [5] PUB_AUDIT_TRAILS TABLE ---</b>\n";
try {
    $check = $db->query("SHOW TABLES LIKE 'pub_audit_trails'")->rowCount();
    if ($check > 0) {
        echo "<span style='color:#00ff88;'>  [OK]</span> pub_audit_trails EXISTS\n";
    } else {
        echo "<span style='color:#ff4444;'>  [MISSING]</span> pub_audit_trails does NOT exist\n";
        $errors[] = "Table pub_audit_trails missing";
    }
} catch (Exception $e) {
    echo "<span style='color:#ff4444;'>  [ERROR]</span> " . $e->getMessage() . "\n";
}

// 6. Check mutation_error.log
echo "\n<b style='color:#ffd700;'>--- [6] MUTATION ERROR LOG ---</b>\n";
$logFile = $appRoot . '/mutation_error.log';
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    echo "  Log file found! Last 10 lines:\n";
    $lines = array_filter(explode("\n", trim($content)));
    $last10 = array_slice($lines, -10);
    foreach ($last10 as $line) {
        echo "  <span style='color:#ff8c00;'>$line</span>\n";
    }
} else {
    echo "  No mutation_error.log found (either no errors occurred, or file write is disabled)\n";
}

// SUMMARY
echo "\n<b style='color:#00d4ff;'>=== SUMMARY ===</b>\n";
if (empty($errors)) {
    echo "<span style='color:#00ff88;font-size:14px;'>[ALL CLEAR] No structural issues found!</span>\n";
    echo "The problem may be in PHP error logs. Check your hosting error log.\n";
} else {
    echo "<span style='color:#ff4444;font-size:14px;'>[" . count($errors) . " ISSUE(S) FOUND]:</span>\n";
    foreach ($errors as $i => $err) {
        echo "  <span style='color:#ff8c00;'>" . ($i+1) . ". $err</span>\n";
    }

    echo "\n<b style='color:#ffd700;'>=== PASTE THIS SQL IN phpMyAdmin ===</b>\n";
    echo '<span style="color:#aaffaa;">';
    echo htmlspecialchars("-- Run these in phpMyAdmin on your hosting:\n\n");
    if (in_array("Missing column: stock.source_type", $errors)) {
        echo htmlspecialchars("ALTER TABLE `stock` ADD COLUMN `source_type` VARCHAR(50) NULL DEFAULT NULL;\n");
    }
    if (in_array("'bibitgratis' not in stock.program_type enum", $errors)) {
        echo htmlspecialchars("ALTER TABLE `stock` MODIFY COLUMN `program_type` ENUM('Reguler','FOLU','bibitgratis') NOT NULL DEFAULT 'Reguler';\n");
    }
    foreach ($errors as $err) {
        if (strpos($err, 'NOT NULL') !== false) {
            echo htmlspecialchars("ALTER TABLE `seedling_mutations` MODIFY `origin_location` VARCHAR(255) NULL, MODIFY `target_location` VARCHAR(255) NULL;\n");
            break;
        }
    }
    if (in_array("Table pub_audit_trails missing", $errors)) {
        echo htmlspecialchars("CREATE TABLE IF NOT EXISTS `pub_audit_trails` (id INT AUTO_INCREMENT PRIMARY KEY, transaction_type VARCHAR(100) NOT NULL, record_id INT NOT NULL, audit_data JSON NOT NULL, edited_by INT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (edited_by) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n");
    }
    echo "</span>";
}

echo "\n<span style='color:#888;'>DELETE this file after diagnosis! (pub_debug_mutation.php)</span>\n";
echo "</pre>";
