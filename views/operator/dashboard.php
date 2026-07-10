<!-- Offline Sync Banner -->
<div id="offlineSyncBanner" class="alert alert-warning align-items-center justify-content-between mb-4 shadow-sm" style="display: none; border-radius: 12px; border-left: 5px solid #ffc107; padding: 15px 20px;">
    <div class="d-flex align-items-center flex-wrap">
        <div class="mr-3" style="font-size: 1.5rem;">
            <i class="fas fa-exclamation-triangle text-warning"></i>
        </div>
        <div>
            <h6 class="alert-heading mb-1 font-weight-bold" style="color: #856404;">Ada Data Offline Belum Sinkron</h6>
            <p class="mb-0 text-muted small">Sebanyak <span class="pending-count font-weight-bold text-dark">0</span> transaksi tersimpan secara lokal. Silakan sinkronkan data saat Anda terhubung ke internet.</p>
        </div>
    </div>
    <div class="mt-2 mt-sm-0">
        <button id="btnSyncOffline" class="btn btn-warning btn-sm font-weight-bold shadow-sm px-3 py-2" onclick="handleOfflineSync()">
            <i class="fas fa-sync-alt mr-1"></i> Sinkronkan Sekarang
        </button>
    </div>
</div>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-home"></i> Dashboard Operator</h1>
        <p>Selamat datang, <strong><?= $user['full_name'] ?></strong></p>
        <p class="text-muted"><i class="fas fa-building"></i> <?= $bpdas_name ?> - <i class="fas fa-leaf"></i> <?= $nursery_name ?></p>
    </div>
    
    <!-- Global Program Filter -->
    <div class="program-filter">
        <form action="<?= url('operator/dashboard') ?>" method="GET" class="form-inline">
            <label for="program_type" class="mr-2 font-weight-bold">Filter Program:</label>
            <select name="program_type" id="program_type" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Program</option>
                <option value="Reguler" <?= ($currentProgram === 'Reguler') ? 'selected' : '' ?>>Reguler</option>
                <option value="RHL" <?= ($currentProgram === 'RHL') ? 'selected' : '' ?>>RHL</option>
                <option value="FOLU" <?= ($currentProgram === 'FOLU') ? 'selected' : '' ?>>FOLU Net Sink 2030</option>
            </select>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Stok Bibit</h6>
                        <h2 class="mt-2 mb-0">
                            <?= number_format(array_sum(array_column($stocks['data'], 'quantity'))) ?>
                        </h2>
                    </div>
                    <i class="fas fa-boxes fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Jenis Bibit</h6>
                        <h2 class="mt-2 mb-0"><?= count($stocks['data']) ?></h2>
                    </div>
                    <i class="fas fa-seedling fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white h-100" style="background:#8e44ad;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Bibit Terdistribusi (<?= date('Y') ?>)</h6>
                        <h2 class="mt-2 mb-0"><?= number_format($totalDistributed ?? 0) ?></h2>
                    </div>
                    <i class="fas fa-truck fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Swipeable Menu Cepat PUB (Khusus Mobile) -->
