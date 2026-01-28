<?php
/**
 * Error Layout Template
 * For error pages (404, 403, 500)
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Error' ?> - Dashboard Stok Bibit</title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--light-bg);
            padding: 2rem;
        }
        .error-box {
            text-align: center;
            max-width: 600px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: var(--primary-color);
            line-height: 1;
            margin-bottom: 1rem;
        }
        .error-message {
            font-size: 1.5rem;
            color: var(--text-dark);
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-box">
            <?= $content ?>
        </div>
    </div>
</body>
</html>
