-- ============================================================
-- MIGRATION: Propagate program_type (RHL/Reguler) dari hulu ke hilir
-- Tanggal  : 2026-06-11
-- Issue    : Bug laporan Persemaian Sukosari - data RHL & Reguler merge
-- ============================================================

-- Jalankan dengan: mysql -u root wast6986_db_bibit < migrate_program_type_propagation.sql

SET @migration_name = 'migrate_program_type_propagation';

-- ============================================================
-- STEP 1: DDL — Tambah kolom program_type di 4 tabel produksi
-- ============================================================

-- 1a. bahan_baku_transactions (Bahan Baku IN)
ALTER TABLE `bahan_baku_transactions`
  ADD COLUMN IF NOT EXISTS `program_type`
  ENUM('Reguler','RHL','FOLU','bibitgratis') NOT NULL DEFAULT 'Reguler'
  COMMENT 'Program asal bahan baku: Reguler atau RHL/FOLU/bibitgratis'
  AFTER `seed_source_id`;

-- 1b. seed_sowings (Penaburan Benih)
ALTER TABLE `seed_sowings`
  ADD COLUMN IF NOT EXISTS `program_type`
  ENUM('Reguler','RHL','FOLU','bibitgratis') NOT NULL DEFAULT 'Reguler'
  COMMENT 'Program penaburan: diambil dari bahan baku benih yang dipakai'
  AFTER `seed_source_id`;

-- 1c. seedling_harvests (Pemanenan Semai)
ALTER TABLE `seedling_harvests`
  ADD COLUMN IF NOT EXISTS `program_type`
  ENUM('Reguler','RHL','FOLU','bibitgratis') NOT NULL DEFAULT 'Reguler'
  COMMENT 'Program panen: propagasi dari sowing'
  AFTER `nursery_id`;

-- 1d. seedling_weanings (Penyapihan / PE)
ALTER TABLE `seedling_weanings`
  ADD COLUMN IF NOT EXISTS `program_type`
  ENUM('Reguler','RHL','FOLU','bibitgratis') NOT NULL DEFAULT 'Reguler'
  COMMENT 'Program penyapihan: propagasi dari harvest'
  AFTER `seed_source_id`;

-- ============================================================
-- STEP 2: Backfill data lama dari chain sowing -> harvest -> weaning
-- (data yang sudah ada sebelum migrasi ini dijalankan)
-- Semua data lama tidak punya info program_type, default ke 'Reguler'
-- Tapi jika ada data yang bisa ditelusuri dari mutation_type, kita ambil itu
-- ============================================================

-- Backfill seed_sowings.program_type dari weaning -> mutation chain
-- Jika ada mutasi NAIK KELAS (RHL) yang terhubung ke sowing ini, update jadi RHL
UPDATE seed_sowings s
INNER JOIN seedling_harvests h ON h.sowing_id = s.id
INNER JOIN seedling_weanings w ON w.harvest_id = h.id
INNER JOIN seedling_mutations m ON m.source_id = w.id AND m.source_type = 'PE'
SET s.program_type = 'RHL'
WHERE m.mutation_type = 'NAIK KELAS (RHL)'
  AND s.program_type = 'Reguler';

-- Backfill seedling_harvests dari linked sowing
UPDATE seedling_harvests h
INNER JOIN seed_sowings s ON s.id = h.sowing_id
SET h.program_type = s.program_type
WHERE h.program_type = 'Reguler' AND s.program_type != 'Reguler';

-- Backfill seedling_weanings dari linked harvest
UPDATE seedling_weanings w
INNER JOIN seedling_harvests h ON h.id = w.harvest_id
SET w.program_type = h.program_type
WHERE w.program_type = 'Reguler' AND h.program_type != 'Reguler';

-- ============================================================
-- STEP 3: DATA CLEANUP Bug #1 — Hapus duplikasi master item
-- Persemaian Sukosari punya item dengan nama identik tapi ID berbeda
-- ============================================================

-- 3a. Benih Alpukat: ID 261 (kode A-012) adalah duplikat dari ID 190 (kode A4)
--     Pindahkan semua transaksi yang pakai ID 261 → pakai ID 190
UPDATE `bahan_baku_transactions`
SET `item_id` = 190
WHERE `item_id` = 261;

UPDATE `seed_sowings`
SET `seed_item_id` = 190
WHERE `seed_item_id` = 261;

-- 3b. Benih Durian: ID 191 (kode A5) adalah duplikat dari ID 6 (kode 1.07)
--     Pindahkan semua transaksi yang pakai ID 191 → pakai ID 6
UPDATE `bahan_baku_transactions`
SET `item_id` = 6
WHERE `item_id` = 191;

UPDATE `seed_sowings`
SET `seed_item_id` = 6
WHERE `seed_item_id` = 191;

-- 3c. Non-aktifkan item master duplikat agar tidak muncul di dropdown
UPDATE `bahan_baku_master`
SET `is_active` = 0,
    `description` = CONCAT(COALESCE(description, ''), ' [DEPRECATED: Merged ke ID canonical - see migration 2026-06-11]')
WHERE `id` IN (261, 191);

-- ============================================================
-- STEP 4: Verifikasi (SELECT only, tidak mengubah data)
-- ============================================================

-- Cek kolom baru terbentuk
SELECT
  TABLE_NAME,
  COLUMN_NAME,
  COLUMN_TYPE,
  COLUMN_DEFAULT,
  IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME = 'program_type'
  AND TABLE_NAME IN ('bahan_baku_transactions','seed_sowings','seedling_harvests','seedling_weanings','stock')
ORDER BY TABLE_NAME;

-- Cek item duplikat sudah non-aktif
SELECT id, code, name, is_active, description
FROM bahan_baku_master
WHERE id IN (190, 191, 261, 6);

-- Cek transaksi Sukosari sudah bersih (tidak ada lagi pakai ID 261 atau 191)
SELECT 'bahan_baku_transactions' as tbl, COUNT(*) as remaining_dup
FROM bahan_baku_transactions WHERE item_id IN (261, 191)
UNION ALL
SELECT 'seed_sowings', COUNT(*)
FROM seed_sowings WHERE seed_item_id IN (261, 191);

-- Sukses!
SELECT 'Migration migrate_program_type_propagation COMPLETED' AS status;
