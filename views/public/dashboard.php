<?php
/**
 * Public User Dashboard
 */
?>

<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Dashboard Saya</h1>
    <p>Selamat datang, <?= htmlspecialchars(currentUser()['full_name'] ?? 'User') ?>!</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= $stats['total_requests'] ?? 0 ?></div>
                <div class="stat-label">Total Permintaan</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= $stats['pending'] ?? 0 ?></div>
                <div class="stat-label">Menunggu</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= $stats['approved'] ?? 0 ?></div>
                <div class="stat-label">Disetujui</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= $stats['rejected'] ?? 0 ?></div>
                <div class="stat-label">Ditolak</div>
            </div>
        </div>
    </div>
</div>

<!-- Quota Information -->
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"><i class="fas fa-chart-pie"></i> Kuota Permintaan Bibit</h5>
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="progress mb-2" style="height: 25px;">
                    <div class="progress-bar bg-<?= $quota['percentage'] >= 90 ? 'danger' : ($quota['percentage'] >= 70 ? 'warning' : 'success') ?>" 
                         role="progressbar" 
                         style="width: <?= $quota['percentage'] ?>%;" 
                         aria-valuenow="<?= $quota['percentage'] ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <?= $quota['percentage'] ?>%
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Terpakai: <strong><?= formatNumber($quota['used']) ?></strong></small>
                    <small class="text-muted">Maksimal: <strong><?= formatNumber($quota['max']) ?></strong></small>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <div class="h4 mb-0 text-<?= $quota['remaining'] == 0 ? 'danger' : 'success' ?>">
                    Sisa: <?= formatNumber($quota['remaining']) ?>
                </div>
                <small class="text-muted">Batang Bibit</small>
            </div>
        </div>
        <?php if ($quota['remaining'] <= 0): ?>
            <div class="alert alert-danger mt-3 mb-0">
                <i class="fas fa-exclamation-circle"></i> Kuota Anda telah habis. Anda tidak dapat mengajukan permintaan baru saat ini.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-bolt"></i> Aksi Cepat</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <a href="<?= url('public/request-form') ?>" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-plus-circle"></i> Ajukan Permintaan Bibit
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="<?= url('public/my-requests') ?>" class="btn btn-info btn-block btn-lg">
                    <i class="fas fa-list"></i> Lihat Permintaan Saya
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="<?= url('search') ?>" class="btn btn-success btn-block btn-lg">
                    <i class="fas fa-search"></i> Cari Stok Bibit
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Requests -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-history"></i> Permintaan Terbaru</h5>
        <a href="<?= url('public/my-requests') ?>" class="btn btn-sm btn-outline-primary">
            Lihat Semua <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($recentRequests)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Anda belum memiliki permintaan bibit. 
                <a href="<?= url('public/request-form') ?>">Ajukan permintaan sekarang</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Permintaan</th>
                            <th>BPDAS</th>
                            <th>Jenis Bibit</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentRequests as $request): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($request['request_number'] ?? 'N/A') ?></strong></td>
                                <td><?= htmlspecialchars($request['bpdas_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($request['seedling_name'] ?? 'N/A') ?></td>
                                <td><?= formatNumber($request['quantity'] ?? 0) ?> bibit</td>
                                <td>
                                    <?php $status = $request['status'] ?? 'pending'; ?>
                                    <span class="badge badge-<?= status_badge_class($status) ?>">
                                        <?= status_text($status) ?>
                                    </span>
                                </td>
                                <td><?= isset($request['created_at']) ? formatDate($request['created_at']) : 'N/A' ?></td>
                                <td>
                                    <a href="<?= url('public/requestDetail/' . ($request['id'] ?? '')) ?>" 
                                       class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Information Box -->
<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi</h5>
    </div>
    <div class="card-body">
        <h6><strong>Cara Mengajukan Permintaan Bibit:</strong></h6>
        <ol>
            <li>Klik tombol "Ajukan Permintaan Bibit" di atas</li>
            <li>Pilih provinsi dan BPDAS tujuan</li>
            <li>Pilih jenis bibit yang diinginkan</li>
            <li>Masukkan jumlah bibit dan tujuan penggunaan</li>
            <li>Submit permintaan dan tunggu persetujuan dari BPDAS</li>
            <li>Jika disetujui, Anda dapat mengunduh surat persetujuan</li>
        </ol>
        
        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-triangle"></i> 
            <strong>Catatan:</strong> Pastikan data yang Anda masukkan sudah benar. 
            Permintaan yang sudah diajukan tidak dapat dibatalkan.
        </div>
    </div>
</div>
