<?php
/**
 * Dashboard Layout Template
 * For authenticated users (Admin, BPDAS, Public)
 */
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - Dashboard Stok Bibit</title>
    <title><?= $title ?? 'Dashboard' ?> - Direktorat Penghijauan dan Perbenihan Tanaman Hutan</title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <style>
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 200px);
        }
        .dashboard-sidebar {
            width: 250px;
            background: var(--white);
            padding: 2rem 0;
            box-shadow: var(--shadow);
        }
        .dashboard-content {
            flex: 1;
            padding: 2rem;
            background: var(--light-bg);
        }
        .user-info {
            padding: 1rem 1.5rem;
            border-bottom: 2px solid var(--primary-color);
            margin-bottom: 1rem;
        }
        .user-info h4 {
            margin: 0;
            color: var(--primary-dark);
        }
        .user-info p {
            margin: 0.25rem 0 0 0;
            font-size: 0.875rem;
            color: var(--text-light);
        }
        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }
            .dashboard-sidebar {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container-fluid">
            <nav class="navbar">
                <a href="<?= url('') ?>" class="navbar-brand">
                    🌳 Dashboard Stok Bibit Indonesia
                    Direktorat Penghijauan dan Perbenihan Tanaman Hutan
                </a>
                <ul class="navbar-menu">
                    <li><a href="<?= url('') ?>">Beranda</a></li>
                    <li><span style="color: white;">Halo, <?= htmlspecialchars($user['full_name']) ?></span></li>
                    <li><a href="<?= url('auth/logout') ?>" class="btn btn-danger btn-sm">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if (isset($flash)): ?>
        <div class="container-fluid mt-3">
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= $flash['message'] ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <div class="user-info">
                <h4><?= htmlspecialchars($user['full_name']) ?></h4>
                <p><?= ucfirst($user['role']) ?></p>
            </div>
            
            <nav class="sidebar">
                <ul class="sidebar-menu">
                    <?php if ($user['role'] === 'admin'): ?>
                        <li><a href="<?= url('admin/dashboard') ?>" class="<?= $this->activeClass('admin/dashboard') ?>">
                            <i class="fas fa-chart-line"></i> Dashboard
                        </a></li>
                        <li><a href="<?= url('admin/bpdas') ?>" class="<?= $this->activeClass('admin/bpdas') ?>">
                            <i class="fas fa-building"></i> Kelola BPDAS
                        </a></li>
                        <li><a href="<?= url('admin/seedling-types') ?>" class="<?= $this->activeClass('admin/seedling-types') ?>">
                            <i class="fas fa-seedling"></i> Jenis Bibit
                        </a></li>
                        <li><a href="<?= url('admin/stock') ?>" class="<?= $this->activeClass('admin/stock') ?>">
                            <i class="fas fa-boxes"></i> Stok Nasional
                        </a></li>
                        <li><a href="<?= url('admin/requests') ?>" class="<?= $this->activeClass('admin/requests') ?>">
                            <i class="fas fa-file-alt"></i> Permintaan
                        </a></li>
                        <li><a href="<?= url('admin/map-distribution') ?>" class="<?= $this->activeClass('admin/map-distribution') ?>">
                            <i class="fas fa-map-marked-alt"></i> Peta Distribusi
                        </a></li>
                        <li><a href="<?= url('admin/users') ?>" class="<?= $this->activeClass('admin/users') ?>">
                            <i class="fas fa-users"></i> Pengguna
                        </a></li>
                    <?php elseif ($user['role'] === 'bpdas'): ?>
                        <li><a href="<?= url('bpdas/dashboard') ?>" class="<?= $this->activeClass('bpdas/dashboard') ?>">
                            <i class="fas fa-home"></i> Dashboard
                        </a></li>
                        <li><a href="<?= url('bpdas/stock') ?>" class="<?= $this->activeClass('bpdas/stock') ?>">
                            <i class="fas fa-boxes"></i> Kelola Stok
                        </a></li>
                        <li><a href="<?= url('bpdas/requests') ?>" class="<?= $this->activeClass('bpdas/requests') ?>">
                            <i class="fas fa-inbox"></i> Permintaan Masuk
                        </a></li>
                        <li><a href="<?= url('bpdas/map-distribution') ?>" class="<?= $this->activeClass('bpdas/map-distribution') ?>">
                            <i class="fas fa-map-marked-alt"></i> Peta Distribusi
                        </a></li>
                        <li><a href="<?= url('bpdas/profile') ?>" class="<?= $this->activeClass('bpdas/profile') ?>">
                            <i class="fas fa-user"></i> Profil
                        </a></li>
                    <?php else: ?>
                        <li><a href="<?= url('public/dashboard') ?>" class="<?= $this->activeClass('public/dashboard') ?>">
                            <i class="fas fa-home"></i> Dashboard
                        </a></li>
                        <li><a href="<?= url('public/request-form') ?>" class="<?= $this->activeClass('public/request-form') ?>">
                            <i class="fas fa-plus-circle"></i> Ajukan Permintaan
                        </a></li>
                        <li><a href="<?= url('public/my-requests') ?>" class="<?= $this->activeClass('public/my-requests') ?>">
                            <i class="fas fa-list"></i> Permintaan Saya
                        </a></li>
                        <li><a href="<?= url('public/profile') ?>" class="<?= $this->activeClass('public/profile') ?>">
                            <i class="fas fa-user"></i> Profil
                        </a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-content">
            <?= $content ?>
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Kementerian Kehutanan</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="<?= asset('js/main.js') ?>"></script>
    <script src="<?= asset('js/datatables.js') ?>"></script>
    <?php if ($user['role'] === 'admin'): ?>
    <script src="<?= asset('js/charts.js') ?>"></script>
    <?php endif; ?>
</body>
</html>
