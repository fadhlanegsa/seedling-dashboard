<?php
define('APP_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();

try {
    echo "--- Menambah kolom source_type ke tabel stock ---\n";
    $db->exec("ALTER TABLE stock ADD COLUMN source_type VARCHAR(50) DEFAULT 'manual' AFTER quantity");
    echo "OK\n\n";
} catch (Exception $e) {
    echo "Info: Kolom source_type mungkin sudah ada atau error: " . $e->getMessage() . "\n\n";
}

try {
    echo "--- Membuat tabel seedling_mutations ---\n";
    $sql = "CREATE TABLE IF NOT EXISTS seedling_mutations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mutation_code VARCHAR(50) NOT NULL UNIQUE,
        mutation_date DATE NOT NULL,
        source_type ENUM('PE', 'ET') NOT NULL,
        source_id INT NOT NULL,
        mutation_type ENUM('MATI', 'NAIK KELAS', 'TRANSFER') NOT NULL,
        quantity INT NOT NULL,
        origin_location VARCHAR(255),
        target_location VARCHAR(255),
        mandor VARCHAR(150),
        manager VARCHAR(150),
        notes TEXT,
        bpdas_id INT,
        nursery_id INT NOT NULL,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (source_id),
        INDEX (mutation_type),
        INDEX (bpdas_id),
        INDEX (nursery_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $db->exec($sql);
    echo "OK\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n--- Migrasi Selesai ---\n";
