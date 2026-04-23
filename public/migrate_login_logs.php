<?php
// Mock HTTP_HOST for CLI or direct access if needed by config
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

// Define APP_PATH if not defined (usually defined in index.php or config.php)
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__));
}

// Include config to get database credentials
require_once APP_PATH . '/config/config.php';

try {
    // Use constants from config.php
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "CREATE TABLE IF NOT EXISTS login_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        username VARCHAR(100) NOT NULL,
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        status ENUM('success', 'failed') NOT NULL,
        login_time DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX(username),
        INDEX(status),
        INDEX(login_time)
    )";
    
    $db->exec($sql);
    echo "<h1>SUCCESS: Tabel login_logs berhasil dibuat di hosting.</h1>";
    echo "<p>Silakan segera hapus file ini demi keamanan.</p>";
} catch (PDOException $e) {
    echo "<h1>ERROR KONEKSI DATABASE</h1>";
    echo "<p>Pesan Error: " . $e->getMessage() . "</p>";
    echo "<p>Pastikan file config/config.php di hosting sudah benar.</p>";
}
