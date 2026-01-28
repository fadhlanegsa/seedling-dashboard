<?php
/**
 * Auth Layout Template
 * For login and registration pages
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login' ?> - Dashboard Stok Bibit</title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            padding: 2rem;
        }
        .auth-box {
            background: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-header h1 {
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }
        .auth-header p {
            color: var(--text-light);
        }
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>🌳 Dashboard Stok Bibit</h1>
                <p>Kementerian Kehutanan</p>
            </div>

            <?php if (isset($flash)): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <?= $content ?>

            <div class="auth-footer">
                <a href="<?= url('') ?>">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>
