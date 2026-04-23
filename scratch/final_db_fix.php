<?php
$databases = ['wast6986_db_bibit', 'wast6986_seedling_dashboard1'];

foreach ($databases as $dbName) {
    try {
        $db = new PDO("mysql:host=localhost;dbname=$dbName", "root", "");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $db->exec("ALTER TABLE bahan_baku_master ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1 AFTER description");
        echo "DB '$dbName': SUKSES!\n";
    } catch (Exception $e) {
        echo "DB '$dbName': Error (Mungkin nama DB beda) -> " . $e->getMessage() . "\n";
    }
}
