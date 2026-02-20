<?php
/**
 * FINAL FIX CATEGORIES SCRIPT
 * 1. Mengubah tipe kolom category dari ENUM menjadi VARCHAR (supaya bisa simpan nama bebas)
 * 2. Mengisi kategori yang kosong akibat error ENUM sebelumnya
 */

require_once __DIR__ . '/../config/config.php';

echo '<style>body{font-family:sans-serif;padding:20px;line-height:1.6}.box{padding:15px;margin:10px 0;border-left:4px solid #eee;background:#f9f9f9}.success{border-left-color:green}.info{border-left-color:blue}.error{border-left-color:red}</style>';
echo "<h1>Final Category Repair</h1>";

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<div class='box info'>Connecting to database... OK</div>";

    // 1. UBAH STRUKTUR TABEL (PENTING!)
    // Ubah dari ENUM ke VARCHAR agar bisa menampung nama kategori baru
    echo "<h3>1. Memperbaiki Struktur Tabel</h3>";
    try {
        $sql = "ALTER TABLE seedling_types MODIFY category VARCHAR(100) DEFAULT NULL";
        $db->exec($sql);
        echo "<div class='box success'>[SUKSES] Kolom 'category' diubah menjadi VARCHAR. Sekarang bisa menerima nama kategori baru.</div>";
    } catch (PDOException $e) {
        echo "<div class='box error'>[SKIP/ERROR] Gagal ubah struktur (mungkin sudah varchar?): " . $e->getMessage() . "</div>";
    }

    // 2. DATA RESTORATION
    echo "<h3>2. Memulihkan Data Kategori</h3>";
    
    // A. Set Default untuk yang Kosong -> Tanaman Kayu-Kayuan
    $sql = "UPDATE seedling_types SET category = 'Tanaman Kayu-Kayuan' WHERE category = '' OR category IS NULL OR category = 'Pohon Hutan'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    echo "<div class='box success'>Set <b>" . $stmt->rowCount() . "</b> data kosong menjadi 'Tanaman Kayu-Kayuan'</div>";

    // B. Deteksi Buah-buahan -> HHBK
    $fruits = [
        'Durian', 'Alpukat', 'Mangga', 'Rambutan', 'Jambu', 'Jeruk', 
        'Kelengkeng', 'Manggis', 'Sirsak', 'Nangka', 'Pete', 'Petai', 'Jengkol', 
        'Aren', 'Kopi', 'Kemiri', 'Duku', 'Langsat', 'Cempedak', 'Matoa'
    ];
    
    $countFruit = 0;
    foreach ($fruits as $fruit) {
        $sql = "UPDATE seedling_types SET category = 'HHBK' WHERE name LIKE ? AND category = 'Tanaman Kayu-Kayuan'";
        $stmt = $db->prepare($sql);
        $stmt->execute(["%$fruit%"]);
        $countFruit += $stmt->rowCount();
    }
    echo "<div class='box success'>Deteksi automatis <b>$countFruit</b> Tanaman Buah/HHBK (Durian, Alpukat, dll).</div>";

    // C. Lainnya
    $sql = "UPDATE seedling_types SET category = 'Estetika, Pakan, Dll' WHERE category = 'Lainnya'";
    $db->exec($sql);

    // 3. SHOW RESULT
    echo "<h3>3. Hasil Akhir (Grouping)</h3>";
    $stmt = $db->query("SELECT category, COUNT(*) as c FROM seedling_types GROUP BY category");
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>" . ($row['category'] ?: 'NULL') . ": <b>" . $row['c'] . "</b> data</li>";
    }
    echo "</ul>";

    echo "<div class='box info'>Silakan cek aplikasi Anda sekarang. Seharusnya kategori sudah muncul.</div>";

} catch (Exception $e) {
    echo "<div class='box error'>ERROR UTAMA: " . $e->getMessage() . "</div>";
}
?>
