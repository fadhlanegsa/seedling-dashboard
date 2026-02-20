<?php
/**
 * Operator - Request Management View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-inbox"></i> Kelola Permintaan Bibit</h1>
</div>

<!-- Status Filter -->
<div class="card mb-3">
    <div class="card-body">
        <div class="filter-buttons">
            <a href="<?= url('operator/requests') ?>" 
               class="btn btn-sm <?= $currentStatus == 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">
                Semua
            </a>
            <a href="<?= url('operator/requests?status=pending') ?>" 
               class="btn btn-sm <?= $currentStatus == 'pending' ? 'btn-primary' : 'btn-outline-primary' ?>">
                Menunggu
            </a>
            <a href="<?= url('operator/requests?status=approved') ?>" 
               class="btn btn-sm <?= $currentStatus == 'approved' ? 'btn-primary' : 'btn-outline-primary' ?>">
                Disetujui
            </a>
            <a href="<?= url('operator/requests?status=delivered') ?>" 
               class="btn btn-sm <?= $currentStatus == 'delivered' ? 'btn-primary' : 'btn-outline-primary' ?>">
                Selesai/Diserahkan
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Permintaan</th>
                        <th>Pemohon</th>
                        <th>Jenis Bibit</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $index => $request): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= htmlspecialchars($request['request_number'] ?? '-') ?></strong></td>
                                <td>
                                    <?= htmlspecialchars($request['requester_name'] ?? '-') ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($request['requester_email'] ?? '-') ?></small>
                                </td>
                                <td><?= htmlspecialchars($request['seedling_name'] ?? '-') ?></td>
                                <td><strong><?= formatNumber($request['quantity'] ?? 0) ?></strong></td>
                                <td>
                                    <?php $status = $request['status'] ?? 'pending'; ?>
                                    <span class="badge badge-<?= status_badge_class($status) ?>">
                                        <?= status_text($status) ?>
                                    </span>
                                </td>
                                <td><?= isset($request['created_at']) ? formatDate($request['created_at'], DATE_FORMAT) : '-' ?></td>
                                <td>
                                    <a href="<?= url('operator/requestDetail/' . $request['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">Tidak ada permintaan untuk persemaian Anda</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.filter-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
</style>
