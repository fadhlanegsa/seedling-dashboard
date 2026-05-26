<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Starting Seed Source Traceability Migration...\n";

    function addColumnIfNotExists($db, $table, $column, $definition) {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        if ($stmt->rowCount() == 0) {
            $db->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
            echo "   Column $column added to $table.\n";
        } else {
            echo "   Column $column already exists in $table.\n";
        }
    }

    echo "1. Altering bahan_baku_transactions...\n";
    addColumnIfNotExists($db, 'bahan_baku_transactions', 'seed_source_id', 'INT NULL');
    try {
        $db->exec("ALTER TABLE bahan_baku_transactions ADD CONSTRAINT fk_bahan_baku_seed_source FOREIGN KEY (seed_source_id) REFERENCES seed_sources(id) ON DELETE SET NULL");
    } catch (PDOException $e) {
        if ($e->getCode() !== 'HY000') {
            echo "   (Constraint already exists or error: " . $e->getMessage() . ")\n";
        }
    }
    echo "   Done.\n";

    echo "2. Altering seed_sowings...\n";
    addColumnIfNotExists($db, 'seed_sowings', 'seed_source_id', 'INT NULL');
    try {
        $db->exec("ALTER TABLE seed_sowings ADD CONSTRAINT fk_seed_sowings_seed_source FOREIGN KEY (seed_source_id) REFERENCES seed_sources(id) ON DELETE SET NULL");
    } catch (PDOException $e) {
        if ($e->getCode() !== 'HY000') {
            echo "   (Constraint already exists or error: " . $e->getMessage() . ")\n";
        }
    }
    echo "   Done.\n";

    echo "3. Altering seedling_weanings...\n";
    addColumnIfNotExists($db, 'seedling_weanings', 'seed_source_id', 'INT NULL');
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
