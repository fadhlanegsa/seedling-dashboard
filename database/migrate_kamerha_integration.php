<?php
/**
 * Migration: Kamerha Integration Tables
 * 
 * Membuat tabel untuk:
 * 1. kamerha_qr_push_log  - Menyimpan log pengiriman QR index ke Kamerha
 * 2. kamerha_geotag_sync  - Menyimpan hasil sinkronisasi geotag dari Kamerha
 * 
 * Jalankan via browser: /seedling-dashboard/database/migrate_kamerha_integration.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();
$errors = [];
$successes = [];

// ============================================================
// 1. Tambahkan kolom kamerha_index_pushed ke tabel requests
//    untuk menandai apakah QR index sudah dipush ke Kamerha
// ============================================================
$sqls = [
    "ALTER TABLE requests 
     ADD COLUMN kamerha_index_code VARCHAR(100) NULL 
     COMMENT 'Kode indeks unik yang dipush ke Kamerha (e.g. PE-54-...-1)' 
     AFTER delivery_photo_path" => 'requests.kamerha_index_code',

    "ALTER TABLE requests 
     ADD COLUMN kamerha_push_status ENUM('pending','pushed','failed') DEFAULT 'pending' 
     COMMENT 'Status push QR index ke Kamerha' 
     AFTER kamerha_index_code" => 'requests.kamerha_push_status',

    "ALTER TABLE requests 
     ADD COLUMN kamerha_push_at TIMESTAMP NULL 
     COMMENT 'Waktu terakhir push ke Kamerha' 
     AFTER kamerha_push_status" => 'requests.kamerha_push_at',

    "ALTER TABLE requests 
     ADD COLUMN kamerha_geotag_lat DECIMAL(10,8) NULL 
     COMMENT 'Latitude dari geotag Kamerha (koordinat tanam aktual)' 
     AFTER kamerha_push_at" => 'requests.kamerha_geotag_lat',

    "ALTER TABLE requests 
     ADD COLUMN kamerha_geotag_lng DECIMAL(11,8) NULL 
     COMMENT 'Longitude dari geotag Kamerha' 
     AFTER kamerha_geotag_lat" => 'requests.kamerha_geotag_lng',

    "ALTER TABLE requests 
     ADD COLUMN kamerha_photo_url VARCHAR(512) NULL 
     COMMENT 'URL foto geotag dari Kamerha' 
     AFTER kamerha_geotag_lng" => 'requests.kamerha_photo_url',

    "ALTER TABLE requests 
     ADD COLUMN kamerha_scan_at TIMESTAMP NULL 
     COMMENT 'Waktu scan QR oleh masyarakat (dari Kamerha)' 
     AFTER kamerha_photo_url" => 'requests.kamerha_scan_at',

    "ALTER TABLE requests 
     ADD COLUMN kamerha_synced_at TIMESTAMP NULL 
     COMMENT 'Waktu sinkronisasi data dari Kamerha ke sistem kita' 
     AFTER kamerha_scan_at" => 'requests.kamerha_synced_at',
];

// Alter requests table columns
foreach ($sqls as $sql => $label) {
    try {
        $db->exec($sql);
        $successes[] = "✅ Kolom '$label' berhasil ditambahkan";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false || 
            strpos($e->getMessage(), 'already exists') !== false) {
            $successes[] = "⚠️ Kolom '$label' sudah ada (dilewati)";
        } else {
            $errors[] = "❌ Error '$label': " . $e->getMessage();
        }
    }
}

// ============================================================
// 2. Buat tabel kamerha_sync_log untuk audit trail sinkronisasi
// ============================================================
$createSyncLog = "CREATE TABLE IF NOT EXISTS kamerha_sync_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sync_type ENUM('push_qr','pull_geotag') NOT NULL COMMENT 'Jenis operasi sync',
    request_id INT NULL COMMENT 'ID permintaan bibit terkait',
    index_code VARCHAR(100) NULL COMMENT 'Kode QR index (PE-54-...-1)',
    kamerha_record_id VARCHAR(255) NULL COMMENT 'ID record di database Kamerha',
    status ENUM('success','failed','partial') NOT NULL DEFAULT 'success',
    http_code INT NULL COMMENT 'HTTP response code dari Kamerha API',
    request_payload JSON NULL COMMENT 'Payload yang dikirim ke Kamerha',
    response_body TEXT NULL COMMENT 'Response dari Kamerha API',
    error_message TEXT NULL COMMENT 'Pesan error jika gagal',
    synced_by INT NULL COMMENT 'User ID yang memicu sinkronisasi (NULL = cronjob)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sync_type (sync_type),
    INDEX idx_request_id (request_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE SET NULL,
    FOREIGN KEY (synced_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Log audit sinkronisasi data dengan Kamerha'";

try {
    $db->exec($createSyncLog);
    $successes[] = "✅ Tabel 'kamerha_sync_log' berhasil dibuat";
} catch (PDOException $e) {
    $errors[] = "❌ Error membuat tabel kamerha_sync_log: " . $e->getMessage();
}

// ============================================================
// Output hasil
// ============================================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Migrasi Kamerha Integration</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a2e; color: #eee; }
        h2 { color: #00d4ff; }
        .success { color: #00ff88; margin: 4px 0; }
        .error { color: #ff4444; margin: 4px 0; }
        .summary { margin-top: 20px; padding: 15px; background: #16213e; border-radius: 8px; }
    </style>
</head>
<body>
<h2>🚀 Migrasi Kamerha Integration</h2>
<?php
foreach ($successes as $msg) {
    echo "<p class='success'>$msg</p>";
}
foreach ($errors as $msg) {
    echo "<p class='error'>$msg</p>";
}
?>
<div class="summary">
    <p>Total sukses: <?= count($successes) ?> | Total error: <?= count($errors) ?></p>
    <?php if (empty($errors)): ?>
    <p style="color:#00ff88; font-weight:bold;">✅ Migrasi berhasil! Sistem siap integrasi dengan Kamerha.</p>
    <?php else: ?>
    <p style="color:#ff9944;">⚠️ Ada beberapa error, periksa log di atas.</p>
    <?php endif; ?>
    <p><a href="/seedling-dashboard/public/" style="color:#00d4ff;">← Kembali ke Dashboard</a></p>
</div>
</body>
</html>
