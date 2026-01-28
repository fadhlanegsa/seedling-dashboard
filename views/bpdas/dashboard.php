<?php
/**
 * BPDAS Dashboard View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-home"></i> Dashboard BPDAS</h1>
    <p>Selamat datang di Dashboard BPDAS</p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--primary-color);">
            <i class="fas fa-seedling"></i>
        </div>
        <div class="stat-content">
            <h3><?= formatNumber($stockStats['total_types'] ?? 0) ?></h3>
            <p>Jenis Bibit</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--success-color);">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-content">
            <h3><?= formatNumber($stockStats['total_quantity'] ?? 0) ?></h3>
            <p>Total Stok</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--warning-color);">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h3><?= formatNumber($requestStats['pending'] ?? 0) ?></h3>
            <p>Permintaan Pending</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--info-color);">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3><?= formatNumber($requestStats['approved'] ?? 0) ?></h3>
            <p>Permintaan Disetujui</p>
        </div>
    </div>
</div>

<!-- Recent Stock Updates -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-boxes"></i> Stok Terbaru</h3>
                <a href="<?= url('bpdas/stock') ?>" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <?php if (!empty($recentStock)): ?>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Jenis Bibit</th>
                                <th>Jumlah</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentStock as $stock): ?>
                                <tr>
                                    <td><?= htmlspecialchars($stock['seedling_name']) ?></td>
                                    <td><strong><?= formatNumber($stock['quantity']) ?></strong></td>
                                    <td><?= formatDate($stock['updated_at'], DATE_FORMAT) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-muted">Belum ada data stok</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-inbox"></i> Permintaan Pending</h3>
                <a href="<?= url('bpdas/requests') ?>" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <?php if (!empty($pendingRequests)): ?>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>No. Permintaan</th>
                                <th>Pemohon</th>
                                <th>Jenis Bibit</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingRequests as $request): ?>
                                <tr>
                                    <td><a href="<?= url('bpdas/requestDetail/' . $request['id']) ?>"><?= htmlspecialchars($request['request_number']) ?></a></td>
                                    <td><?= htmlspecialchars($request['requester_name']) ?></td>
                                    <td><?= htmlspecialchars($request['seedling_name']) ?></td>
                                    <td><strong><?= formatNumber($request['quantity']) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-muted">Tidak ada permintaan pending</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-bolt"></i> Aksi Cepat</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="<?= url('bpdas/stock-form') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Stok
                    </a>
                    <a href="<?= url('bpdas/stock') ?>" class="btn btn-success">
                        <i class="fas fa-boxes"></i> Kelola Stok
                    </a>
                    <a href="<?= url('bpdas/requests') ?>" class="btn btn-warning">
                        <i class="fas fa-inbox"></i> Lihat Permintaan
                    </a>
                    <a href="<?= url('bpdas/profile') ?>" class="btn btn-info">
                        <i class="fas fa-user"></i> Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--white);
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.5rem;
}

.stat-content h3 {
    margin: 0;
    font-size: 2rem;
    color: var(--primary-dark);
}

.stat-content p {
    margin: 0.25rem 0 0 0;
    color: var(--text-light);
    font-size: 0.875rem;
}

.quick-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.quick-actions .btn {
    flex: 1;
    min-width: 150px;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions .btn {
        flex: 1 1 100%;
    }
}
</style>
