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
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar-complex">
                <a href="<?= url('') ?>" class="navbar-brand-complex">
                    <img src="<?= asset('images/logo-kementerian.png') ?>" alt="Logo Kementerian" class="brand-logo-img">
                    <div class="brand-text">
                        <span class="brand-title">Dashboard Stok Bibit</span>
                        <span class="brand-subtitle">Kementerian Kehutanan Republik Indonesia</span>
                    </div>
                </a>
                
                <ul class="nav-links-complex">
                    <li>
                        <a href="<?= url('') ?>" class="nav-link-item <?= $this->activeClass('') ?>">
                            <i class="fas fa-home"></i> Beranda
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('home/search') ?>" class="nav-link-item <?= $this->activeClass('home/search') ?>">
                            <i class="fas fa-search"></i> Cari Stok
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('home/distribution') ?>" class="nav-link-item <?= $this->activeClass('home/distribution') ?>">
                            <i class="fas fa-map-marked-alt"></i> Peta Sebaran
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('home/howto') ?>" class="nav-link-item <?= $this->activeClass('home/howto') ?>">
                            <i class="fas fa-info-circle"></i> Info Layanan
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('public/seed-source-directory') ?>" class="nav-link-item <?= $this->activeClass('public/seed-source-directory') ?>">
                            <i class="fas fa-tree"></i> Direktori Sumber Benih
                        </a>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                        <?php $user = currentUser(); ?>
                         <li>
                            <?php if ($user['role'] === 'admin'): ?>
                                <a href="<?= url('admin/dashboard') ?>" class="btn btn-warning btn-sm" style="color: #333; font-weight: bold;">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard Admin
                                </a>
                            <?php elseif ($user['role'] === 'bpdas'): ?>
                                <a href="<?= url('bpdas/dashboard') ?>" class="btn btn-warning btn-sm" style="color: #333; font-weight: bold;">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard BPDAS
                                </a>
                            <?php else: ?>
                                <a href="<?= url('public/dashboard') ?>" class="btn btn-warning btn-sm" style="color: #333; font-weight: bold;">
                                    <i class="fas fa-user-circle"></i> Akun Saya
                                </a>
                            <?php endif; ?>
                        </li>
                        <li><a href="<?= url('auth/logout') ?>" class="nav-link-item"><i class="fas fa-sign-out-alt"></i></a></li>
                    <?php else: ?>
                        <li><a href="<?= url('auth/login') ?>" class="btn btn-warning btn-sm" style="color: #333; font-weight: bold; margin-left: 1rem;">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a></li>
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
