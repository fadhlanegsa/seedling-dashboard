<?php
// Script untuk mengecek struktur database requests
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

echo "<h2>Pengecekan Struktur Tabel Requests</h2>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Check requests table
    echo "<h3>Tabel: requests</h3>";
    $stmt = $db->query("DESCRIBE requests");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    $hasNurseryId = false;
    $hasProposal = false;
    foreach ($columns as $col) {
        echo "<tr>";
        foreach ($col as $val) echo "<td>$val</td>";
        echo "</tr>";
        if ($col['Field'] == 'nursery_id') $hasNurseryId = true;
        if ($col['Field'] == 'proposal_file_path') $hasProposal = true;
    }
    echo "</table>";
    
    if (!$hasNurseryId) echo "<p style='color:red'>⚠️ Kolom 'nursery_id' HILANG di tabel requests!</p>";
    if (!$hasProposal) echo "<p style='color:red'>⚠️ Kolom 'proposal_file_path' HILANG di tabel requests!</p>";

    // Check request_items table
    echo "<h3>Tabel: request_items</h3>";
    try {
        $stmt = $db->query("DESCRIBE request_items");
        $itemsCols = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Field</th><th>Type</th></tr>";
        foreach ($itemsCols as $col) {
            echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<p style='color:red'>❌ Tabel 'request_items' BELUM ADA!</p>";
    }

    // Check request_history table
    echo "<h3>Tabel: request_history</h3>";
    try {
        $stmt = $db->query("DESCRIBE request_history");
        echo "<p style='color:green'>✅ Tabel 'request_history' sudah ada.</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>❌ Tabel 'request_history' BELUM ADA!</p>";
    }

} catch (Exception $e) {
    echo "<p style='color:red'>Error DB: " . $e->getMessage() . "</p>";
}
?>
