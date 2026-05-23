<?php
/**
 * Migration: PUB Program Segregation
 * Splits NAIK KELAS into 3 program-specific mutation types:
 *   - NAIK KELAS (REGULER)
 *   - NAIK KELAS (FOLU)
 *   - NAIK KELAS (RHL)
 * 
 * Also adds 'RHL' to stock.program_type ENUM if missing.
 * Migrates old 'NAIK KELAS' records to 'NAIK KELAS (REGULER)'.
 */

require_once __DIR__ . '/../config/database.php';

echo "=== Migration: PUB Program Segregation ===\n\n";

try {
    $db = Database::getInstance()->getConnection();

    // ============================================================
    // STEP 1: Alter seedling_mutations.mutation_type ENUM
    // ============================================================
    echo "[1/4] Altering seedling_mutations.mutation_type ENUM...\n";

    $db->exec("ALTER TABLE seedling_mutations 
               MODIFY COLUMN mutation_type ENUM(
                   'MATI', 
                   'NAIK KELAS', 
                   'NAIK KELAS (REGULER)', 
                   'NAIK KELAS (FOLU)', 
                   'NAIK KELAS (RHL)', 
                   'TRANSFER'
               ) NOT NULL");

    echo "  [OK] ENUM updated successfully.\n\n";

    // ============================================================
    // STEP 2: Migrate old 'NAIK KELAS' → 'NAIK KELAS (REGULER)'
    // ============================================================
    echo "[2/4] Migrating old 'NAIK KELAS' records to 'NAIK KELAS (REGULER)'...\n";

    $stmt = $db->prepare("UPDATE seedling_mutations SET mutation_type = 'NAIK KELAS (REGULER)' WHERE mutation_type = 'NAIK KELAS'");
    $stmt->execute();
    $affected = $stmt->rowCount();

    echo "  [OK] Migrated {$affected} records.\n\n";

    // ============================================================
    // STEP 3: Ensure stock.program_type ENUM has 'RHL'
    // ============================================================
    echo "[3/4] Checking stock.program_type ENUM for 'RHL'...\n";

    $colInfo = $db->query("SHOW COLUMNS FROM stock LIKE 'program_type'")->fetch(PDO::FETCH_ASSOC);
    
    if ($colInfo) {
        $currentType = $colInfo['Type'];
        echo "  Current ENUM: {$currentType}\n";

        if (strpos($currentType, 'RHL') === false) {
            // Add RHL to the ENUM
            // Parse existing values and add RHL
            preg_match_all("/'([^']+)'/", $currentType, $matches);
            $existingValues = $matches[1] ?? [];

            if (!in_array('RHL', $existingValues)) {
                $existingValues[] = 'RHL';
            }

            $newEnum = "ENUM('" . implode("','", $existingValues) . "')";
            $db->exec("ALTER TABLE stock MODIFY COLUMN program_type {$newEnum} NOT NULL DEFAULT 'Reguler'");
            echo "  [OK] Added 'RHL' to stock.program_type ENUM.\n";
        } else {
            echo "  [SKIP] 'RHL' already exists in ENUM.\n";
        }
    } else {
        echo "  [WARN] program_type column not found in stock table.\n";
    }

    echo "\n";

    // ============================================================
    // STEP 4: Remove old 'NAIK KELAS' from ENUM (clean up)
    // ============================================================
    echo "[4/4] Removing legacy 'NAIK KELAS' from ENUM (all records already migrated)...\n";

    // Verify no records remain with old value
    $remaining = $db->query("SELECT COUNT(*) FROM seedling_mutations WHERE mutation_type = 'NAIK KELAS'")->fetchColumn();

    if ((int)$remaining === 0) {
        $db->exec("ALTER TABLE seedling_mutations 
                   MODIFY COLUMN mutation_type ENUM(
                       'MATI', 
                       'NAIK KELAS (REGULER)', 
                       'NAIK KELAS (FOLU)', 
                       'NAIK KELAS (RHL)', 
                       'TRANSFER'
                   ) NOT NULL");
        echo "  [OK] Legacy 'NAIK KELAS' removed from ENUM.\n";
    } else {
        echo "  [SKIP] {$remaining} records still have 'NAIK KELAS'. Keeping in ENUM for safety.\n";
    }

    echo "\n=== Migration Complete! ===\n";

} catch (PDOException $e) {
    echo "\n[ERROR] Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
