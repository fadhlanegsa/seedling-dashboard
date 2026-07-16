<?php
/**
 * Migration: Modul PDB (Biaya Produksi Bibit) Pelaku Usaha
 * Dashboard Stok Bibit Persemaian Indonesia
 *
 * Yang dibuat:
 *  1. Menambahkan role 'pelaku_usaha' ke ENUM kolom users.role
 *  2. Tabel pdb_isian_master  (Langkah 1: Profil & Biaya Operasional)
 *  3. Tabel pdb_isian_detail  (Langkah 2: Detail per Jenis Bibit)
 *
 * Cara menjalankan (sekali saja):
 *   CLI     :  php database/migrate_pdb.php
 *   Browser :  https://<domain>/database/migrate_pdb.php   (lalu hapus/lindungi file)
 *
 * Catatan: Statement ALTER/CREATE TABLE bersifat DDL (auto-commit di MySQL),
 * sehingga tidak dibungkus transaksi — cukup dijalankan berurutan.
 */

require_once __DIR__ . '/../config/database.php';
$db = Database::getInstance()->getConnection();

try {
    // ── 1. Tambah role 'pelaku_usaha' ke ENUM users.role (idempotent) ──────────
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'role'");
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && strpos($row['Type'], 'pelaku_usaha') === false) {
        // Ambil daftar enum yang ada lalu tambahkan 'pelaku_usaha' di belakang
        preg_match("/^enum\((.*)\)$/i", $row['Type'], $m);
        $newEnums = $m[1] . ",'pelaku_usaha'";
        $db->exec("ALTER TABLE users MODIFY COLUMN role ENUM($newEnums) NOT NULL DEFAULT 'public'");
        echo "[OK] Role enum diperbarui: + 'pelaku_usaha'.\n";
    } else {
        echo "[SKIP] Role 'pelaku_usaha' sudah ada.\n";
    }

    // ── 2. Tabel Master (Langkah 1: Biaya Operasional) ─────────────────────────
    // Satu baris per (pelaku usaha, tahun). C12 dihitung otomatis oleh backend.
    $db->exec("CREATE TABLE IF NOT EXISTS pdb_isian_master (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        periode_tahun YEAR NOT NULL,
        total_biaya_a BIGINT NOT NULL DEFAULT 0            COMMENT 'Total Biaya Tetap (ATK, Honor, Mandor, dll)',
        total_biaya_b BIGINT NOT NULL DEFAULT 0            COMMENT 'Total Biaya Tidak Tetap (Media, Polybag, Pupuk, Upah, dll)',
        target_produksi_total INT NOT NULL DEFAULT 0       COMMENT 'Total batang produksi keseluruhan (pembagi)',
        biaya_produksi_per_batang_c12 DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT 'AUTO: (A+B)/target -> nilai C12',
        rincian_json TEXT NULL                             COMMENT 'Rincian item biaya A & B (untuk edit ulang)',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_user_periode (user_id, periode_tahun),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "[OK] Tabel 'pdb_isian_master' siap.\n";

    // ── 3. Tabel Detail (Langkah 2: Detail per Jenis Bibit) ────────────────────
    // Satu baris per (master, jenis bibit). Semua kolom AUTO dihitung backend.
    $db->exec("CREATE TABLE IF NOT EXISTS pdb_isian_detail (
        id INT AUTO_INCREMENT PRIMARY KEY,
        master_id INT NOT NULL,
        seedling_type_id INT NOT NULL,
        harga_benih DECIMAL(15,2) NOT NULL DEFAULT 0       COMMENT 'INPUT: Kolom C',
        berat_1000_butir DECIMAL(10,2) NOT NULL DEFAULT 0  COMMENT 'INPUT: Kolom E (gram)',
        daya_kecambah DECIMAL(5,4) NOT NULL DEFAULT 0      COMMENT 'INPUT: Kolom G (desimal 0..1)',
        bibit_jadi DECIMAL(5,4) NOT NULL DEFAULT 0         COMMENT 'INPUT: Kolom I (desimal 0..1)',
        jml_benih_per_kg INT NOT NULL DEFAULT 0            COMMENT 'AUTO: Kolom F',
        jml_benih_berkecambah INT NOT NULL DEFAULT 0       COMMENT 'AUTO: Kolom H',
        jml_bibit_jadi INT NOT NULL DEFAULT 0              COMMENT 'AUTO: Kolom J',
        harga_benih_per_butir DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT 'AUTO: Kolom K',
        harga_bibit_per_batang_final INT NOT NULL DEFAULT 0    COMMENT 'AUTO + ROUND UP 50: Kolom N/P',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_master_seedling (master_id, seedling_type_id),
        FOREIGN KEY (master_id) REFERENCES pdb_isian_master(id) ON DELETE CASCADE,
        FOREIGN KEY (seedling_type_id) REFERENCES seedling_types(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "[OK] Tabel 'pdb_isian_detail' siap.\n";

    echo "\nMigrasi modul PDB berhasil dijalankan.\n";
} catch (PDOException $e) {
    echo "Migrasi GAGAL: " . $e->getMessage() . "\n";
}
