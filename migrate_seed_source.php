<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Starting Seed Source Traceability Migration...\n";

    // 1. bahan_baku_transactions
    echo "1. Altering bahan_baku_transactions...\n";
    $db->exec("ALTER TABLE bahan_baku_transactions ADD COLUMN IF NOT EXISTS seed_source_id INT NULL");
    // check if constraint exists, if not add
    try {
        $db->exec("ALTER TABLE bahan_baku_transactions ADD CONSTRAINT fk_bahan_baku_seed_source FOREIGN KEY (seed_source_id) REFERENCES seed_sources(id) ON DELETE SET NULL");
    } catch (PDOException $e) {
        if ($e->getCode() !== 'HY000') { // Error 1061: Duplicate key name
            echo "   (Constraint already exists or error: " . $e->getMessage() . ")\n";
        }
    }
    echo "   Done.\n";

    // 2. seed_sowings
    echo "2. Altering seed_sowings...\n";
    $db->exec("ALTER TABLE seed_sowings ADD COLUMN IF NOT EXISTS seed_source_id INT NULL");
    try {
        $db->exec("ALTER TABLE seed_sowings ADD CONSTRAINT fk_seed_sowings_seed_source FOREIGN KEY (seed_source_id) REFERENCES seed_sources(id) ON DELETE SET NULL");
    } catch (PDOException $e) {
        if ($e->getCode() !== 'HY000') {
            echo "   (Constraint already exists or error: " . $e->getMessage() . ")\n";
        }
    }
    echo "   Done.\n";

    // 3. seedling_weanings
    echo "3. Altering seedling_weanings...\n";
    $db->exec("ALTER TABLE seedling_weanings ADD COLUMN IF NOT EXISTS seed_source_id INT NULL");
    try {
        $db->exec("ALTER TABLE seedling_weanings ADD CONSTRAINT fk_weanings_seed_source FOREIGN KEY (seed_source_id) REFERENCES seed_sources(id) ON DELETE SET NULL");
    } catch (PDOException $e) {
        if ($e->getCode() !== 'HY000') {
            echo "   (Constraint already exists or error: " . $e->getMessage() . ")\n";
        }
    }
    echo "   Done.\n";

    echo "Migration completed successfully!\n";
} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage() . "\n");
}
