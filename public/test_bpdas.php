<?php
/**
 * Test BPDASController Loading
 * Upload ke: public_html/bibitgratis.com/seedling-dashboard/public/test_bpdas.php
 * Akses: https://bibitgratis.com/seedling-dashboard/public/test_bpdas.php
 * 
 * HAPUS FILE INI SETELAH TESTING!
 */

// Define paths
define('APP_PATH', dirname(__DIR__) . '/');
define('CORE_PATH', APP_PATH . 'core/');
define('CONTROLLERS_PATH', APP_PATH . 'controllers/');

?>
<!DOCTYPE html>
<html>
<head>
    <title>BPDAS Controller Test</title>
    <style>
        body { font-family: monospace; background: #1a1a1a; color: #00ff00; padding: 20px; }
        .ok { color: #00ff00; }
        .error { color: #ff0000; }
        .warning { color: #ffaa00; }
        h1 { border-bottom: 2px solid #00ff00; padding-bottom: 10px; }
        .box { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #00ff00; }
    </style>
</head>
<body>
    <h1>🔧 BPDAS Controller Test</h1>
    
    <div class="box">
        <h2>📁 Path Information</h2>
        <?php
        echo "APP_PATH: " . APP_PATH . "<br>";
        echo "CORE_PATH: " . CORE_PATH . "<br>";
        echo "CONTROLLERS_PATH: " . CONTROLLERS_PATH . "<br>";
        ?>
    </div>

    <div class="box">
        <h2>📄 File Existence Check</h2>
        <?php
        $controllerFile = CONTROLLERS_PATH . 'BPDASController.php';
        $coreFile = CORE_PATH . 'Controller.php';
        
        if (file_exists($coreFile)) {
            echo "<span class='ok'>✓ Controller.php EXISTS</span><br>";
            echo "Size: " . filesize($coreFile) . " bytes<br>";
        } else {
            echo "<span class='error'>✗ Controller.php MISSING!</span><br>";
        }
        
        echo "<br>";
        
        if (file_exists($controllerFile)) {
            echo "<span class='ok'>✓ BPDASController.php EXISTS</span><br>";
            $size = filesize($controllerFile);
            echo "Size: " . $size . " bytes<br>";
            echo "Modified: " . date('Y-m-d H:i:s', filemtime($controllerFile)) . "<br>";
            
            if ($size == 25626 || $size == 25631) {
                echo "<span class='ok'>✓ File size CORRECT!</span><br>";
            } else {
                echo "<span class='error'>✗ File size WRONG! Expected: 25626-25631 bytes</span><br>";
            }
        } else {
            echo "<span class='error'>✗ BPDASController.php MISSING!</span><br>";
            echo "<span class='error'>PATH: $controllerFile</span><br>";
        }
        ?>
    </div>

    <div class="box">
        <h2>🔌 Controller Loading Test</h2>
        <?php
        try {
            if (file_exists($coreFile) && file_exists($controllerFile)) {
                require_once $coreFile;
                require_once $controllerFile;
                
                if (class_exists('BPDASController')) {
                    echo "<span class='ok'>✓ BPDASController class loaded successfully!</span><br><br>";
                    
                    $reflection = new ReflectionClass('BPDASController');
                    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
                    
                    echo "Public methods found: " . count($methods) . "<br><br>";
                    echo "<strong>Methods list:</strong><br>";
                    echo "<ul>";
                    foreach ($methods as $method) {
                        if ($method->class === 'BPDASController') {
                            echo "<li>" . $method->name . "()</li>";
                        }
                    }
                    echo "</ul>";
                    
                    // Check critical methods
                    $criticalMethods = ['dashboard', 'stock', 'requests', 'stockForm'];
                    echo "<br><strong>Critical methods check:</strong><br>";
                    foreach ($criticalMethods as $methodName) {
                        if ($reflection->hasMethod($methodName)) {
                            echo "<span class='ok'>✓ $methodName() exists</span><br>";
                        } else {
                            echo "<span class='error'>✗ $methodName() MISSING!</span><br>";
                        }
                    }
                    
                } else {
                    echo "<span class='error'>✗ BPDASController class NOT FOUND after include!</span><br>";
                }
            } else {
                echo "<span class='error'>✗ Required files missing, cannot test loading</span><br>";
            }
        } catch (Exception $e) {
            echo "<span class='error'>✗ ERROR: " . $e->getMessage() . "</span><br>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        ?>
    </div>

    <div class="box">
        <h2>📝 File Content Preview (First 30 lines)</h2>
        <?php
        if (file_exists($controllerFile)) {
            $lines = file($controllerFile);
            echo "<pre>";
            for ($i = 0; $i < min(30, count($lines)); $i++) {
                echo htmlspecialchars(($i + 1) . ": " . $lines[$i]);
            }
            echo "</pre>";
        } else {
            echo "<span class='error'>File not found</span>";
        }
        ?>
    </div>

    <div class="box">
        <h2>✅ Next Steps</h2>
        <?php
        if (file_exists($controllerFile) && class_exists('BPDASController')) {
            echo "<span class='ok'>✓ ALL CHECKS PASSED!</span><br><br>";
            echo "You can now:<br>";
            echo "1. Login as BPDAS user<br>";
            echo "2. Should redirect to: /bpdas/dashboard<br>";
            echo "3. Dashboard should load successfully!<br><br>";
            echo "<span class='warning'>⚠️ REMEMBER TO DELETE THIS FILE AFTER TESTING!</span>";
        } else {
            echo "<span class='error'>✗ CHECKS FAILED!</span><br><br>";
            echo "Action required:<br>";
            echo "1. Re-upload BPDASController.php<br>";
            echo "2. Verify file size: 25,626 bytes<br>";
            echo "3. Check file permissions: 644<br>";
            echo "4. Refresh this page to test again<br>";
        }
        ?>
    </div>

    <p style="margin-top: 30px;">
        <a href="auth/login" style="color: #00ffff;">Go to Login Page</a> | 
        <a href="debug.php" style="color: #00ffff;">System Debug</a>
    </p>
</body>
</html>
