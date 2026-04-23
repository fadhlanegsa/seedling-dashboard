<?php
// Mock server variables for CLI execution
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/scratch/migrate_soft_delete.php';

require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Tambah kolom is_active jika belum ada
    $check = $db->query("SHOW COLUMNS FROM bahan_baku_master LIKE 'is_active'")->fetch();
    if (!$check) {
        $db->exec("ALTER TABLE bahan_baku_master ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER description");
        echo "Kolom is_active berhasil ditambahkan.\n";
    } else {
        echo "Kolom is_active sudah ada.\n";
    }

    echo "Migrasi Selesai.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
