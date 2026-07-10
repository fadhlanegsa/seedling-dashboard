<?php
/**
 * Dashboard Layout Template
 * For authenticated users (Admin, BPDAS, Public)
 */
$user = currentUser();
$role = $user['role'] ?? '';

// Mobile Bottom Navigation URLs mapping based on user role
$bnHome = url('public/dashboard');
$bnStok = url('public/my-requests');
$bnInput = url('seedling-admin');
$bnProfile = url('public/profile');

if ($role === 'admin') {
    $bnHome = url('admin/dashboard');
    $bnStok = url('admin/stock');
    $bnProfile = url('admin/users');
} elseif ($role === 'bpdas') {
    $bnHome = url('bpdas/dashboard');
    $bnStok = url('bpdas/stock');
    $bnProfile = url('bpdas/profile');
} elseif ($role === 'operator_persemaian') {
    $bnHome = url('operator/dashboard');
    $bnStok = url('operator/stock');
    $bnProfile = url('operator/profile');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - Direktorat Penghijauan dan Perbenihan Tanaman Hutan</title>
    
    <!-- PWA Manifest & Theme -->
    <link rel="manifest" href="<?= asset('manifest.json') ?>">
    <meta name="theme-color" content="#28a745">

    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=<?= time() ?>">
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
            min-width: 0; /* Prevents flex items from overflowing */
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
        /* Mobile Native UI (PWA) Adjustments */
        @media (max-width: 768px) {
            body, .dashboard-content {
                background-color: var(--white) !important;
            }
            .header, .footer {
                display: none !important; /* Sembunyikan untuk sensasi native app */
            }
            .dashboard-container {
                flex-direction: column;
                min-height: auto;
            }
            .dashboard-sidebar {
                width: 100%;
            }
            .dashboard-content {
                padding: 0 !important;
            }
            /* Edge-to-edge container */
            .dashboard-content > .container-fluid {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            /* Flat cards without border/margin on mobile */
            .dashboard-content .card {
                border-radius: 0 !important;
                border: none !important;
                box-shadow: none !important;
                margin-bottom: 0 !important;
            }
            .dashboard-content .card-header {
                border-radius: 0 !important;
            }
            /* Sticky save button menumpuk mulus di atas bottom nav */
            .sticky-bottom-bar {
                bottom: 65px !important;
                padding-bottom: 12px !important;
                border-top: 1px solid #f1f3f5;
            }
        }

        /* Bottom Navigation Bar Styles */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 65px;
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1040;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            padding-bottom: env(safe-area-inset-bottom, 0);
        }
        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #8e8e93;
            text-decoration: none !important;
            font-size: 0.75rem;
            font-weight: 500;
            width: 25%;
            height: 100%;
            transition: all 0.2s ease;
        }
        .bottom-nav-item i {
            font-size: 1.25rem;
            margin-bottom: 4px;
            transition: transform 0.2s ease;
        }
        .bottom-nav-item:hover, .bottom-nav-item.active {
            color: var(--primary-color, #2e7d32);
        }
        .bottom-nav-item.active i {
            transform: scale(1.1);
            color: var(--primary-color, #2e7d32);
        }

        /* Floating Action Button di Bottom Nav */
        .bottom-nav-fab {
            position: relative;
            top: -20px;
            background-color: var(--primary-color, #2e7d32);
            color: white !important;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.4);
            margin: 0 auto;
            border: 4px solid white;
            transition: transform 0.2s ease;
        }
        .bottom-nav-fab:active {
            transform: scale(0.95);
        }
        .bottom-nav-fab i {
            font-size: 1.5rem !important;
            margin-bottom: 0 !important;
            color: white !important;
        }

        /* Bottom Sheet Modal */
        .modal.bottom-sheet .modal-dialog {
            margin: 0;
            margin-top: auto;
            align-items: flex-end;
            min-height: 100%;
            display: flex;
        }
        .modal.bottom-sheet .modal-content {
            border-radius: 20px 20px 0 0;
            border: none;
            width: 100%;
            padding-bottom: env(safe-area-inset-bottom, 0);
        }
        .modal.bottom-sheet.fade .modal-dialog {
            transform: translate(0, 100%);
            transition: transform 0.3s ease-out;
        }
        .modal.bottom-sheet.show .modal-dialog {
            transform: translate(0, 0);
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
                    <li id="offlineIndicator" style="display: none; margin-right: 15px;"><span class="badge badge-warning text-dark font-weight-bold px-3 py-2" style="border-radius: 20px;"><i class="fas fa-wifi-slash mr-1"></i> Mode Offline</span></li>
                    <li><a href="<?= url('') ?>">Beranda</a></li>
                    <li><span style="color: white;">Halo, <?= htmlspecialchars($user['full_name'] ?? 'Tamu') ?></span></li>
                    <li><a href="<?= url('auth/logout') ?>" class="btn btn-danger btn-sm">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Mobile Offline Banner -->
    <div id="mobileOfflineBanner" class="bg-warning text-dark text-center py-2 px-3 d-md-none font-weight-bold" style="display: none; font-size: 0.85rem; border-bottom: 1.5px solid #d39e00; z-index: 1030; position: sticky; top: 0;">
        <i class="fas fa-wifi-slash mr-1"></i> Mode Offline Aktif — Transaksi Disimpan Lokal
    </div>

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
        <aside class="dashboard-sidebar d-none d-md-block">
            <div class="user-info">
                <h4><?= htmlspecialchars($user['full_name'] ?? 'Tamu') ?></h4>
                <p><?= ucfirst($user['role'] ?? 'guest') ?></p>
            </div>
            
            <nav class="sidebar">
                <ul class="sidebar-menu">
                    <?php if (($user['role'] ?? '') === 'admin'): ?>
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
                        <li><a href="<?= url('admin/seed-sources') ?>" class="<?= $this->activeClass('admin/seed-sources') ?>">
                            <i class="fas fa-tree"></i> Direktori Sumber Benih
                        </a></li>
                        <li><a href="<?= url('admin/nurseries') ?>" class="<?= $this->activeClass('admin/nurseries') ?>">
                            <i class="fas fa-seedling"></i> Kelola Persemaian
                        </a></li>
                        <li><a href="<?= url('admin/kabar-kehutanan') ?>" class="<?= $this->activeClass('admin/kabar-kehutanan') ?>">
                            <i class="fas fa-newspaper"></i> Kabar Kehutanan
                        </a></li>
                        <li><a href="<?= url('admin/survey-summary') ?>" class="<?= $this->activeClass('admin/survey-summary') ?>">
                            <i class="fas fa-poll"></i> Survei Kepuasan
                        </a></li>
                    <?php elseif (($user['role'] ?? '') === 'bpdas'): ?>
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
                        <li><a href="<?= url('bpdas/kabar-kehutanan') ?>" class="<?= $this->activeClass('bpdas/kabar-kehutanan') ?>">
                            <i class="fas fa-newspaper"></i> Kabar Kehutanan
                        </a></li>
                        <li><a href="<?= url('bpdas/survey-summary') ?>" class="<?= $this->activeClass('bpdas/survey-summary') ?>">
                            <i class="fas fa-poll"></i> Survei Kepuasan
                        </a></li>
                    <?php elseif (($user['role'] ?? '') === 'operator_persemaian'): ?>
                        <li><a href="<?= url('operator/dashboard') ?>" class="<?= $this->activeClass('operator/dashboard') ?>">
                            <i class="fas fa-home"></i> Dashboard
                        </a></li>
                        <li><a href="<?= url('operator/stock') ?>" class="<?= $this->activeClass('operator/stock') ?>">
                            <i class="fas fa-boxes"></i> Kelola Stok
                        </a></li>
                        <li><a href="<?= url('operator/requests') ?>" class="<?= $this->activeClass('operator/requests') ?>">
                            <i class="fas fa-inbox"></i> Permintaan Masuk
                        </a></li>
                        <li><a href="<?= url('operator/map-distribution') ?>" class="<?= $this->activeClass('operator/map-distribution') ?>">
                            <i class="fas fa-map-marked-alt"></i> Peta Distribusi
                        </a></li>
                        <li><a href="<?= url('operator/profile') ?>" class="<?= $this->activeClass('operator/profile') ?>">
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

                        <!-- Penatausahaan Bibit Module (Visible only to Admin, BPDAS, Operator) -->
                        <?php if (in_array($user['role'] ?? '', ['admin', 'bpdas', 'operator_persemaian'])): ?>
                            <li class="nav-section-header mt-3 px-3 small text-muted font-weight-bold">PENATAUSAHAAN BIBIT</li>
                            <li><a href="<?= url('seedling-admin') ?>" class="<?= $this->activeClass('seedling-admin') ?> d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-microscope"></i> Ringkasan</span>
                                <span class="badge badge-warning offline-badge text-dark font-weight-bold" style="display: none; border-radius: 10px; font-size: 0.75rem; padding: 3px 8px;">0</span>
                            </a></li>
                            <li><a href="<?= url('seedling-admin/master-data') ?>" class="<?= $this->activeClass('seedling-admin/master-data') ?>">
                                <i class="fas fa-database"></i> Database
                            </a></li>
                            <li><a href="<?= url('kamerha/sync-log') ?>" class="<?= $this->activeClass('kamerha/sync-log') ?>">
                                <i class="fas fa-satellite-dish"></i> Integrasi Kamerha
                            </a></li>
                            
                            <?php if (in_array($user['role'] ?? '', ['admin', 'operator_persemaian'])): ?>
                                <li><a href="<?= url('seedling-admin/bahan-baku-form') ?>" class="<?= $this->activeClass('seedling-admin/bahan-baku-form') ?>">
                                    <i class="fas fa-layer-group"></i> Bahan Baku IN
                                </a></li>
                                <li><a href="<?= url('seedling-admin/media-mixing-form') ?>" class="<?= $this->activeClass('seedling-admin/media-mixing-form') ?>">
                                    <i class="fas fa-blender"></i> Pencampuran Media
                                </a></li>
                                <li><a href="<?= url('seedling-admin/bag-filling-form') ?>" class="<?= $this->activeClass('seedling-admin/bag-filling-form') ?>">
                                    <i class="fas fa-fill-drip"></i> Pengisian Kantong
                                </a></li>
                                <li><a href="<?= url('seedling-admin/seed-sowing-form') ?>" class="<?= $this->activeClass('seedling-admin/seed-sowing-form') ?>">
                                    <i class="fas fa-seedling"></i> Penaburan Benih
                                </a></li>
                                <li><a href="<?= url('seedling-admin/harvesting-form') ?>" class="<?= $this->activeClass('seedling-admin/harvesting-form') ?>">
                                    <i class="fas fa-leaf"></i> Pemanenan Semai
                                </a></li>
                                <li><a href="<?= url('seedling-admin/weaning-form') ?>" class="<?= $this->activeClass('seedling-admin/weaning-form') ?>">
                                    <i class="fas fa-seedling" style="color: #4CAF50;"></i> Penyapihan Bibit
                                </a></li>
                                <li><a href="<?= url('seedling-admin/entres-form') ?>" class="<?= $this->activeClass('seedling-admin/entres-form') ?>">
                                    <i class="fas fa-cut" style="color: #ff9800;"></i> Penggunaan Entres
                                </a></li>
                                <li><a href="<?= url('seedling-admin/mutation-form') ?>" class="<?= $this->activeClass('seedling-admin/mutation-form') ?>">
                                    <i class="fas fa-exchange-alt" style="color: #f44336;"></i> Mutasi Bibit (BO)
                                </a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-content">
            <?= $content ?>
        </main>
    </div>

    <!-- Bottom Navigation (Mobile Only) -->
    <div class="bottom-nav d-block d-md-none">
        <a href="<?= $bnHome ?>" class="bottom-nav-item <?= ($this->isActive('admin/dashboard') || $this->isActive('bpdas/dashboard') || $this->isActive('operator/dashboard') || $this->isActive('public/dashboard')) ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="<?= $bnStok ?>" class="bottom-nav-item <?= ($this->isActive('admin/stock') || $this->isActive('bpdas/stock') || $this->isActive('operator/stock')) ? 'active' : '' ?>">
            <i class="fas fa-boxes"></i>
            <span>Stok</span>
        </a>
        <a href="#" class="bottom-nav-item" data-toggle="modal" data-target="#pubBottomSheet" style="position: relative;">
            <div class="bottom-nav-fab">
                <i class="fas fa-plus"></i>
            </div>
            <span class="badge badge-warning offline-badge text-dark font-weight-bold" style="position: absolute; top: 0px; right: 22%; display: none; border-radius: 50%; min-width: 18px; height: 18px; align-items: center; justify-content: center; font-size: 0.65rem; z-index: 1050; border: 1.5px solid white; padding: 2px;">0</span>
        </a>
        <a href="<?= $bnProfile ?>" class="bottom-nav-item <?= ($this->isActive('admin/users') || $this->isActive('bpdas/profile') || $this->isActive('operator/profile') || $this->isActive('public/profile')) ? 'active' : '' ?>">
            <i class="fas fa-user"></i>
            <span>Profil</span>
        </a>
    </div>

    <!-- Bottom Sheet Modal untuk Menu Cepat PUB -->
    <div class="modal fade bottom-sheet" id="pubBottomSheet" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content pb-4">
                <div class="modal-header border-0 pb-0 justify-content-center">
                    <div style="width: 40px; height: 5px; background-color: #e0e0e0; border-radius: 10px; margin-top: 5px;"></div>
                </div>
                <div class="modal-body pt-3">
                    <h6 class="font-weight-bold text-center mb-4 text-dark">Menu Cepat Produksi (PUB)</h6>
                    <div class="row text-center px-2" style="row-gap: 20px;">
                        <!-- Bahan Baku -->
                        <div class="col-4">
                            <a href="<?= url('seedling-admin/bahan-baku-form') ?>" class="d-flex flex-column align-items-center text-decoration-none">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 55px; height: 55px; font-size: 1.2rem;">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <span class="x-small font-weight-bold text-dark">Bahan Baku</span>
                            </a>
                        </div>
                        <!-- Mixing -->
                        <div class="col-4">
                            <a href="<?= url('seedling-admin/media-mixing-form') ?>" class="d-flex flex-column align-items-center text-decoration-none">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 55px; height: 55px; font-size: 1.2rem;">
                                    <i class="fas fa-mortar-pestle"></i>
                                </div>
                                <span class="x-small font-weight-bold text-dark">Mixing</span>
                            </a>
                        </div>
                        <!-- Tanam -->
                        <div class="col-4">
                            <a href="<?= url('seedling-admin/seed-sowing-form') ?>" class="d-flex flex-column align-items-center text-decoration-none">
                                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 55px; height: 55px; font-size: 1.2rem;">
                                    <i class="fas fa-seedling"></i>
                                </div>
                                <span class="x-small font-weight-bold text-dark">Tanam</span>
                            </a>
                        </div>
                        <!-- Panen -->
                        <div class="col-4">
                            <a href="<?= url('seedling-admin/harvesting-form') ?>" class="d-flex flex-column align-items-center text-decoration-none">
                                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 55px; height: 55px; font-size: 1.2rem;">
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <span class="x-small font-weight-bold text-dark">Panen</span>
                            </a>
                        </div>
                        <!-- Sapih -->
                        <div class="col-4">
                            <a href="<?= url('seedling-admin/weaning-form') ?>" class="d-flex flex-column align-items-center text-decoration-none">
                                <div class="bg-teal text-white rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 55px; height: 55px; font-size: 1.2rem; background-color: #20c997;">
                                    <i class="fas fa-expand-arrows-alt"></i>
                                </div>
                                <span class="x-small font-weight-bold text-dark">Sapih</span>
                            </a>
                        </div>
                        <!-- Naik Kelas -->
                        <div class="col-4">
                            <a href="<?= url('seedling-admin/mutation-form') ?>" class="d-flex flex-column align-items-center text-decoration-none">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 55px; height: 55px; font-size: 1.2rem;">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <span class="x-small font-weight-bold text-dark">Naik Kelas</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="<?= asset('js/main.js') ?>"></script>
    <script src="<?= asset('js/datatables.js') ?>"></script>
    <script src="<?= asset('js/offline-manager.js') ?>"></script>
    <script>
        // Global handler for offline sync button click
        async function handleOfflineSync() {
            const btn = document.getElementById('btnSyncOffline');
            if (!btn) return;
            
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sinkronisasi...';
            
            try {
                const result = await OfflineManager.syncAll((current, total, label) => {
                    btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i> (${current}/${total}) ${label}...`;
                });
                
                if (result.success) {
                    OfflineManager.showToast(result.message, 'success');
                } else {
                    OfflineManager.showToast(result.message, 'error');
                    if (result.failCount > 0) {
                        let errorDetails = result.results
                            .filter(r => r.status === 'error')
                            .map(r => `• ${r.label}: ${r.message}`)
                            .join('\n');
                        alert(`Beberapa data gagal disinkronkan:\n${errorDetails}`);
                    }
                }
            } catch (err) {
                console.error('[Sync] Error:', err);
                OfflineManager.showToast('Gagal melakukan sinkronisasi: ' + err.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
                await OfflineManager.updateBadge();
            }
        }

        // Handle mobile offline banner display
        function updateConnectionStatus() {
            const mobileBanner = document.getElementById('mobileOfflineBanner');
            if (mobileBanner) {
                mobileBanner.style.display = navigator.onLine ? 'none' : 'block';
            }
        }
        window.addEventListener('online', updateConnectionStatus);
        window.addEventListener('offline', updateConnectionStatus);
        document.addEventListener('DOMContentLoaded', updateConnectionStatus);
    </script>
    <?php if (($user['role'] ?? '') === 'admin'): ?>
    <script src="<?= asset('js/charts.js') ?>"></script>
    <script>
        // Auto-inject Nursery Selector for Admins on PUB Form Pages
        if (window.location.pathname.includes('/seedling-admin/') && window.location.pathname.includes('-form')) {
            document.addEventListener('DOMContentLoaded', function() {
                var form = document.querySelector('form');
                if (form) {
                    var html = '<div class="form-group row mb-4 border border-danger p-2 bg-light rounded"><label class="col-sm-5 col-form-label font-weight-bold text-danger"><i class="fas fa-exclamation-triangle"></i> ADMIN OVERRIDE:<br><small>Pilih Persemaian Target</small></label><div class="col-sm-7"><select name="nursery_id" id="admin_nursery_id" class="form-control" required><option value="">-- Loading Persemaian --</option></select><input type="hidden" name="bpdas_id" id="admin_bpdas_id" value=""></div></div>';
                    var rightCol = form.querySelector('.col-md-6.pl-md-5');
                    if (rightCol) {
                        rightCol.insertAdjacentHTML('afterbegin', html);
                    } else if (form.querySelector('.card-body')) {
                        form.querySelector('.card-body').insertAdjacentHTML('afterbegin', html);
                    }
                    
                    // Fetch all nurseries via new endpoint
                    fetch('<?= url("seedling-admin/get-all-nurseries-ajax") ?>')
                        .then(res => res.json())
                        .then(res => {
                            if (res.success && res.data) {
                                var select = document.getElementById('admin_nursery_id');
                                var bpdasInput = document.getElementById('admin_bpdas_id');
                                select.innerHTML = '<option value="">-- Pilih Persemaian Target --</option>';
                                res.data.forEach(function(n) {
                                    var opt = document.createElement('option');
                                    opt.value = n.id;
                                    opt.textContent = n.name;
                                    opt.dataset.bpdas = n.bpdas_id;
                                    select.appendChild(opt);
                                });
                                select.addEventListener('change', function() {
                                    var selOpt = this.options[this.selectedIndex];
                                    bpdasInput.value = selOpt.dataset.bpdas || '';
                                });
                            }
                        });
                }
            });
        }
    </script>
    <?php endif; ?>
    
    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= asset("sw.js") ?>')
                    .then(registration => console.log('ServiceWorker registered'))
                    .catch(err => console.log('ServiceWorker registration failed: ', err));
            });
        }
    </script>
</body>
</html>
