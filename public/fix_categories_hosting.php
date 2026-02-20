<?php
/**
 * Script ini digunakan untuk memperbaiki nama kategori di database
 * agar sesuai dengan konfigurasi terbaru di web hosting.
 * 
 * CARA PAKAI:
 * 1. Upload file ini ke folder 'public' di hosting Anda
 * 2. Buka di browser: nama-website-anda.com/fix_categories_hosting.php
 * 3. Hapus file ini setelah selesai!
 */

// Load konfigurasi utama untuk koneksi database
require_once __DIR__ . '/../config/config.php';

// CSS sederhana untuk tampilan
echo '<style>
    body { font-family: monospace; padding: 20px; line-height: 1.5; }
    .box { background: #f5f5f5; padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px; }
    .success { color: green; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; max-width: 600px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #eee; }
</style>';

echo "<h1>Perbaikan Kategori Database</h1>";

try {
    // 1. Koneksi Database
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='box success'>Koneksi Database Berhasil!</div>";

    // 2. Cek Kondisi Sebelum Perbaikan
    echo "<h3>1. Kondisi Kategori Saat Ini (Sebelum Fix)</h3>";
    showCategoryDistribution($db);

    // 3. Lakukan Update / Mapping
    echo "<h3>2. Melakukan Update Data...</h3>";
    echo "<div class='box'>";
    
    // Mapping Lama -> Baru
    $updates = [
        'Pohon Hutan' => 'Tanaman Kayu-Kayuan',
        'Pohon Buah'  => 'HHBK', // Hasil Hutan Bukan Kayu (biasanya buah masuk sini/agroforestry)
        'Lainnya'     => 'Estetika, Pakan, Dll'
    ];

    $countTotal = 0;
    foreach ($updates as $old => $new) {
        $stmt = $db->prepare("UPDATE seedling_types SET category = ? WHERE category = ?");
        $stmt->execute([$new, $old]);
        $count = $stmt->rowCount();
        
        if ($count > 0) {
            echo "<span class='success'>[BERHASIL]</span> Mengubah <b>$count</b> data dari '$old' menjadi '$new'<br>";
            $countTotal += $count;
        } else {
            echo "<span class='info'>[INFO]</span> Tidak ditemukan data dengan kategori lama '$old'<br>";
        }
    }
    echo "</div>";

    if ($countTotal > 0) {
        echo "<div class='box success'>Total data diperbarui: $countTotal baris.</div>";
    } else {
        echo "<div class='box info'>Tidak ada perubahan yang dilakukan (mungkin data sudah benar?).</div>";
    }

    // 4. Cek Kondisi Setelah Perbaikan
    echo "<h3>3. Kondisi Kategori Setelah Perbaikan</h3>";
    showCategoryDistribution($db);

    // 5. Validasi terhadap Config
    echo "<h3>4. Pengecekan Final vs Config</h3>";
    validateAgainstConfig($db);

} catch (Exception $e) {
    echo "<div class='box error'>TERJADI ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<p>Pastikan file config.php terbaca dengan benar dan kredensial database sesuai.</p>";
}

// === Helper Functions ===

function showCategoryDistribution($db) {
    $stmt = $db->query("SELECT category, COUNT(*) as count FROM seedling_types GROUP BY category ORDER BY count DESC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        echo "<p><i>Tabel seedling_types kosong.</i></p>";
        return;
    }

    echo "<table>";
    echo "<tr><th>Nama Kategori di Database</th><th>Jumlah Data</th></tr>";
    foreach ($results as $row) {
        $catName = $row['category'] === null ? 'NULL (Kosong)' : $row['category'];
        // Highlight jika nama kategori aneh/kosong
        $style = ($row['category'] === null || $row['category'] === '') ? 'background:#ffcccc' : '';
        
        echo "<tr style='$style'>";
        echo "<td>" . htmlspecialchars($catName) . "</td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

function validateAgainstConfig($db) {
    $validCategories = defined('SEEDLING_CATEGORIES') ? SEEDLING_CATEGORIES : [];
    
    // Ambil semua kategori distinct dari DB lagi
    $stmt = $db->query("SELECT DISTINCT category FROM seedling_types WHERE category IS NOT NULL AND category != ''");
    $dbCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<div class='box'>";
    $allGood = true;
    
    if (empty($validCategories)) {
         echo "<span class='error'>WARNING: Konstanta SEEDLING_CATEGORIES tidak ditemukan di config!</span><br>";
         return;
    }

    foreach ($dbCategories as $dbCat) {
        if (!in_array($dbCat, $validCategories)) {
            echo "<span class='error'>[PERINGATAN]</span> Kategori <b>'$dbCat'</b> ada di database tapi TIDAK ADA di config program.<br>";
            echo "&nbsp;&nbsp;-> Data ini mungkin tidak akan muncul di filter.<br>";
            $allGood = false;
        }
    }

    if ($allGood) {
        echo "<span class='success'>SEMUA AMAN! Semua kategori di database dikenali oleh program.</span>";
    }
    echo "</div>";
}
?>
