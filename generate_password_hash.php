<?php
/**
 * Password Hash Generator
 * 
 * Script ini untuk generate password hash yang benar
 * Upload ke hosting dan akses via browser untuk generate hash baru
 */

// Prevent direct access from browser in production
// Comment line dibawah jika ingin akses via browser
// die('Access denied');

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .result h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 18px;
        }
        .hash-output {
            background: #fff;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .sql-output {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin-bottom: 10px;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #2196f3;
            margin-top: 20px;
            font-size: 14px;
        }
        .warning {
            background: #fff3cd;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #ffc107;
            margin-bottom: 20px;
            font-size: 14px;
            color: #856404;
        }
        .copy-btn {
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
        }
        .copy-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Password Hash Generator</h1>
        <p class="subtitle">Generate password hash untuk update database MySQL</p>

        <div class="warning">
            <strong>⚠️ PENTING:</strong> Setelah selesai, HAPUS file ini dari hosting untuk keamanan!
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username (untuk SQL query)</label>
                <input type="text" id="username" name="username" placeholder="e.g., bpdas1" required>
            </div>

            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password baru" required>
            </div>

            <div class="form-group">
                <label for="password_confirm">Konfirmasi Password</label>
                <input type="password" id="password_confirm" name="password_confirm" placeholder="Ketik ulang password" required>
            </div>

            <button type="submit" class="btn">Generate Hash</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = htmlspecialchars($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if (empty($username) || empty($password)) {
                echo '<div class="result" style="border-left-color: #dc3545;">
                        <h3 style="color: #dc3545;">❌ Error</h3>
                        <p>Username dan password harus diisi!</p>
                      </div>';
            } elseif ($password !== $password_confirm) {
                echo '<div class="result" style="border-left-color: #dc3545;">
                        <h3 style="color: #dc3545;">❌ Error</h3>
                        <p>Password dan konfirmasi password tidak cocok!</p>
                      </div>';
            } else {
                // Generate password hash
                $hash = password_hash($password, PASSWORD_DEFAULT);

                echo '<div class="result">';
                echo '<h3>✅ Password Hash Berhasil Dibuat!</h3>';
                
                echo '<p style="margin-bottom: 10px;"><strong>Password Hash:</strong></p>';
                echo '<div class="hash-output" id="hash-output">' . htmlspecialchars($hash) . '</div>';
                echo '<button class="copy-btn" onclick="copyHash()">📋 Copy Hash</button>';
                
                echo '<hr style="margin: 20px 0; border: none; border-top: 1px solid #e0e0e0;">';
                
                echo '<p style="margin-bottom: 10px;"><strong>SQL Query untuk Update:</strong></p>';
                
                // SQL query untuk update by username
                $sql = "UPDATE users SET password = '" . $hash . "' WHERE username = '" . $username . "';";
                echo '<div class="sql-output" id="sql-output">' . htmlspecialchars($sql) . '</div>';
                echo '<button class="copy-btn" onclick="copySQL()">📋 Copy SQL</button>';
                
                echo '</div>';

                // Info PHP version
                echo '<div class="info">';
                echo '<strong>ℹ️ Info Server:</strong><br>';
                echo 'PHP Version: ' . PHP_VERSION . '<br>';
                echo 'Hashing Algorithm: ' . PASSWORD_DEFAULT . ' (bcrypt)';
                echo '</div>';
            }
        }
        ?>
    </div>

    <script>
        function copyHash() {
            const hashText = document.getElementById('hash-output').textContent;
            navigator.clipboard.writeText(hashText).then(() => {
                alert('✅ Hash copied to clipboard!');
            });
        }

        function copySQL() {
            const sqlText = document.getElementById('sql-output').textContent;
            navigator.clipboard.writeText(sqlText).then(() => {
                alert('✅ SQL query copied to clipboard!');
            });
        }
    </script>
</body>
</html>
