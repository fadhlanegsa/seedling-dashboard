<?php
/**
 * Migration: Create news table for Kabar Kehutanan feature
 * Run this once from browser or CLI to create the table.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Model.php';

$db = Database::getInstance()->getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS `news` (
    `id`               INT(11)         NOT NULL AUTO_INCREMENT,
    `title`            VARCHAR(255)    NOT NULL,
    `content`          TEXT            NOT NULL,
    `image_filename`   VARCHAR(255)    DEFAULT NULL,
    `source_type`      ENUM('pusat','bpdas') NOT NULL DEFAULT 'pusat',
    `bpdas_id`         INT(11)         DEFAULT NULL,
    `author_name`      VARCHAR(100)    NOT NULL DEFAULT 'Admin',
    `published_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at`       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_source_type` (`source_type`),
    KEY `idx_bpdas_id`    (`bpdas_id`),
    KEY `idx_published_at`(`published_at`),
    CONSTRAINT `fk_news_bpdas` FOREIGN KEY (`bpdas_id`) REFERENCES `bpdas`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $db->exec($sql);
    echo "<p style='color:green;font-family:sans-serif;'>✅ Tabel <strong>news</strong> berhasil dibuat (atau sudah ada).</p>";
    echo "<p style='font-family:sans-serif;'><a href='../public/public/kabar-kehutanan'>→ Buka halaman Kabar Kehutanan</a></p>";
} catch (PDOException $e) {
    echo "<p style='color:red;font-family:sans-serif;'>❌ Gagal membuat tabel: " . htmlspecialchars($e->getMessage()) . "</p>";
}
