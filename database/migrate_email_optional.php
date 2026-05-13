<?php
/**
 * Migration: Jadikan email opsional untuk user publik
 * 
 * Masalah: Kolom email di tabel users di-set NOT NULL, 
 *          sehingga masyarakat yang tidak punya email tidak bisa daftar.
 * 
 * Solusi: Ubah kolom email menjadi NULL (opsional), 
 *         tetapi tetap UNIQUE agar tidak ada duplikat email yang sama.
 * 
 * CATATAN: Di MySQL, banyak baris boleh punya nilai NULL di kolom UNIQUE.
 *          NULL = "tidak diisi", bukan "nilai kosong yang sama".
 * 
 * Cara pakai: Akses file ini via browser di hosting sekali saja,
 *             lalu hapus file ini setelah berhasil.
 */

// Load config
define('BASE_PATH', __DIR__ . '/../');
require_once BASE_PATH . 'config/database.php';

$db = Database::getInstance()->getConnection();

echo "<h2>Migration: Email Opsional untuk User Publik</h2>";
echo "<pre style='background:#1a1a2e;color:#e0e0e0;padding:20px;border-radius:8px;font-size:14px;'>";

$steps = [];
$hasError = false;

// -------------------------------------------------------
// STEP 1: Cek kondisi kolom email saat ini
// -------------------------------------------------------
echo "🔍 Mengecek kondisi kolom 'email' saat ini...\n";

$columnInfo = $db->query("SHOW COLUMNS FROM users WHERE Field = 'email'")->fetch(PDO::FETCH_ASSOC);

if (!$columnInfo) {
    echo "❌ ERROR: Kolom 'email' tidak ditemukan di tabel users!\n";
    $hasError = true;
} else {
    echo "   Tipe    : " . $columnInfo['Type'] . "\n";
    echo "   Null    : " . $columnInfo['Null'] . "\n";
    echo "   Default : " . ($columnInfo['Default'] ?? 'NULL') . "\n";
    echo "   Key     : " . $columnInfo['Key'] . "\n\n";

    if ($columnInfo['Null'] === 'YES') {
        echo "✅ Kolom email sudah opsional (NULL diizinkan). Migration tidak diperlukan.\n";
        echo "\n⏩ Selesai — tidak ada perubahan dilakukan.\n";
        echo "</pre>";
        exit;
    }
}

if ($hasError) {
    echo "</pre>";
    exit;
}

// -------------------------------------------------------
// STEP 2: Ubah kolom email jadi NULL + pertahankan UNIQUE
// -------------------------------------------------------
echo "🔧 Mengubah kolom 'email' menjadi opsional (NULL)...\n";

try {
    // Hapus index lama (jika bernama idx_email)
    $indexes = $db->query("SHOW INDEX FROM users WHERE Key_name = 'idx_email'")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($indexes)) {
        $db->exec("ALTER TABLE users DROP INDEX idx_email");
        echo "   ✓ Index 'idx_email' lama dihapus\n";
    }

    // Cek apakah UNIQUE constraint bernama 'email' atau 'unique_email' sudah ada
    $uniqueIndexes = $db->query("SHOW INDEX FROM users WHERE Key_name = 'email'")->fetchAll(PDO::FETCH_ASSOC);
    
    // Ubah kolom: NOT NULL → NULL, pertahankan UNIQUE
    $db->exec("ALTER TABLE users MODIFY COLUMN email VARCHAR(100) NULL DEFAULT NULL");
    echo "   ✓ Kolom 'email' berhasil diubah menjadi NULL\n";

    // Pastikan unique key masih ada dengan nama standar
    $checkUnique = $db->query("SHOW INDEX FROM users WHERE Key_name = 'email' AND Non_unique = 0")->fetchAll(PDO::FETCH_ASSOC);
    if (empty($checkUnique)) {
        // Coba tambahkan unique key (mungkin hilang karena MODIFY)
        try {
            $db->exec("ALTER TABLE users ADD UNIQUE KEY unique_email (email)");
            echo "   ✓ UNIQUE constraint pada 'email' ditambahkan kembali\n";
        } catch (PDOException $ex) {
            // Sudah ada, skip
            echo "   ✓ UNIQUE constraint pada 'email' sudah ada\n";
        }
    } else {
        echo "   ✓ UNIQUE constraint pada 'email' masih ada\n";
    }

    $steps[] = "✅ STEP 2: Kolom email berhasil diubah menjadi opsional";

} catch (PDOException $e) {
    echo "   ❌ GAGAL: " . $e->getMessage() . "\n";
    $hasError = true;
}

