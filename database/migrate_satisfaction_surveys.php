<?php
/**
 * Migration: Create satisfaction_surveys table for Survei Kepuasan Pelanggan (PEMDI Kemenhut)
 * Run this once from browser or CLI to create the table.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Model.php';

$db = Database::getInstance()->getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS `satisfaction_surveys` (
    `id`          INT(11)      NOT NULL AUTO_INCREMENT,
    `request_id`  INT(11)      NOT NULL,
    `user_id`     INT(11)      NOT NULL,
    `rating`      TINYINT(1)   NOT NULL,
    `comment`     TEXT         DEFAULT NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_request_survey` (`request_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_rating` (`rating`),
    CONSTRAINT `fk_survey_request` FOREIGN KEY (`request_id`) REFERENCES `requests`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_survey_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `chk_survey_rating` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $db->exec($sql);
    echo "<p style='color:green;font-family:sans-serif;'>✅ Tabel <strong>satisfaction_surveys</strong> berhasil dibuat (atau sudah ada).</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;font-family:sans-serif;'>❌ Gagal membuat tabel: " . htmlspecialchars($e->getMessage()) . "</p>";
}
