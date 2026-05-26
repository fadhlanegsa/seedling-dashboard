<?php
$source = 'c:/xampp/htdocs/seedling-dashboard/seedling-dashboard';
$dest = 'c:/xampp/htdocs/seedling-dashboard';

if (!is_dir($source)) {
    echo "Source directory does not exist or has already been moved.\n";
    exit;
}

echo "=== MOVING FILES FROM SUBFOLDER TO ROOT ===\n";

// Function to recursively copy or move directory contents
function moveDirContents($src, $dst) {
    $dir = opendir($src);
    if (!$dir) {
        echo "Failed to open source directory: $src\n";
        return false;
    }
    
    @mkdir($dst, 0777, true);
    
    while (($file = readdir($dir)) !== false) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $srcFile = $src . '/' . $file;
        $dstFile = $dst . '/' . $file;
        
        if (is_dir($srcFile)) {
            moveDirContents($srcFile, $dstFile);
            @rmdir($srcFile);
        } else {
            // Overwrite existing files if any
            if (file_exists($dstFile)) {
                @unlink($dstFile);
            }
            if (rename($srcFile, $dstFile)) {
                echo "Moved: $file\n";
            } else {
                // Fallback copy & unlink
                if (copy($srcFile, $dstFile)) {
                    unlink($srcFile);
                    echo "Copied & Deleted: $file\n";
                } else {
                    echo "FAILED to move: $file\n";
                }
            }
        }
    }
    closedir($dir);
    return true;
}

moveDirContents($source, $dest);

// Finally, remove the source subfolder
if (is_dir($source)) {
    @rmdir($source);
    if (is_dir($source)) {
        echo "Source folder is not empty yet. Please manually remove it after verification.\n";
    } else {
        echo "Source subfolder successfully removed!\n";
    }
}

echo "=== MOVE COMPLETE! ===\n";
