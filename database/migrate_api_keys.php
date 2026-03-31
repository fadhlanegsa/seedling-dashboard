<?php
/**
 * Migration Script for API Keys Table
 */

require_once __DIR__ . '/../config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Starting migration for API Keys table...\n";

    $sql = "
    CREATE TABLE IF NOT EXISTS api_keys (
        id INT AUTO_INCREMENT PRIMARY KEY,
        key_string VARCHAR(100) NOT NULL UNIQUE,
        bpdas_id INT NOT NULL,
        nursery_id INT NULL,
        description VARCHAR(255) NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (bpdas_id) REFERENCES bpdas(id) ON DELETE CASCADE,
        FOREIGN KEY (nursery_id) REFERENCES nurseries(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $pdo->exec($sql);
    echo "Table 'api_keys' created successfully.\n";

    // Insert dummy API key for testing
    // Get first BPDAS to associate
    $stmt = $pdo->query("SELECT id FROM bpdas LIMIT 1");
    $bpdas = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($bpdas) {
        $bpdasId = $bpdas['id'];
        $dummyKey = 'dummy-key-for-bpdas-' . $bpdasId;
        
        $checkStmt = $pdo->prepare("SELECT id FROM api_keys WHERE key_string = ?");
        $checkStmt->execute([$dummyKey]);
        
        if (!$checkStmt->fetch()) {
            $insertStmt = $pdo->prepare("INSERT INTO api_keys (key_string, bpdas_id, description) VALUES (?, ?, ?)");
            $insertStmt->execute([$dummyKey, $bpdasId, 'Dummy Key for Testing (All Nurseries)']);
            echo "Inserted dummy API key: $dummyKey for BPDAS ID: $bpdasId\n";
        } else {
            echo "Dummy API key already exists.\n";
        }
    }

    echo "Migration completed.\n";

} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
