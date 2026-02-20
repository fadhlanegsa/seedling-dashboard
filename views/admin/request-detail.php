<?php
/**
 * Admin - Request Detail View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-file-alt"></i> Detail Permintaan</h1>
    <a href="<?= url('admin/requests') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Informasi Permintaan</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">No. Permintaan:</th>
                        <td><strong><?= htmlspecialchars($request['request_number'] ?? '-') ?></strong></td>
                    </tr>
                    <tr>
                        <th>BPDAS Tujuan:</th>
                        <td><strong><?= htmlspecialchars($request['bpdas_name'] ?? '-') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <?php
                            $statusClass = [
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'completed' => 'info',
                                'delivered' => 'purple'
                            ];
                            $status = $request['status'] ?? 'pending';
                            $class = $statusClass[$status] ?? 'secondary';
                            ?>
                            <span class="badge badge-<?= $class ?>">
                                <?= REQUEST_STATUS[$status] ?? $status ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Permintaan:</th>
                        <td><?= isset($request['created_at']) ? formatDate($request['created_at'], DATETIME_FORMAT) : '-' ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Bibit:</th>
                        <td><strong><?= htmlspecialchars($request['seedling_name'] ?? '-') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Jumlah Diminta:</th>
                        <td><strong><?= formatNumber($request['quantity'] ?? 0) ?></strong> bibit</td>
                    </tr>
                    <tr>
                        <th>Tujuan Penggunaan:</th>
                        <td><?= htmlspecialchars($request['purpose'] ?? '-') ?></td>
                    </tr>
                    <?php if (!empty($request['land_area'])): ?>
                    <tr>
                        <th>Luas Lahan:</th>
                        <td><?= formatLandArea($request['land_area']) ?> Ha</td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($request['planting_address'])): ?>
                    <tr>
                        <th>Alamat Tanam:</th>
                        <td><?= nl2br(htmlspecialchars($request['planting_address'])) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($request['latitude']) && !empty($request['longitude'])): ?>
                    <tr>
                        <th>Koordinat:</th>
                        <td>
                            <a href="https://www.google.com/maps?q=<?= $request['latitude'] ?>,<?= $request['longitude'] ?>" target="_blank">
                                <?= $request['latitude'] ?>, <?= $request['longitude'] ?>
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($request['proposal_file_path'])): ?>
                    <tr>
                        <th>Surat Pengajuan:</th>
                        <td>
                            <a href="<?= url('uploads/' . $request['proposal_file_path']) ?>" 
                               target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-file-pdf"></i> Download Proposal (PDF)
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($request['delivery_photo_path'])): ?>
                    <tr>
                        <th>Foto Bukti Serah Terima:</th>
                        <td>
                            <a href="<?= url('uploads/' . $request['delivery_photo_path']) ?>" target="_blank">
                                <img src="<?= url('uploads/' . $request['delivery_photo_path']) ?>" 
                                     alt="Bukti Serah Terima" 
                                     class="img-thumbnail" 
                                     style="max-width: 300px; cursor: pointer;">
                            </a>
                            <br><small class="text-muted">Klik untuk melihat ukuran penuh</small>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($request['notes'])): ?>
                    <tr>
                        <th>Catatan:</th>
                        <td><?= nl2br(htmlspecialchars($request['notes'])) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <?php if (!empty($history)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3>Riwayat</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach ($history as $item): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <strong><?= REQUEST_STATUS[$item['status'] ?? 'pending'] ?? $item['status'] ?? '-' ?></strong>
                                <?php if (!empty($item['notes'])): ?>
                                    <p><?= nl2br(htmlspecialchars($item['notes'])) ?></p>
                                <?php endif; ?>
                                <small class="text-muted">
                                    <?= isset($item['created_at']) ? formatDate($item['created_at'], DATETIME_FORMAT) : '-' ?>
                                    <?php if (!empty($item['user_name'])): ?>
                                        oleh <?= htmlspecialchars($item['user_name']) ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3>Data Pemohon</h3>
            </div>
            <div class="card-body">
                <p><strong>Nama:</strong><br><?= htmlspecialchars($request['requester_name'] ?? '-') ?></p>
                <p><strong>Email:</strong><br><?= htmlspecialchars($request['requester_email'] ?? '-') ?></p>
                <p><strong>Telepon:</strong><br><?= htmlspecialchars($request['requester_phone'] ?? '-') ?></p>
                <p><strong>NIK:</strong><br><?= htmlspecialchars($request['requester_nik'] ?? '-') ?></p>
                <?php if (!empty($request['requester_address'])): ?>
                <p><strong>Alamat:</strong><br><?= nl2br(htmlspecialchars($request['requester_address'])) ?></p>
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

.timeline-marker {
    position: absolute;
    left: -30px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--primary-color);
    border: 2px solid var(--white);
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 12px;
    bottom: -20px;
    width: 2px;
    background: var(--border-color);
}

.timeline-item:last-child::before {
    display: none;
}

.badge-purple {
    background-color: #9b59b6;
    color: white;
}
</style>
