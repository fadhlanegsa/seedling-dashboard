<?php
$dsn = "mysql:host=localhost;dbname=wast6986_db_bibit;charset=utf8mb4";
try {
    $db = new PDO($dsn, 'root', '');
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
    echo "SUCCESS";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
