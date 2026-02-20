<?php
require_once 'config/database.php';
$db = Database::getInstance()->getConnection();

echo "Modifying requests table schema to allow NULL for seedling_type_id and quantity...<br>";

try {
    $sql = "ALTER TABLE requests MODIFY seedling_type_id INT NULL";
    $db->exec($sql);
    echo "✅ ALTER TABLE requests MODIFY seedling_type_id INT NULL - Success<br>";
    
    $sql = "ALTER TABLE requests MODIFY quantity INT NULL";
    $db->exec($sql);
    echo "✅ ALTER TABLE requests MODIFY quantity INT NULL - Success<br>";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br>Verifying new schema:<br>";
$stmt = $db->query("DESCRIBE requests");
echo "<pre>";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "</pre>";
?>
