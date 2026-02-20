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


<!-- Nursery Stock Breakdown -->
<div class="row mt-4">
    <div class="col-12 mb-3">
        <h3><i class="fas fa-warehouse"></i> Stok Per Persemaian</h3>
    </div>
    <?php if (!empty($nurseries)): ?>
        <?php foreach ($nurseries as $nursery): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-truncate" title="<?= htmlspecialchars($nursery['name']) ?>">
                        <?= htmlspecialchars($nursery['name']) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6 border-right">
                            <h4 class="text-primary font-weight-bold"><?= formatNumber($nursery['stats']['total_quantity']) ?></h4>
                            <small class="text-muted">Total Stok</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success font-weight-bold"><?= formatNumber($nursery['stats']['total_types']) ?></h4>
                            <small class="text-muted">Jenis Bibit</small>
                        </div>
                    </div>
                    
                    <h6 class="border-bottom pb-2 mb-2 text-muted small text-uppercase">Stok Terbanyak</h6>
                    <?php if (!empty($nursery['stock_items'])): ?>
                    <div class="table-responsive" style="max-height: 150px; overflow-y: auto;">
                        <table class="table table-sm table-borderless table-striped small mb-0">
                            <tbody>
                                <?php foreach ($nursery['stock_items'] as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['seedling_name']) ?></td>
                                    <td class="text-right"><strong><?= formatNumber($item['quantity']) ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <p class="text-center text-muted small py-3">Belum ada data stok</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white text-center">
                    <small class="text-muted">Update: <?= $nursery['stats']['last_update'] ? formatDate($nursery['stats']['last_update'], DATE_FORMAT) : '-' ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">Belum ada data persemaian untuk BPDAS ini.</div>
        </div>
    <?php endif; ?>
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
                                <th>Persemaian</th>
                                <th>Jumlah</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentStock as $stock): ?>
                                <tr>
                                    <td><?= htmlspecialchars($stock['seedling_name']) ?></td>
                                    <td><small class="text-muted"><?= htmlspecialchars($stock['nursery_name'] ?? 'Langsung') ?></small></td>
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
                                    <td><strong><?= formatNumber($request['quantity'] ?? $request['item_quantity'] ?? 0) ?></strong></td>
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

<!-- Recent Delivery Gallery -->
<div class="card mt-4 mb-4">
    <div class="card-header bg-success text-white">
        <h3><i class="fas fa-camera"></i> Dokumentasi Penyerahan Terakhir</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($recentDeliveries)): ?>
            <div class="gallery-grid">
                <?php foreach ($recentDeliveries as $delivery): ?>
                    <div class="gallery-item">
                        <div class="gallery-image-wrapper">
                            <img src="<?= url('uploads/' . $delivery['delivery_photo_path']) ?>" 
                                 alt="Dokumentasi <?= htmlspecialchars($delivery['request_number']) ?>"
                                 class="gallery-image"
                                 onclick="window.open(this.src, '_blank')">
                            <div class="gallery-overlay">
                                <span class="badge badge-light"><?= formatDate($delivery['delivery_date'], DATE_FORMAT) ?></span>
                            </div>
                        </div>
                        <div class="gallery-info">
                            <h6 class="text-truncate" title="<?= htmlspecialchars($delivery['request_number']) ?>">
                                <a href="<?= url('bpdas/requestDetail/' . $delivery['id']) ?>">
                                    <?= htmlspecialchars($delivery['request_number']) ?>
                                </a>
                            </h6>
                            <p class="text-muted small mb-0 text-truncate">
                                <i class="fas fa-user"></i> <?= htmlspecialchars($delivery['requester_name']) ?>
                            </p>
                            <p class="text-primary small mb-0 text-truncate">
                                <i class="fas fa-seedling"></i> <?= htmlspecialchars($delivery['seedling_name']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4 text-muted">
                <i class="fas fa-camera fa-3x mb-3 text-gray-300"></i>
                <p>Belum ada dokumentasi penyerahan yang diupload.</p>
                <small>Foto akan muncul di sini setelah Anda mengupload bukti serah terima pada permintaan yang disetujui.</small>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* ... existing styles ... */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}

.gallery-item {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.gallery-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.gallery-image-wrapper {
    position: relative;
    height: 150px;
    overflow: hidden;
    background-color: #f8f9fa;
    cursor: pointer;
}

.gallery-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.gallery-image:hover {
    transform: scale(1.05);
}

.gallery-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
}

.gallery-info {
    padding: 12px;
    background: #fff;
}

.gallery-info h6 {
    margin-bottom: 5px;
    font-weight: 600;
}
</style>



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
