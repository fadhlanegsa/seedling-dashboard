<?php
/**
 * Migration Runner: Province → BPDAS Delegation Mapping
 * 
 * Cara pakai:
 *   1. Jalankan via browser: http://localhost/seedling-dashboard/public/database/migrate_province_bpdas_delegation.php
 *      ATAU via CLI: php database/migrate_province_bpdas_delegation.php
 *   2. Setelah ALTER TABLE berhasil, catat ID BPDAS Citarum Ciliwung
 *      dari tabel yang ditampilkan, lalu jalankan UPDATE manual.
 * 
 * PERINGATAN: Jalankan hanya sekali. Hapus file ini setelah selesai.
 */

$_SERVER['HTTP_HOST']  = $_SERVER['HTTP_HOST']  ?? 'localhost';
$_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '/database/migrate_province_bpdas_delegation.php';

require_once __DIR__ . '/../config/database.php';

$db   = Database::getInstance()->getConnection();
$isCli = php_sapi_name() === 'cli';

function output(string $msg, string $type = 'info'): void {
    global $isCli;
    if ($isCli) {
        echo "[{$type}] {$msg}\n";
    } else {
        $colors = ['info' => '#17a2b8', 'success' => '#28a745', 'error' => '#dc3545', 'warn' => '#ffc107'];
        $color  = $colors[$type] ?? '#333';
        echo "<p style=\"color:{$color};font-family:monospace\">[{$type}] " . htmlspecialchars($msg) . "</p>\n";
        ob_flush(); flush();
    }
}

if (!$isCli) {
    echo "<!DOCTYPE html><html><head><title>Migration: Province BPDAS Delegation</title></head><body>";
    echo "<h2>Migration: Province → BPDAS Delegation Mapping</h2><hr>";
}

// ── Step 1: Check if column already exists ──────────────────────────────────
$checkSql = "SELECT COUNT(*) as cnt
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME   = 'provinces'
               AND COLUMN_NAME  = 'delegated_bpdas_id'";
$exists = (int)$db->query($checkSql)->fetch()['cnt'];

if ($exists > 0) {
    output("Kolom 'delegated_bpdas_id' sudah ada di tabel 'provinces'. Migration dilewati.", 'warn');
} else {
    // ── Step 2: Add the column ───────────────────────────────────────────────
    try {
        $db->exec("ALTER TABLE `provinces`
                   ADD COLUMN `delegated_bpdas_id` INT NULL DEFAULT NULL
                     COMMENT 'Jika diisi, provinsi ini pelayanannya dilimpahkan ke BPDAS yang direferensikan di sini',
                   ADD CONSTRAINT `fk_province_delegated_bpdas`
                     FOREIGN KEY (`delegated_bpdas_id`) REFERENCES `bpdas`(`id`) ON DELETE SET NULL");
        output("ALTER TABLE berhasil: kolom 'delegated_bpdas_id' ditambahkan.", 'success');

        $db->exec("CREATE INDEX `idx_delegated_bpdas` ON `provinces` (`delegated_bpdas_id`)");
        output("Index 'idx_delegated_bpdas' dibuat.", 'success');
    } catch (PDOException $e) {
        output("Gagal ALTER TABLE: " . $e->getMessage(), 'error');
        if (!$isCli) echo "</body></html>";
        exit(1);
    }
}

// ── Step 3: Show available BPDAS for admin reference ────────────────────────
output("─────────────────────────────────────────", 'info');
output("Daftar BPDAS yang tersedia (untuk seed data):", 'info');

$bpdasList = $db->query("SELECT id, name FROM bpdas WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

if ($isCli) {
    echo str_pad("ID", 6) . "| NAMA BPDAS\n";
    echo str_repeat("-", 60) . "\n";
    foreach ($bpdasList as $b) {
        echo str_pad($b['id'], 6) . "| " . $b['name'] . "\n";
    }
} else {
    echo "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;font-family:monospace'>";
    echo "<thead><tr><th>ID</th><th>Nama BPDAS</th></tr></thead><tbody>";
    foreach ($bpdasList as $b) {
        $highlight = (stripos($b['name'], 'Citarum') !== false || stripos($b['name'], 'Ciliwung') !== false)
            ? " style='background:#fff3cd;font-weight:bold'" : "";
        echo "<tr{$highlight}><td>{$b['id']}</td><td>" . htmlspecialchars($b['name']) . "</td></tr>";
    }
    echo "</tbody></table>";
}

// ── Step 4: Show current provinces without BPDAS (candidates for delegation) ─
output("─────────────────────────────────────────", 'info');
output("Provinsi yang belum punya BPDAS (kandidat untuk di-delegate):", 'warn');

$orphanSql = "SELECT p.id, p.name, p.delegated_bpdas_id,
                     b.name as delegated_to
              FROM provinces p
              LEFT JOIN bpdas own ON own.province_id = p.id AND own.is_active = 1
              LEFT JOIN bpdas b   ON b.id = p.delegated_bpdas_id
              WHERE own.id IS NULL
              ORDER BY p.name ASC";
$orphans = $db->query($orphanSql)->fetchAll();

if ($isCli) {
    echo str_pad("ID", 6) . str_pad("PROVINSI", 30) . "| DELEGATED KE\n";
    echo str_repeat("-", 70) . "\n";
    foreach ($orphans as $p) {
        echo str_pad($p['id'], 6) . str_pad($p['name'], 30) . "| " . ($p['delegated_to'] ?? '(belum diset)') . "\n";
    }
} else {
    echo "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;font-family:monospace;margin-top:10px'>";
    echo "<thead><tr><th>ID Provinsi</th><th>Nama Provinsi</th><th>Delegated Ke</th></tr></thead><tbody>";
    foreach ($orphans as $p) {
        $delegated = htmlspecialchars($p['delegated_to'] ?? '—');
        $style     = $p['delegated_bpdas_id'] ? " style='color:green'" : " style='color:red'";
        echo "<tr><td>{$p['id']}</td><td>" . htmlspecialchars($p['name']) . "</td><td{$style}>{$delegated}</td></tr>";
    }
    echo "</tbody></table>";
}

output("─────────────────────────────────────────", 'info');
output("LANGKAH SELANJUTNYA: Jalankan query UPDATE di bawah setelah mengetahui ID BPDAS yang tepat:", 'warn');
$updateExample = "UPDATE provinces SET delegated_bpdas_id = [ID_BPDAS] WHERE name IN ('Banten', 'DKI Jakarta');";
if ($isCli) {
    echo "  SQL: " . $updateExample . "\n";
} else {
    echo "<pre style='background:#f4f4f4;padding:12px;border-radius:4px;margin-top:8px'>" . htmlspecialchars($updateExample) . "</pre>";
}

output("Migration selesai.", 'success');

if (!$isCli) {
    echo "</body></html>";
}
