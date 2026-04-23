<?php
$file = 'c:/xampp/htdocs/seedling-dashboard/seedling-dashboard/controllers/SeedlingEditController.php';
$content = file_get_contents($file);

// Remove checkEditWindow definition
$content = preg_replace('/(\s*\/\*\*\s*\*\s*Check if Operator is within 24 hours window\s*\*\/\s*protected function checkEditWindow\(.*?\)\s*\{.*?return true;\s*\})/s', '', $content);

// Remove all checkEditWindow calls (blocks with braces)
$content = preg_replace('/(\s*if \(\!\$this->checkEditWindow\([^)]+\)\)\s*\{[^\}]+\})/s', '', $content);
// Remove all checkEditWindow calls (single line redirects)
$content = preg_replace('/(\s*if \(\!\$this->checkEditWindow\([^)]+\)\)\s*\$this->redirect\([^)]+\);)/s', '', $content);

// Remove all isLocked blocks
$content = preg_replace('/(\s*(?:\$user = currentUser\(\);\s*)?if \([^)]*?role[^)]*?operator_persemaian[^)]*?\)\s*\{\s*\$isLocked = \$this->db->prepare\([^\;]+\;\s*\$isLocked->execute\([^\;]+\;\s*if \(\$isLocked->fetch\(\)\)\s*\{[^\}]+\}\s*\})/s', '', $content);

file_put_contents($file, $content);
echo "Locks removed successfully.";
