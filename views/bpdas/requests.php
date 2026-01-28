<?php
/**
 * BPDAS - Incoming Requests View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-inbox"></i> Permintaan Bibit</h1>
</div>

<!-- Status Filter -->
<div class="card mb-3">
    <div class="card-body">
        <div class="filter-buttons">
            <a href="<?= url('bpdas/requests') ?>" 
               class="btn btn-sm <?= !$currentStatus ? 'btn-primary' : 'btn-outline-primary' ?>">
                Semua
            </a>
            <?php foreach (REQUEST_STATUS as $statusKey => $statusLabel): ?>
                <a href="<?= url('bpdas/requests?status=' . $statusKey) ?>" 
                   class="btn btn-sm <?= $currentStatus == $statusKey ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <?= htmlspecialchars($statusLabel) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
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
                                <?php
                                $status = $request['status'] ?? 'pending';
                                $statusClass = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'completed' => 'info'
                                ];
                                $class = $statusClass[$status] ?? 'secondary';
                                ?>
                                <span class="badge badge-<?= $class ?>">
                                    <?= REQUEST_STATUS[$status] ?? $status ?>
                                </span>
                            </td>
                            <td><?= isset($request['created_at']) ? formatDate($request['created_at'], DATE_FORMAT) : '-' ?></td>
                            <td>
                                <?php if (!empty($request['id'])): ?>
                                    <a href="<?= url('bpdas/requestDetail/' . $request['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada permintaan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.filter-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
</style>
