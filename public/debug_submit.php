<?php
/**
 * Debug Submit Request - Show Exact Error
 * Upload ke: public_html/bibitgratis.com/seedling-dashboard/public/debug_submit.php
 * Akses: https://bibitgratis.com/seedling-dashboard/public/debug_submit.php
 * 
 * HAPUS SETELAH DEBUG!
 */

// Enable ALL error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load core classes
require_once CORE_PATH . 'Model.php';
require_once CORE_PATH . 'View.php';
require_once CORE_PATH . 'Controller.php';

// Load PublicController
require_once CONTROLLERS_PATH . 'PublicController.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Submit Request</title>
    <style>
        body { font-family: monospace; background: #1a1a1a; color: #00ff00; padding: 20px; }
        .ok { color: #00ff00; }
        .error { color: #ff0000; font-weight: bold; }
        .warning { color: #ffaa00; }
        h1 { border-bottom: 2px solid #00ff00; padding-bottom: 10px; }
        .box { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #00ff00; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; white-space: pre-wrap; }
        code { color: #00ffff; }
    </style>
</head>
<body>
    <h1>🐛 Debug Submit Request</h1>

    <div class="box">
        <h2>📋 Session Check</h2>
        <?php
        if (isset($_SESSION['user'])) {
            echo "<span class='ok'>✓ User logged in</span><br>";
            echo "User ID: " . $_SESSION['user']['id'] . "<br>";
            echo "Username: " . $_SESSION['user']['username'] . "<br>";
            echo "Role: " . $_SESSION['user']['role'] . "<br>";
        } else {
            echo "<span class='error'>✗ User NOT logged in</span><br>";
            echo "<span class='warning'>You need to login first to test submit request!</span>";
        }
        ?>
    </div>

    <div class="box">
        <h2>🧪 Test PublicController Methods</h2>
        <?php
        try {
            $controller = new PublicController();
            echo "<span class='ok'>✓ PublicController instantiated</span><br>";
            
            // Check if submitRequest method exists
            if (method_exists($controller, 'submitRequest')) {
                echo "<span class='ok'>✓ submitRequest() method exists</span><br>";
            } else {
                echo "<span class='error'>✗ submitRequest() method NOT FOUND!</span><br>";
            }
            
        } catch (Exception $e) {
            echo "<span class='error'>✗ Error instantiating controller:</span><br>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            echo "<strong>Stack trace:</strong><br>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
        ?>
    </div>

    <div class="box">
        <h2>📝 Simulate Submit Request</h2>
        <?php
        if (!isset($_SESSION['user'])) {
            echo "<span class='warning'>⚠ Login first to test submit</span>";
        } else {
            echo "<p>Fill the form below to test submit request with FULL ERROR REPORTING:</p>";
            ?>
            
            <form method="POST" enctype="multipart/form-data" style="background: #3a3a3a; padding: 15px; border-radius: 5px;">
                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="test_submit" value="1">
                
                <div style="margin-bottom: 10px;">
                    <label style="color: #00ffff;">BPDAS ID:</label><br>
                    <input type="number" name="bpdas_id" value="1" required style="padding: 5px; width: 100px;">
                </div>
                
                <div style="margin-bottom: 10px;">
                    <label style="color: #00ffff;">Seedling Type ID:</label><br>
                    <input type="number" name="seedling_type_id" value="1" required style="padding: 5px; width: 100px;">
                </div>
                
                <div style="margin-bottom: 10px;">
                    <label style="color: #00ffff;">Quantity:</label><br>
                    <input type="number" name="quantity" value="10" required style="padding: 5px; width: 100px;">
                </div>
                
                <div style="margin-bottom: 10px;">
                    <label style="color: #00ffff;">Purpose:</label><br>
                    <textarea name="purpose" required style="padding: 5px; width: 300px; height: 60px;">Testing submit request debug</textarea>
                </div>
                
                <div style="margin-bottom: 10px;">
                    <label style="color: #00ffff;">Land Area (Ha):</label><br>
                    <input type="text" name="land_area" value="0.5" style="padding: 5px; width: 100px;">
                </div>
                
                <div style="margin-bottom: 10px;">
                    <label style="color: #00ffff;">Latitude:</label><br>
                    <input type="text" name="latitude" value="-6.2000" style="padding: 5px; width: 150px;">
                </div>
                
                <div style="margin-bottom: 10px;">
                    <label style="color: #00ffff;">Longitude:</label><br>
                    <input type="text" name="longitude" value="106.8000" style="padding: 5px; width: 150px;">
                </div>
                
                <div style="margin-bottom: 10px;">
                    <label style="color: #00ffff;">Proposal (PDF, optional):</label><br>
                    <input type="file" name="proposal" accept=".pdf" style="padding: 5px;">
                </div>
                
                <button type="submit" style="padding: 10px 20px; background: #00ff00; color: #000; border: none; cursor: pointer; font-weight: bold;">
                    🚀 SUBMIT REQUEST (WITH DEBUG)
                </button>
            </form>
            
            <?php
        }
        ?>
    </div>

    <?php
    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_submit'])) {
        echo "<div class='box'>";
        echo "<h2>🔧 Processing Request...</h2>";
        
        try {
            // Call submitRequest
            echo "<p>Calling PublicController->submitRequest()...</p>";
            
            // We can't call it directly because it will redirect
            // Instead, let's manually run the same logic with error output
            
            $user = currentUser();
            echo "<span class='ok'>✓ Got current user: " . $user['username'] . "</span><br>";
            
            $data = [
                'user_id' => $user['id'],
                'bpdas_id' => $_POST['bpdas_id'] ?? null,
                'seedling_type_id' => $_POST['seedling_type_id'] ?? null,
                'quantity' => $_POST['quantity'] ?? null,
                'purpose' => sanitize($_POST['purpose'] ?? ''),
                'land_area' => $_POST['land_area'] ?? null,
                'latitude' => $_POST['latitude'] ?? null,
                'longitude' => $_POST['longitude'] ?? null
            ];
            
            echo "<span class='ok'>✓ Data prepared</span><br>";
            echo "<pre>" . print_r($data, true) . "</pre>";
            
            // Check stock
            require_once MODELS_PATH . 'Stock.php';
            $stockModel = new Stock();
            $stock = $stockModel->findByBPDASAndSeedling($data['bpdas_id'], $data['seedling_type_id']);
            
            if (!$stock) {
                echo "<span class='error'>✗ Stock NOT FOUND for BPDAS " . $data['bpdas_id'] . " and Seedling " . $data['seedling_type_id'] . "</span><br>";
            } else {
                echo "<span class='ok'>✓ Stock found: " . $stock['quantity'] . " available</span><br>";
                
                if ($stock['quantity'] < $data['quantity']) {
                    echo "<span class='error'>✗ Stock not enough!</span><br>";
                } else {
                    // Create request
                    require_once MODELS_PATH . 'Request.php';
                    $requestModel = new Request();
                    
                    echo "Creating request...<br>";
                    $requestId = $requestModel->createRequest($data);
                    
                    if ($requestId) {
                        echo "<span class='ok'>✓ Request created! ID: $requestId</span><br>";
                        
                        // Try add history
                        echo "Adding history...<br>";
                        try {
                            $requestModel->addHistory($requestId, 'pending', $user['id'], 'Permintaan dibuat (DEBUG TEST)');
                            echo "<span class='ok'>✓ History added</span><br>";
                        } catch (Exception $e) {
                            echo "<span class='error'>✗ addHistory ERROR:</span><br>";
                            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
                            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                        }
                        
                        // Try send email
                        echo "Sending email notification...<br>";
                        try {
                            if (file_exists(UTILS_PATH . 'EmailSender.php')) {
                                require_once UTILS_PATH . 'EmailSender.php';
                                $emailSender = new EmailSender();
                                $request = $requestModel->getWithDetails($requestId);
                                $emailSender->sendNewRequestNotification($request);
                                echo "<span class='ok'>✓ Email sent</span><br>";
                            } else {
                                echo "<span class='warning'>⚠ EmailSender.php not found (non-critical)</span><br>";
                            }
                        } catch (Exception $e) {
                            echo "<span class='warning'>⚠ Email ERROR (non-critical):</span><br>";
                            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
                        }
                        
                        echo "<br><strong class='ok'>✅ REQUEST SUBMISSION SUCCESSFUL!</strong><br>";
                        echo "<p>Request ID: $requestId</p>";
                        echo "<p>If this works but normal submit still errors, the problem is in redirect or flash message logic.</p>";
                        
                    } else {
                        echo "<span class='error'>✗ createRequest() returned FALSE!</span><br>";
                        echo "<p>Check database connection or createRequest() method in Request model.</p>";
                    }
                }
            }
            
        } catch (Exception $e) {
            echo "<span class='error'>✗ EXCEPTION CAUGHT:</span><br>";
            echo "<strong>Message:</strong><br>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            echo "<strong>File:</strong> " . $e->getFile() . "<br>";
            echo "<strong>Line:</strong> " . $e->getLine() . "<br>";
            echo "<strong>Stack trace:</strong><br>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
        
        echo "</div>";
    }
    ?>

    <p style="margin-top: 30px;">
        <a href="auth/login" style="color: #00ffff;">Go to Login</a> | 
        <a href="public/dashboard" style="color: #00ffff;">Public Dashboard</a>
    </p>

    <p class="error">⚠️ HAPUS FILE INI SETELAH DEBUGGING!</p>
</body>
</html>
