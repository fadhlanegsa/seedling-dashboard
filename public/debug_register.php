<?php
// Script untuk mengecek error saat registrasi
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

echo "<h2>Pengecekan Error Registrasi</h2>";

try {
    $db = Database::getInstance()->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $data = [
        'username' => 'test_reg_' . time(),
        'email' => 'test_reg_' . time() . '@example.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'full_name' => 'Tester Registrasi',
        'phone' => '08123456789',
        'nik' => '1234567890123456',
        'user_type' => 'kelompok',
        'role' => 'public'
    ];
    
    $columns = array_keys($data);
    $placeholders = array_fill(0, count($columns), '?');
    
    $sql = "INSERT INTO users (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
    
    $stmt = $db->prepare($sql);
    $stmt->execute(array_values($data));
    
    echo "<h3 style='color:green'>✅ Registrasi Sukses (Tanpa Error Database)!</h3>";
    echo "<p>Artinya konfigurasi tabel users sudah benar. Harusnya form registrasi normal juga bisa.</p>";
    
} catch (PDOException $e) {
    echo "<h3 style='color:red'>❌ ERROR DATABASE TERTANGKAP:</h3>";
    echo "<p><b>Pesan Error:</b> " . $e->getMessage() . "</p>";
    
    if (strpos($e->getMessage(), "Unknown column 'user_type'") !== false) {
        echo "<hr><h4 style='color:blue'>💡 DIAGNOSA SAYA:</h4>";
        echo "<p>Kolom <b>'user_type'</b> ternyata belum ada di database yang ini. Kemungkinan tadi kamu menjalankan query <code>ALTER TABLE</code> di database yang salah (kayak kasus bibit_karnaval vs db_bibit tadi), atau belum menjalankannya sama sekali.</p>";
        echo "<p><b>Solusi:</b> Buka PhpMyAdmin, pastikan pilih database yang sama persis dengan yang ada di file <code>config.php</code>, buka tab SQL, dan jalankan:<br>";
        echo "<code>ALTER TABLE users ADD COLUMN user_type ENUM('perorangan', 'kelompok') DEFAULT 'perorangan' AFTER role;</code></p>";
    }
} catch (Exception $e) {
    echo "<h3 style='color:red'>ERROR UMUM:</h3>";
    echo $e->getMessage();
}
?>