// -------------------------------------------------------
// STEP 3: Fix user yang email-nya string kosong "" → NULL
//         (Jika ada data lama yang tersimpan sebagai "")
// -------------------------------------------------------
if (!$hasError) {
    echo "\n🔧 Membersihkan data email kosong ('') yang tersimpan sebagai string...\n";

    try {
        $stmt = $db->prepare("UPDATE users SET email = NULL WHERE email = '' OR email = ' '");
        $stmt->execute();
        $affected = $stmt->rowCount();
        echo "   ✓ {$affected} baris data email kosong dibersihkan → diubah ke NULL\n";
        $steps[] = "✅ STEP 3: Data email kosong dibersihkan ({$affected} baris)";
    } catch (PDOException $e) {
        echo "   ❌ GAGAL membersihkan data: " . $e->getMessage() . "\n";
        $hasError = true;
    }
}

// -------------------------------------------------------
// STEP 4: Verifikasi hasil akhir
// -------------------------------------------------------
echo "\n🔍 Verifikasi hasil akhir...\n";

$columnAfter = $db->query("SHOW COLUMNS FROM users WHERE Field = 'email'")->fetch(PDO::FETCH_ASSOC);
echo "   Tipe    : " . $columnAfter['Type'] . "\n";
echo "   Null    : " . $columnAfter['Null'] . "\n";
echo "   Default : " . ($columnAfter['Default'] ?? 'NULL') . "\n";
echo "   Key     : " . $columnAfter['Key'] . "\n";

if ($columnAfter['Null'] === 'YES') {
    echo "\n";
    echo "✅ Verifikasi BERHASIL — email sekarang opsional!\n";
} else {
    echo "\n";
    echo "⚠️  Verifikasi GAGAL — kolom masih NOT NULL, coba manual.\n";
    $hasError = true;
}

// -------------------------------------------------------
// Ringkasan
// -------------------------------------------------------
echo "\n" . str_repeat("─", 50) . "\n";
echo "📋 RINGKASAN:\n\n";
foreach ($steps as $step) {
    echo "   {$step}\n";
}

if ($hasError) {
    echo "\n⚠️  Ada masalah dalam proses migration.\n";
    echo "   Silakan jalankan SQL manual berikut di phpMyAdmin:\n\n";
    echo "   ALTER TABLE users MODIFY COLUMN email VARCHAR(100) NULL DEFAULT NULL;\n";
    echo "   UPDATE users SET email = NULL WHERE email = '' OR email = ' ';\n";
} else {
    echo "\n🎉 MIGRATION SELESAI!\n";
    echo "\n⚠️  PENTING: Hapus file ini setelah migration selesai!\n";
    echo "   Path: " . __FILE__ . "\n";
}

echo "</pre>";

echo "<hr>";
echo "<p><strong>SQL yang dieksekusi (untuk referensi):</strong></p>";
echo "<pre style='background:#f5f5f5;padding:15px;border-radius:4px;'>";
echo "-- Ubah email menjadi opsional (NULL)\n";
echo "ALTER TABLE users MODIFY COLUMN email VARCHAR(100) NULL DEFAULT NULL;\n\n";
echo "-- Bersihkan data email kosong lama\n";
echo "UPDATE users SET email = NULL WHERE email = '' OR email = ' ';\n";
echo "</pre>";
