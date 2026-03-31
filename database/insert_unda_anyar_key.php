<?php
require_once __DIR__ . '/../config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cari ID BPDAS Unda Anyar
    $stmt = $pdo->query("SELECT id FROM bpdas WHERE name LIKE '%Unda Anyar%' LIMIT 1");
    $bpdas = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bpdas) {
        die("BPDAS Unda Anyar tidak ditemukan.\n");
    }
    $bpdasId = $bpdas['id'];

    // Cari ID Nursery Unda Anyar (asumsi namanya mirip)
    $stmt2 = $pdo->prepare("SELECT id FROM nurseries WHERE name LIKE '%Unda Anyar%' AND bpdas_id = ? LIMIT 1");
    $stmt2->execute([$bpdasId]);
    $nursery = $stmt2->fetch(PDO::FETCH_ASSOC);

    if (!$nursery) {
        die("Nursery Unda Anyar tidak ditemukan di bawah BPDAS ini.\n");
    }
    $nurseryId = $nursery['id'];

    // Insert the API Key
    $apiKey = "UNDA_ANYAR_SECRET_2026";
    
    // Check if it already exists to prevent duplicate error
    $checkStmt = $pdo->prepare("SELECT id FROM api_keys WHERE key_string = ?");
    $checkStmt->execute([$apiKey]);
    
    if ($checkStmt->fetch()) {
        echo "API Key $apiKey sudah ada di database.\n";
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO api_keys (key_string, bpdas_id, nursery_id, description) VALUES (?, ?, ?, ?)");
        $insertStmt->execute([
            $apiKey, 
            $bpdasId, 
            $nurseryId, 
            'API Key Spesifik untuk Persemaian Unda Anyar (Integration)'
        ]);
        
        echo "Berhasil! API Key: $apiKey telah ditambahkan untuk,\n";
        echo " - BPDAS ID: $bpdasId\n";
        echo " - Nursery ID: $nurseryId\n";
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