<div class="d-block d-md-none mb-4">
    <h6 class="font-weight-bold text-muted mb-2 px-1">Menu Cepat Produksi (PUB)</h6>
    <div class="swipeable-menu pb-2">
        <a href="<?= url('seedling-admin/bahan-baku-form') ?>" class="swipe-item bg-white shadow-sm rounded p-3 text-center text-decoration-none d-flex flex-column align-items-center justify-content-center">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 45px; height: 45px; font-size: 1.1rem;">
                <i class="fas fa-layer-group"></i>
            </div>
            <span class="small font-weight-bold text-dark" style="line-height: 1.1;">Bahan Baku</span>
        </a>
        <a href="<?= url('seedling-admin/media-mixing-form') ?>" class="swipe-item bg-white shadow-sm rounded p-3 text-center text-decoration-none d-flex flex-column align-items-center justify-content-center">
            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 45px; height: 45px; font-size: 1.1rem;">
                <i class="fas fa-mortar-pestle"></i>
            </div>
            <span class="small font-weight-bold text-dark" style="line-height: 1.1;">Mixing</span>
        </a>
        <a href="<?= url('seedling-admin/seed-sowing-form') ?>" class="swipe-item bg-white shadow-sm rounded p-3 text-center text-decoration-none d-flex flex-column align-items-center justify-content-center">
            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 45px; height: 45px; font-size: 1.1rem;">
                <i class="fas fa-seedling"></i>
            </div>
            <span class="small font-weight-bold text-dark" style="line-height: 1.1;">Tanam</span>
        </a>
        <a href="<?= url('seedling-admin/harvesting-form') ?>" class="swipe-item bg-white shadow-sm rounded p-3 text-center text-decoration-none d-flex flex-column align-items-center justify-content-center">
            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 45px; height: 45px; font-size: 1.1rem;">
                <i class="fas fa-leaf"></i>
            </div>
            <span class="small font-weight-bold text-dark" style="line-height: 1.1;">Panen</span>
        </a>
        <a href="<?= url('seedling-admin/weaning-form') ?>" class="swipe-item bg-white shadow-sm rounded p-3 text-center text-decoration-none d-flex flex-column align-items-center justify-content-center">
            <div class="bg-teal text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 45px; height: 45px; font-size: 1.1rem; background-color: #20c997;">
                <i class="fas fa-expand-arrows-alt"></i>
            </div>
            <span class="small font-weight-bold text-dark" style="line-height: 1.1;">Sapih</span>
        </a>
        <a href="<?= url('seedling-admin/mutation-form') ?>" class="swipe-item bg-white shadow-sm rounded p-3 text-center text-decoration-none d-flex flex-column align-items-center justify-content-center">
            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 45px; height: 45px; font-size: 1.1rem;">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <span class="small font-weight-bold text-dark" style="line-height: 1.1;">Naik Kelas</span>
        </a>
    </div>
</div>

<style>
/* CSS khusus untuk Swipeable Menu di dashboard operator */
.swipeable-menu {
    display: flex;
    overflow-x: auto;
    gap: 12px;
    padding-bottom: 5px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
}
.swipeable-menu::-webkit-scrollbar {
    display: none; /* Hide scrollbar for Chrome, Safari and Opera */
}
.swipeable-menu {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
.swipe-item {
    flex: 0 0 auto;
    width: 95px;
    scroll-snap-align: start;
    border: 1px solid rgba(0,0,0,0.05);
    transition: transform 0.1s ease;
}
.swipe-item:active {
    transform: scale(0.95);
}
</style>

<!-- Stock List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Stok Bibit di <?= $nursery_name ?></h5>
        <a href="<?= url('operator/stock') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i> Kelola Stok
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Jenis/Program Bibit</th>
                        <th>Kategori</th>
                        <th>Jumlah Stok</th>
                        <th>Terakhir Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stocks['data'])): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data stok</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($stocks['data'] as $stock): ?>
                            <tr>
                                <td>
                                    <?= $stock['seedling_name'] ?><br><small class="text-muted"><em><?= $stock['scientific_name'] ?></em></small><br>
                                    <?php $pt = $stock['program_type'] ?? 'Reguler'; ?>
                                    <?php if($pt === 'FOLU'): ?>
                                        <span class="badge" style="background-color: #39FF14; color: #000;">FOLU</span>
                                    <?php elseif($pt === 'RHL'): ?>
                                        <span class="badge badge-info text-white">RHL</span>
                                    <?php elseif($pt === 'bibitgratis' || $pt === 'PUB'): ?>
                                        <span class="badge badge-primary"><i class="fas fa-seedling mr-1"></i> PUB</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Reguler</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-secondary"><?= $stock['category'] ?></span></td>
                                <td class="font-weight-bold"><?= number_format($stock['quantity']) ?></td>
                                <td><?= date('d M Y', strtotime($stock['last_update_date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
