<?php
require_once __DIR__ . '/../config/config.php';

echo "Starting migration...<br>";

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Update stock table
    echo "Updating stock table...<br>";
    $conn->exec("ALTER TABLE stock ADD COLUMN program_type ENUM('Reguler', 'FOLU') NOT NULL DEFAULT 'Reguler' AFTER seedling_type_id");
    
    // Update unique constraint
    $conn->exec("ALTER TABLE stock DROP INDEX unique_stock");
    $conn->exec("ALTER TABLE stock ADD UNIQUE KEY unique_stock (nursery_id, seedling_type_id, program_type)");
    echo "Stock table updated successfully.<br>";

    // 2. Update requests table
    echo "Updating requests table...<br>";
    $conn->exec("ALTER TABLE requests ADD COLUMN program_type ENUM('Reguler', 'FOLU') NOT NULL DEFAULT 'Reguler' AFTER quantity");
    echo "Requests table updated successfully.<br>";

    // 3. Update request_items table
    echo "Updating request_items table...<br>";
    $conn->exec("ALTER TABLE request_items ADD COLUMN program_type ENUM('Reguler', 'FOLU') NOT NULL DEFAULT 'Reguler' AFTER quantity");
    echo "Request_items table updated successfully.<br>";

    echo "<b>Migration completed successfully!</b>";
} catch(PDOException $e) {
    echo "<b>Migration failed:</b> " . $e->getMessage();
}
?>
