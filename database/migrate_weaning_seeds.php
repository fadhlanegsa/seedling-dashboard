<?php
$host = 'localhost';
$db = 'wast6986_db_bibit';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Starting migration for Weaning Direct Seed...\n";

    // 1. Create junction table for direct seed weaning
    $sqlCreateTable = "
    CREATE TABLE IF NOT EXISTS seedling_weaning_seeds (
        id INT AUTO_INCREMENT PRIMARY KEY,
        weaning_id INT NOT NULL,
        item_id INT NOT NULL,
        quantity DECIMAL(15,2) NOT NULL,
        FOREIGN KEY (weaning_id) REFERENCES seedling_weanings(id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES bahan_baku_master(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    $pdo->exec($sqlCreateTable);
    echo "- Table 'seedling_weaning_seeds' checked/created successfully.\n";

    // 2. Modify harvest_id in seedling_weanings to allow NULL
    $sqlAlterTable = "
    ALTER TABLE seedling_weanings 
    MODIFY harvest_id INT NULL;
    ";
    
    $pdo->exec($sqlAlterTable);
    echo "- Column 'harvest_id' in 'seedling_weanings' modified to allow NULL successfully.\n";

    echo "Migration completed successfully!\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}

