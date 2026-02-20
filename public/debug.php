<?php
/**
 * DEBUG SCRIPT - Check System Status
 * Upload ke public_html/bibitgratis.com/seedling-dashboard/public/
 * Akses: https://bibitgratis.com/seedling-dashboard/public/debug.php
 * 
 * HAPUS FILE INI SETELAH SELESAI DEBUG!
 */

// Start session
session_start();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Debug Info</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Courier New', monospace; 
            background: #1a1a1a; 
            color: #00ff00; 
            padding: 20px; 
        }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #00ff00; margin-bottom: 20px; border-bottom: 2px solid #00ff00; padding-bottom: 10px; }
        h2 { color: #ffff00; margin-top: 30px; margin-bottom: 15px; font-size: 18px; }
        .section { 
            background: #2a2a2a; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-left: 4px solid #00ff00; 
            border-radius: 4px;
        }
        .ok { color: #00ff00; }
        .error { color: #ff0000; font-weight: bold; }
        .warning { color: #ffaa00; }
        .info { color: #00aaff; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #444; }
        th { color: #ffff00; }
        code { 
            background: #1a1a1a; 
            padding: 2px 6px; 
            border-radius: 3px; 
            color: #00ffff;
        }
        .path { color: #ff00ff; }
        pre { 
            background: #1a1a1a; 
            padding: 10px; 
            overflow-x: auto; 
            border-radius: 4px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 System Debug Information</h1>
        <p class="warning">⚠️ HAPUS FILE INI SETELAH DEBUGGING!</p>

        <?php
        // Get base paths
        $scriptPath = __DIR__;
        $rootPath = dirname($scriptPath);
        
        echo "<div class='section'>";
        echo "<h2>📁 Path Information</h2>";
        echo "<table>";
        echo "<tr><th>Variable</th><th>Value</th></tr>";
        echo "<tr><td>__DIR__</td><td class='path'>" . $scriptPath . "</td></tr>";
        echo "<tr><td>Root Path</td><td class='path'>" . $rootPath . "</td></tr>";
        echo "<tr><td>Document Root</td><td class='path'>" . $_SERVER['DOCUMENT_ROOT'] . "</td></tr>";
        echo "</table>";
        echo "</div>";

        // Check PHP version
        echo "<div class='section'>";
        echo "<h2>⚙️ PHP Configuration</h2>";
        echo "<table>";
        echo "<tr><th>Setting</th><th>Value</th></tr>";
        echo "<tr><td>PHP Version</td><td class='info'>" . PHP_VERSION . "</td></tr>";
        echo "<tr><td>Server Software</td><td>" . $_SERVER['SERVER_SOFTWARE'] . "</td></tr>";
        echo "<tr><td>Display Errors</td><td>" . (ini_get('display_errors') ? 'On' : 'Off') . "</td></tr>";
        echo "<tr><td>Error Reporting</td><td>" . error_reporting() . "</td></tr>";
        echo "</table>";
        echo "</div>";

        // Check critical files
        echo "<div class='section'>";
        echo "<h2>📄 File Existence Check</h2>";
        $files = [
            'config/config.php',
            'controllers/BPDASController.php',
            'controllers/AuthController.php',
            'controllers/PublicController.php',
            'controllers/AdminController.php',
            'controllers/ErrorController.php',
            'core/Controller.php',
            'core/Model.php',
            'core/View.php',
            'public/index.php',
            'public/.htaccess'
        ];
        
        echo "<table>";
        echo "<tr><th>File</th><th>Status</th><th>Size</th></tr>";
        foreach ($files as $file) {
            $fullPath = $rootPath . '/' . $file;
            $exists = file_exists($fullPath);
            $size = $exists ? filesize($fullPath) : 0;
            $status = $exists ? 
                "<span class='ok'>✓ EXISTS</span>" : 
                "<span class='error'>✗ MISSING</span>";
            $sizeStr = $exists ? number_format($size) . ' bytes' : '-';
            echo "<tr><td><code>$file</code></td><td>$status</td><td>$sizeStr</td></tr>";
        }
        echo "</table>";
        echo "</div>";

        // Check config file
        $configPath = $rootPath . '/config/config.php';
        if (file_exists($configPath)) {
            require_once $configPath;
            
            echo "<div class='section'>";
            echo "<h2>⚙️ Configuration Constants</h2>";
            echo "<table>";
            echo "<tr><th>Constant</th><th>Value</th></tr>";
            
            $constants = [
                'APP_URL', 'BASE_PATH', 'DB_HOST', 'DB_NAME', 'DB_USER',
                'APP_PATH', 'CONTROLLERS_PATH', 'MODELS_PATH', 'CORE_PATH', 'VIEWS_PATH'
            ];
            
            foreach ($constants as $const) {
                if (defined($const)) {
                    $value = constant($const);
                    if ($const === 'DB_PASS') {
                        $value = '****';
                    }
                    echo "<tr><td><code>$const</code></td><td class='path'>$value</td></tr>";
                } else {
                    echo "<tr><td><code>$const</code></td><td class='error'>NOT DEFINED</td></tr>";
                }
            }
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div class='section'>";
            echo "<p class='error'>❌ config/config.php NOT FOUND!</p>";
            echo "</div>";
        }

        // Test controller loading
        echo "<div class='section'>";
        echo "<h2>🔌 Controller Loading Test</h2>";
        
        $controllerPath = $rootPath . '/controllers/BPDASController.php';
        if (file_exists($controllerPath)) {
            echo "<p class='ok'>✓ BPDASController.php exists</p>";
            echo "<p>Size: " . filesize($controllerPath) . " bytes</p>";
            echo "<p>Modified: " . date('Y-m-d H:i:s', filemtime($controllerPath)) . "</p>";
            
            // Try to include it
            try {
                if (defined('CORE_PATH')) {
                    require_once CORE_PATH . 'Controller.php';
                    require_once $controllerPath;
                    
                    if (class_exists('BPDASController')) {
                        echo "<p class='ok'>✓ BPDASController class loaded successfully!</p>";
                        
                        $reflection = new ReflectionClass('BPDASController');
                        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
                        echo "<p>Public methods:</p><ul>";
                        foreach ($methods as $method) {
                            if ($method->class === 'BPDASController') {
                                echo "<li><code>" . $method->name . "()</code></li>";
                            }
                        }
                        echo "</ul>";
                    } else {
                        echo "<p class='error'>✗ BPDASController class not found after include!</p>";
                    }
                } else {
                    echo "<p class='warning'>⚠ CORE_PATH not defined, cannot load controller</p>";
                }
            } catch (Exception $e) {
                echo "<p class='error'>✗ Error loading controller: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='error'>✗ BPDASController.php NOT FOUND at: $controllerPath</p>";
        }
        echo "</div>";

        // Check .htaccess
        $htaccessPath = $scriptPath . '/.htaccess';
        echo "<div class='section'>";
        echo "<h2>⚙️ .htaccess Configuration</h2>";
        if (file_exists($htaccessPath)) {
            echo "<p class='ok'>✓ .htaccess exists</p>";
            echo "<pre>" . htmlspecialchars(file_get_contents($htaccessPath)) . "</pre>";
        } else {
            echo "<p class='error'>✗ .htaccess NOT FOUND!</p>";
        }
        echo "</div>";

        // Test URL routing
        echo "<div class='section'>";
        echo "<h2>🔗 URL Routing Test</h2>";
        echo "<table>";
        echo "<tr><th>Variable</th><th>Value</th></tr>";
        echo "<tr><td>REQUEST_URI</td><td><code>" . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</code></td></tr>";
        echo "<tr><td>SCRIPT_NAME</td><td><code>" . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "</code></td></tr>";
        echo "<tr><td>PHP_SELF</td><td><code>" . ($_SERVER['PHP_SELF'] ?? 'N/A') . "</code></td></tr>";
        echo "</table>";
        
        echo "<p style='margin-top: 15px;'>Test URLs:</p>";
        echo "<ul>";
        echo "<li><a href='" . (defined('APP_URL') ? APP_URL : '') . "/public/'>Home</a></li>";
        echo "<li><a href='" . (defined('APP_URL') ? APP_URL : '') . "/public/auth/login'>Login</a></li>";
        echo "<li><a href='" . (defined('APP_URL') ? APP_URL : '') . "/public/bpdas/dashboard'>BPDAS Dashboard</a></li>";
        echo "</ul>";
        echo "</div>";

        // Session info
        echo "<div class='section'>";
        echo "<h2>🔐 Session Information</h2>";
        echo "<table>";
        echo "<tr><th>Key</th><th>Value</th></tr>";
        if (!empty($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                if ($key === 'user' && is_array($value)) {
                    echo "<tr><td><code>user</code></td><td><pre>" . print_r($value, true) . "</pre></td></tr>";
                } else if (!is_array($value)) {
                    echo "<tr><td><code>$key</code></td><td>" . htmlspecialchars($value) . "</td></tr>";
                }
            }
        } else {
            echo "<tr><td colspan='2' class='warning'>No session data</td></tr>";
        }
        echo "</table>";
        echo "</div>";

        ?>

        <div class="section">
            <h2>⚡ Quick Actions</h2>
            <p><a href="?phpinfo=1" style="color: #00ffff;">View phpinfo()</a></p>
            <p><a href="auth/login" style="color: #00ffff;">Go to Login</a></p>
            <p class="error" style="margin-top: 20px;">⚠️ REMEMBER TO DELETE THIS FILE AFTER DEBUGGING!</p>
        </div>
    </div>

    <?php
    if (isset($_GET['phpinfo'])) {
        echo "<div style='margin-top: 40px; background: white; padding: 20px;'>";
        phpinfo();
        echo "</div>";
    }
    ?>
</body>
</html>
