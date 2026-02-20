<?php
require_once __DIR__ . '/../config/config.php';

try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Checking current categories in seedling_types table...\n";
    $stmt = $db->query("SELECT category, COUNT(*) as count FROM seedling_types GROUP BY category ORDER BY count DESC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo sprintf("%-30s | %s\n", "Category", "Count");
    echo str_repeat("-", 45) . "\n";

    if (empty($results)) {
        echo "No data found in seedling_types.\n";
    } else {
        foreach ($results as $row) {
             $cat = $row['category'] === null ? 'NULL' : ($row['category'] === '' ? '(Empty String)' : $row['category']);
             echo sprintf("%-30s | %d\n", $cat, $row['count']);
        }
    }

    echo "\nExpected Categories (from config):\n";
    foreach (SEEDLING_CATEGORIES as $cat) {
        echo "- $cat\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
