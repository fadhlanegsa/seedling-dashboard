<?php
/**
 * Public Layout Template
 * For public-facing pages (landing, search, BPDAS detail)
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Stok Bibit Persemaian Indonesia' ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="<?= url('') ?>" class="navbar-brand">
                    🌳 Dashboard Stok Bibit Indonesia 
                </a>
                <a>
                    
                </a>
                <a> Direktorat Penghijauan dan Perbenihan Tanaman Hutan</a>
                <ul class="navbar-menu">
                    <li><a href="<?= url('') ?>" class="<?= $this->activeClass('') ?>">Beranda</a></li>
                    <li><a href="<?= url('home/search') ?>" class="<?= $this->activeClass('home/search') ?>">Cari BPDAS</a></li>
                    <li><a href="<?= url('home/howto') ?>" class="<?= $this->activeClass('home/howto') ?>">Cara Mendapatkan</a></li>
                    <?php if (isLoggedIn()): ?>
                        <?php $user = currentUser(); ?>
                        <?php if ($user['role'] === 'admin'): ?>
                            <li><a href="<?= url('admin/dashboard') ?>">Dashboard Admin</a></li>
                        <?php elseif ($user['role'] === 'bpdas'): ?>
                            <li><a href="<?= url('bpdas/dashboard') ?>">Dashboard BPDAS</a></li>
                        <?php else: ?>
                            <li><a href="<?= url('public/dashboard') ?>">Dashboard Saya</a></li>
                        <?php endif; ?>
                        <li><a href="<?= url('auth/logout') ?>">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?= url('auth/login') ?>" class="btn btn-primary btn-sm">Login</a></li>
                        <li><a href="<?= url('auth/register') ?>" class="btn btn-outline btn-sm">Daftar</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if (isset($flash)): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= $flash['message'] ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Kementerian Kehutanan</p>
            <p>Dashboard Stok Bibit Persemaian Indonesia</p>
            <p><em>Hijau Indonesia Dimulai dari Sini</em> 🌳</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
