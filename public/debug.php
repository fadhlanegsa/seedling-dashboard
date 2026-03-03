<?php
// Letakkan file ini di folder public/ atau di folder utama seedling-dashboard
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

echo "<h2>Pengecekan Akun Operator Persemaian</h2>";

try {
    $db = Database::getInstance()->getConnection();
    // Cari 5 user terbaru yang dibuat dari AdminPanel
    $stmt = $db->query("SELECT id, username, email, full_name, role, bpdas_id, nursery_id FROM users ORDER BY id DESC LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "<p style='color:red'>Tidak ada user sama sekali.</p>";
    } else {
        echo "<table border='1' cellpadding='10' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Username</th><th>Name</th><th>Email</th><th>Role</th><th>Nursery ID</th><th>BPDAS ID</th></tr>";
        foreach ($users as $u) {
            echo "<tr>";
            echo "<td>{$u['id']}</td>";
            echo "<td>{$u['username']}</td>";
            echo "<td>{$u['full_name']}</td>";
            echo "<td>{$u['email']}</td>";
            echo "<td>'{$u['role']}'</td>";
            echo "<td>" . var_export($u['nursery_id'], true) . "</td>";
            echo "<td>" . var_export($u['bpdas_id'], true) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error DB: " . $e->getMessage() . "</p>";
}
echo "<hr><p>Selesai. Laporkan hasil tabel ini / foto ke asisten AI.</p>";
?>
