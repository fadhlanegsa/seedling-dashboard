<?php
/**
 * Fix Stock Migration for Hosting
 * Script ini untuk memperbaiki data stok yang "hilang" atau 0 karena belum ada nursery_id
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Perbaikan Data Stok</h1>";
    echo "<pre>";
    
    // 1. Cek User BPDAS
    $stmt = $db->query("SELECT id, name FROM bpdas");
    $bpdasList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Ditemukan " . count($bpdasList) . " BPDAS.\n\n";
    
    foreach ($bpdasList as $bpdas) {
        $bpdasId = $bpdas['id'];
        echo "Memproses BPDAS: {$bpdas['name']} ...\n";
        
        // 2. Pastikan tabel nurseries ada DULU
        try {
            $db->query("SELECT 1 FROM nurseries LIMIT 1");
        } catch (Exception $e) {
            // Buat tabel jika belum ada
            $sql = "CREATE TABLE IF NOT EXISTS nurseries (
                id INT AUTO_INCREMENT PRIMARY KEY,
                bpdas_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                address TEXT,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $db->exec($sql);
            echo "   - Tabel nurseries dibuat.\n";
        }
        
        // 3. Buat Default Nursery jika belum ada
        $nurseryName = "Persemaian " . str_replace('BPDAS ', '', $bpdas['name']);
        
        $stmt = $db->prepare("SELECT id FROM nurseries WHERE bpdas_id = ? LIMIT 1");
        $stmt->execute([$bpdasId]);
        $nursery = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $nurseryId = null;
        if ($nursery) {
            $nurseryId = $nursery['id'];
            echo "   - Persemaian sudah ada: ID $nurseryId\n";
        } else {
            // Cek apakah tabel nurseries ada
            try {
                $db->query("SELECT 1 FROM nurseries LIMIT 1");
            } catch (Exception $e) {
                // Buat tabel jika belum ada
                $sql = "CREATE TABLE IF NOT EXISTS nurseries (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    bpdas_id INT NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    address TEXT,
                    is_active TINYINT(1) DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                $db->exec($sql);
                echo "   - Tabel nurseries dibuat.\n";
            }

            $stmt = $db->prepare("INSERT INTO nurseries (bpdas_id, name) VALUES (?, ?)");
            $stmt->execute([$bpdasId, $nurseryName]);
            $nurseryId = $db->lastInsertId();
            echo "   - Persemaian BARU dibuat: $nurseryName (ID: $nurseryId)\n";
        }
        
        // 3. Update Stock yang nursery_id nya NULL
        // Pastikan kolom nursery_id ada di tabel stock
        try {
            $db->query("SELECT nursery_id FROM stock LIMIT 1");
        } catch (Exception $e) {
            $db->exec("ALTER TABLE stock ADD COLUMN nursery_id INT NULL AFTER bpdas_id");
            echo "   - Kolom nursery_id ditambahkan ke tabel stock.\n";
        }

        $stmt = $db->prepare("UPDATE stock SET nursery_id = ? WHERE bpdas_id = ? AND (nursery_id IS NULL OR nursery_id = 0)");
        $stmt->execute([$nurseryId, $bpdasId]);
        $updated = $stmt->rowCount();
        
        if ($updated > 0) {
            echo "   - BERHASIL: $updated data stok diperbarui ke persemaian ini.\n";
        } else {
            echo "   - Tidak ada data stok yang perlu diupdate.\n";
        }
        
        echo "\n";
    }
    
    echo "<h2>Selesai! Silakan cek kembali menu stok.</h2>";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
