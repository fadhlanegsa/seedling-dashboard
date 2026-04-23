<?php
/**
 * Auto-fix for PUB Delete Feature
 */
// Handle nested or root path for config
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../config/database.php';
} else {
    require_once __DIR__ . '/../../seedling-dashboard/config/config.php';
    require_once __DIR__ . '/../../seedling-dashboard/config/database.php';
}

echo "<h1>PUB Delete Feature Fix</h1>";

try {
    $db = Database::getInstance()->getConnection();
    $db->exec("ALTER TABLE bahan_baku_master ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1 AFTER description");
    echo "<p style='color:green; font-weight:bold;'>✓ Berhasil! Kolom 'is_active' telah dipastikan ada di database.</p>";
    echo "<p>Sekarang tombol hapus lu udah bisa dipake.</p>";
    echo "<a href='../seedling-admin/master-data'>Balik ke Dashboard</a>";
} catch (Exception $e) {
    echo "<p style='color:red;'><strong>ERROR:</strong> " . $e->getMessage() . "</p>";
}
