-- ============================================================
-- MIGRATION: Province → BPDAS Delegation Mapping
-- Deskripsi : Menambah kolom delegated_bpdas_id di tabel provinces
--             agar provinsi yang tidak punya BPDAS sendiri
--             (misal Banten, DKI Jakarta) bisa diarahkan ke
--             BPDAS lain (misal BPDAS Citarum Ciliwung).
-- Dibuat    : 2026-07-02
-- ============================================================

ALTER TABLE `provinces`
ADD COLUMN `delegated_bpdas_id` INT NULL DEFAULT NULL
  COMMENT 'Jika diisi, provinsi ini tidak punya BPDAS sendiri dan pelayanannya dilimpahkan ke BPDAS yang direferensikan di sini',
ADD CONSTRAINT `fk_province_delegated_bpdas`
  FOREIGN KEY (`delegated_bpdas_id`) REFERENCES `bpdas`(`id`) ON DELETE SET NULL;

CREATE INDEX `idx_delegated_bpdas` ON `provinces` (`delegated_bpdas_id`);

-- ============================================================
-- SEED DATA (jalankan setelah ALTER TABLE di atas berhasil)
-- ============================================================
-- Langkah 1: Cari ID BPDAS Citarum Ciliwung di database Anda:
--   SELECT id, name FROM bpdas WHERE name LIKE '%Citarum%' OR name LIKE '%Ciliwung%';
--
-- Langkah 2: Ganti angka 999 di bawah dengan ID yang ditemukan,
--            lalu uncomment dan jalankan:
--
-- UPDATE `provinces`
-- SET `delegated_bpdas_id` = 999  -- ganti 999 dengan ID BPDAS Citarum Ciliwung
-- WHERE `name` IN ('Banten', 'DKI Jakarta');
--
-- Langkah 3: Verifikasi hasilnya:
--   SELECT id, name, delegated_bpdas_id FROM provinces
--   WHERE delegated_bpdas_id IS NOT NULL;
-- ============================================================
