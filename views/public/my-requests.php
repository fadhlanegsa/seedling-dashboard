<?php
/**
 * Public - My Requests View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-list"></i> Permintaan Saya</h1>
</div>

<!-- Status Filter -->
<div class="card mb-3">
    <div class="card-body">
        <div class="filter-buttons">
            <a href="<?= url('public/my-requests') ?>" class="btn btn-sm <?= !$currentStatus ? 'btn-primary' : 'btn-outline-primary' ?>">
                Semua
            </a>
            <?php foreach (REQUEST_STATUS as $statusKey => $statusLabel): ?>
                <a href="<?= url('public/my-requests?status=' . $statusKey) ?>" class="btn btn-sm <?= $currentStatus == $statusKey ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <?= htmlspecialchars($statusLabel) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Permintaan</th>
                        <th>Jenis/Program Bibit</th>
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
                                    <?= htmlspecialchars($request['seedling_name'] ?? '-') ?><br>
                                    <?php if(($request['program_type'] ?? 'Reguler') === 'FOLU'): ?>
                                        <span class="badge" style="background-color: #39FF14; color: #000;">FOLU</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary">Reguler</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= formatNumber($request['item_quantity'] ?: $request['quantity'] ?: 0) ?></strong> bibit</td>
                                <td>
                                    <?php $status = $request['status'] ?? 'pending'; ?>
                                    <span class="badge badge-<?= status_badge_class($status) ?>">
                                        <?= status_text($status) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $date = $request['created_at'] ?? null;
                                    echo $date ? formatDate($date, DATE_FORMAT) : '-';
                                    ?>
                                </td>
                                <td class="action-cell">
                                    <a href="<?= url('public/requestDetail/' . ($request['id'] ?? '')) ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <?php if (in_array($status, ['pending', 'approved', 'delivered']) && empty($request['has_survey'])): ?>
                                        <button type="button" class="btn btn-sm btn-warning btn-rate-request"
                                                data-request-id="<?= (int)($request['id'] ?? 0) ?>"
                                                data-request-number="<?= htmlspecialchars($request['request_number'] ?? '') ?>">
                                            ⭐ Beri Penilaian
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada permintaan</td>
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
.action-cell {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    white-space: nowrap;
}
@media (max-width: 576px) {
    .action-cell {
        white-space: normal;
    }
}
</style>

<?php if (!empty($requests)): ?>
    <?php require_once VIEWS_PATH . 'partials/survey-modal.php'; ?>
<?php endif; ?>
