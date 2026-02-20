<?php
// Debug form submission
session_start();
echo "<pre>";
echo "=== POST DATA ===\n";
print_r($_POST);
echo "\n=== FILES DATA ===\n";
print_r($_FILES);
echo "\n=== SESSION ===\n";
print_r($_SESSION);
echo "</pre>";
?>
