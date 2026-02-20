<?php
/**
 * Debug Router - Find Exact Routing Error
 * Upload ke: public_html/bibitgratis.com/seedling-dashboard/public/debug_router.php
 * Akses: https://bibitgratis.com/seedling-dashboard/public/debug_router.php?path=bpdas/dashboard
 * 
 * HAPUS SETELAH DEBUGGING!
 */

// Enable all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load core classes
require_once CORE_PATH . 'Model.php';
require_once CORE_PATH . 'View.php';
require_once CORE_PATH . 'Controller.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Router Debug</title>
    <style>
        body { font-family: monospace; background: #1a1a1a; color: #00ff00; padding: 20px; }
        .ok { color: #00ff00; }
        .error { color: #ff0000; font-weight: bold; }
        .warning { color: #ffaa00; }
        h1 { border-bottom: 2px solid #00ff00; padding-bottom: 10px; }
        .box { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #00ff00; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; }
        code { color: #00ffff; }
    </style>
</head>
<body>
    <h1>🔧 Router Debug</h1>

    <?php
    // Get path from query string
    $testPath = $_GET['path'] ?? 'bpdas/dashboard';
    
    echo "<div class='box'>";
    echo "<h2>Test Path: <code>$testPath</code></h2>";
    echo "</div>";

    // Simulate routing
    $request = '/' . ltrim(BASE_PATH, '/') . '/' . ltrim($testPath, '/');
    $basePath = BASE_PATH;

    echo "<div class='box'>";
    echo "<h2>📍 URL Processing</h2>";
    echo "Raw request: <code>$request</code><br>";
    echo "Base path: <code>$basePath</code><br>";

    // Remove base path
    $path = str_replace($basePath, '', parse_url($request, PHP_URL_PATH));
    $path = trim($path, '/');
    
    echo "Cleaned path: <code>$path</code><br>";
    echo "</div>";

    // Parse route
    $parts = explode('/', $path);
    $controllerName = ucfirst($parts[0]) . 'Controller';
    $action = isset($parts[1]) ? $parts[1] : 'index';
    $params = array_slice($parts, 2);

    echo "<div class='box'>";
    echo "<h2>🎯 Route Parsing</h2>";
    echo "Parts: <code>" . implode(', ', $parts) . "</code><br>";
    echo "Controller name: <code>$controllerName</code><br>";
    echo "Action: <code>$action</code><br>";
    echo "Params: <code>" . (empty($params) ? 'none' : implode(', ', $params)) . "</code><br>";
    echo "</div>";

    // Controller file path
    $controllerFile = CONTROLLERS_PATH . $controllerName . '.php';

    echo "<div class='box'>";
    echo "<h2>📄 Controller File Check</h2>";
    echo "Expected path: <code>$controllerFile</code><br>";
    
    if (file_exists($controllerFile)) {
        echo "<span class='ok'>✓ File EXISTS</span><br>";
        echo "File size: " . filesize($controllerFile) . " bytes<br>";
    } else {
        echo "<span class='error'>✗ File NOT FOUND!</span><br>";
        echo "Search in: <code>" . CONTROLLERS_PATH . "</code><br>";
        
        // List available controllers
        $files = glob(CONTROLLERS_PATH . '*Controller.php');
        echo "<br>Available controllers:<br><ul>";
        foreach ($files as $file) {
            echo "<li>" . basename($file) . "</li>";
        }
        echo "</ul>";
    }
    echo "</div>";

    // Try to load controller
    echo "<div class='box'>";
    echo "<h2>🔌 Controller Loading</h2>";
    
    if (file_exists($controllerFile)) {
        echo "Including file...<br>";
        
        try {
            require_once $controllerFile;
            echo "<span class='ok'>✓ File included successfully</span><br><br>";
            
            if (class_exists($controllerName)) {
                echo "<span class='ok'>✓ Class '$controllerName' exists</span><br><br>";
                
                echo "Attempting to instantiate...<br>";
                try {
                    $controller = new $controllerName();
                    echo "<span class='ok'>✓ Controller instantiated successfully!</span><br><br>";
                    
                    // Check if method exists
                    if (method_exists($controller, $action)) {
                        echo "<span class='ok'>✓ Method '$action()' exists</span><br><br>";
                        
                        echo "<strong>✅ ROUTING SHOULD WORK!</strong><br>";
                        echo "The controller and method are both available.<br><br>";
                        
                        echo "<span class='warning'>If still getting 404, check:</span><br>";
                        echo "1. .htaccess RewriteBase<br>";
                        echo "2. Session authentication in constructor<br>";
                        echo "3. PHP error logs<br>";
                        
                    } else {
                        echo "<span class='error'>✗ Method '$action()' NOT FOUND!</span><br>";
                        
                        $reflection = new ReflectionClass($controllerName);
                        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
                        
                        echo "<br>Available public methods:<br><ul>";
                        foreach ($methods as $method) {
                            if ($method->class === $controllerName) {
                                echo "<li>" . $method->name . "()</li>";
                            }
                        }
                        echo "</ul>";
                    }
                    
                } catch (Exception $e) {
                    echo "<span class='error'>✗ ERROR during instantiation:</span><br>";
                    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
                    echo "<br><strong>Stack trace:</strong><br>";
                    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                    
                    echo "<br><span class='warning'>⚠️ THIS IS THE PROBLEM!</span><br>";
                    echo "The controller file exists but throws an error when instantiated.<br>";
                }
                
            } else {
                echo "<span class='error'>✗ Class '$controllerName' NOT FOUND after include!</span><br>";
            }
            
        } catch (Exception $e) {
            echo "<span class='error'>✗ ERROR including file:</span><br>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        }
        
    } else {
        echo "<span class='error'>✗ Cannot load - file doesn't exist</span>";
    }
    echo "</div>";

    // Session info
    echo "<div class='box'>";
    echo "<h2>🔐 Session Info</h2>";
    if (isset($_SESSION['user'])) {
        echo "<span class='ok'>✓ User logged in</span><br>";
        echo "User ID: " . $_SESSION['user']['id'] . "<br>";
        echo "Username: " . $_SESSION['user']['username'] . "<br>";
        echo "Role: <code>" . $_SESSION['user']['role'] . "</code><br>";
        
        if ($_SESSION['user']['role'] === 'bpdas') {
            echo "BPDAS ID: " . ($_SESSION['user']['bpdas_id'] ?? 'NULL') . "<br>";
        }
    } else {
        echo "<span class='warning'>⚠ No user session</span><br>";
        echo "You need to login first to test BPDAS routes.";
    }
    echo "</div>";

    // Test different paths
    echo "<div class='box'>";
    echo "<h2>🧪 Test Other Paths</h2>";
    $testPaths = [
        'bpdas/dashboard' => 'BPDAS Dashboard',
        'public/dashboard' => 'Public Dashboard',
        'admin/dashboard' => 'Admin Dashboard',
        'auth/login' => 'Login Page'
    ];
    
    echo "<ul>";
    foreach ($testPaths as $p => $label) {
        echo "<li><a href='?path=$p' style='color: #00ffff;'>$label ($p)</a></li>";
    }
    echo "</ul>";
    echo "</div>";
    ?>

    <p style="margin-top: 30px;">
        <a href="auth/login" style="color: #00ffff;">Go to Login</a> | 
        <a href="test_bpdas.php" style="color: #00ffff;">BPDAS Test</a> |
        <a href="debug.php" style="color: #00ffff;">System Debug</a>
    </p>

    <p class="error">⚠️ HAPUS FILE INI SETELAH DEBUGGING!</p>
</body>
</html>
