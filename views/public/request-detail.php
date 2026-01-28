<?php
/**
 * Request Detail Page
 */

$statusClass = [
    'pending' => 'warning',
    'approved' => 'success',
    'rejected' => 'danger',
    'completed' => 'info',
    'delivered' => 'purple'
];
$statusText = [
    'pending' => 'Menunggu Persetujuan',
    'approved' => 'Disetujui',
    'rejected' => 'Ditolak',
    'completed' => 'Selesai',
    'delivered' => 'Sudah Diserahkan'
];
$statusIcon = [
    'pending' => 'clock',
    'approved' => 'check-circle',
    'rejected' => 'times-circle',
    'completed' => 'check-double',
    'delivered' => 'handshake'
];
?>

<div class="page-header">
    <h1><i class="fas fa-file-alt"></i> Detail Permintaan</h1>
    <p>Informasi lengkap permintaan bibit</p>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Request Information -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Permintaan</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>No. Permintaan:</strong><br>
                        <span class="h5"><?= htmlspecialchars($request['request_number']) ?></span>
                    </div>
                    <div class="col-md-6 text-right">
                        <strong>Status:</strong><br>
                        <span class="badge badge-<?= $statusClass[$request['status']] ?> badge-lg">
                            <i class="fas fa-<?= $statusIcon[$request['status']] ?>"></i>
                            <?= $statusText[$request['status']] ?>
                        </span>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-calendar"></i> Tanggal Permintaan:</strong><br>
                        <?= isset($request['created_at']) ? formatDate($request['created_at'], DATETIME_FORMAT) : '-' ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-building"></i> BPDAS:</strong><br>
                        <?= htmlspecialchars($request['bpdas_name']) ?><br>
                        <small class="text-muted"><?= htmlspecialchars($request['province_name']) ?></small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-seedling"></i> Jenis Bibit:</strong><br>
                        <?= htmlspecialchars($request['seedling_name']) ?><br>
                        <small class="text-muted"><?= htmlspecialchars($request['scientific_name'] ?? '') ?></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-sort-numeric-up"></i> Jumlah:</strong><br>
                        <span class="h5 text-primary"><?= formatNumber($request['quantity']) ?> bibit</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong><i class="fas fa-bullseye"></i> Tujuan Penggunaan:</strong><br>
                    <p class="mt-2"><?= nl2br(htmlspecialchars($request['purpose'])) ?></p>
                </div>
                
                <?php if (!empty($request['land_area'])): ?>
                <div class="mb-3">
                    <strong><i class="fas fa-map"></i> Luas Lahan:</strong><br>
                    <?= number_format($request['land_area'], 3) ?> Ha
                </div>
                <?php endif; ?>
                
                <?php if (!empty($request['proposal_file_path'])): ?>
                <div class="mb-3">
                    <strong><i class="fas fa-file-pdf"></i> Surat Pengajuan/Proposal:</strong><br>
                    <a href="<?= url('uploads/' . $request['proposal_file_path']) ?>" 
                       target="_blank" class="btn btn-sm btn-info mt-2">
                        <i class="fas fa-download"></i> Download Proposal (PDF)
                    </a>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($request['delivery_photo_path'])): ?>
                <div class="mb-3">
                    <strong><i class="fas fa-camera"></i> Foto Bukti Serah Terima:</strong><br>
                    <div class="mt-2">
                        <a href="<?= url('uploads/' . $request['delivery_photo_path']) ?>" target="_blank">
                            <img src="<?= url('uploads/' . $request['delivery_photo_path']) ?>" 
                                 alt="Bukti Serah Terima" 
                                 class="img-thumbnail" 
                                 style="max-width: 400px; cursor: pointer;">
                        </a>
                        <p class="text-muted mt-2">
                            <small>Klik gambar untuk melihat ukuran penuh</small>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($request['status'] === 'approved'): ?>
                    <hr>
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle"></i> Permintaan Disetujui</h6>
                        <p class="mb-2">
                            <strong>Tanggal Persetujuan:</strong> 
                            <?= formatDate($request['approval_date'], DATETIME_FORMAT) ?>
                        </p>
                        <?php if (!empty($request['approval_notes'])): ?>
                            <p class="mb-2">
                                <strong>Catatan:</strong><br>
                                <?= nl2br(htmlspecialchars($request['approval_notes'])) ?>
                            </p>
                        <?php endif; ?>
                        <a href="<?= url('public/download-approval-letter/' . $request['id']) ?>" 
                           class="btn btn-success mt-2">
                            <i class="fas fa-download"></i> Unduh Surat Persetujuan
                        </a>
                    </div>
                <?php elseif ($request['status'] === 'rejected'): ?>
                    <hr>
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-times-circle"></i> Permintaan Ditolak</h6>
                        <p class="mb-2">
                            <strong>Tanggal Penolakan:</strong> 
                            <?= formatDate($request['rejection_date'], DATETIME_FORMAT) ?>
                        </p>
                        <?php if (!empty($request['rejection_reason'])): ?>
                            <p class="mb-0">
                                <strong>Alasan:</strong><br>
                                <?= nl2br(htmlspecialchars($request['rejection_reason'])) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <hr>
                    <div class="alert alert-warning">
                        <i class="fas fa-clock"></i> 
                        Permintaan Anda sedang dalam proses peninjauan oleh BPDAS. 
                        Anda akan menerima notifikasi melalui email setelah permintaan diproses.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Request History -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Riwayat Permintaan</h5>
            </div>
            <div class="card-body">
                <?php if (empty($history)): ?>
                    <p class="text-muted">Belum ada riwayat</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($history as $item): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-<?= $statusClass[$item['status']] ?? 'secondary' ?>">
                                    <i class="fas fa-<?= $statusIcon[$item['status']] ?? 'circle' ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">
                                        <?= $statusText[$item['status']] ?? ucfirst($item['status']) ?>
                                    </h6>
                                    <p class="text-muted mb-1">
                                        <small>
                                            <i class="fas fa-calendar"></i> 
                                            <?= formatDate($item['created_at'], DATETIME_FORMAT) ?>
                                        </small>
                                    </p>
                                    <?php if (!empty($item['notes'])): ?>
                                        <p class="mb-0"><?= nl2br(htmlspecialchars($item['notes'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cog"></i> Aksi</h5>
            </div>
            <div class="card-body">
                <a href="<?= url('public/my-requests') ?>" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
                
                <?php if ($request['status'] === 'approved'): ?>
                    <a href="<?= url('public/download-approval-letter/' . $request['id']) ?>" 
                       class="btn btn-success btn-block">
                        <i class="fas fa-download"></i> Unduh Surat Persetujuan
                    </a>
                <?php endif; ?>
                
                <a href="<?= url('public/request-form') ?>" class="btn btn-primary btn-block">
                    <i class="fas fa-plus"></i> Ajukan Permintaan Baru
                </a>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-phone"></i> Kontak BPDAS</h5>
            </div>
            <div class="card-body">
                <p><strong><?= htmlspecialchars($request['bpdas_name']) ?></strong></p>
                
                <?php if (!empty($request['bpdas_address'])): ?>
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt"></i> 
                        <?= nl2br(htmlspecialchars($request['bpdas_address'])) ?>
                    </p>
                <?php endif; ?>
                
                <?php if (!empty($request['bpdas_phone'])): ?>
                    <p class="mb-2">
                        <i class="fas fa-phone"></i> 
                        <a href="tel:<?= htmlspecialchars($request['bpdas_phone']) ?>">
                            <?= htmlspecialchars($request['bpdas_phone']) ?>
                        </a>
                    </p>
                <?php endif; ?>
                
                <?php if (!empty($request['bpdas_email'])): ?>
                    <p class="mb-0">
                        <i class="fas fa-envelope"></i> 
                        <a href="mailto:<?= htmlspecialchars($request['bpdas_email']) ?>">
                            <?= htmlspecialchars($request['bpdas_email']) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child):before {
    content: '';
    position: absolute;
    left: -19px;
    top: 30px;
    height: calc(100% - 10px);
    width: 2px;
    background: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
}

.badge-lg {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}

.badge-purple {
    background-color: #9b59b6;
    color: white;
}
</style>
