<?php
/**
 * Admin - Manage Requests View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-file-alt"></i> Kelola Permintaan</h1>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?= url('admin/requests') ?>" class="filter-form">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <?php foreach (REQUEST_STATUS as $key => $label): ?>
                                <option value="<?= $key ?>" 
                                        <?= ($filters['status'] == $key) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Provinsi</label>
                        <select name="province_id" class="form-control">
                            <option value="">Semua Provinsi</option>
                            <?php foreach ($provinces as $province): ?>
                                <option value="<?= $province['id'] ?>" 
                                        <?= ($filters['province_id'] == $province['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($province['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">BPDAS</label>
                        <select name="bpdas_id" class="form-control">
                            <option value="">Semua BPDAS</option>
                            <?php foreach ($bpdasList as $bpdas): ?>
                                <option value="<?= $bpdas['id'] ?>" 
                                        <?= ($filters['bpdas_id'] == $bpdas['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($bpdas['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="<?= url('admin/requests') ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Requests Table -->
<div class="card">
    <div class="card-body">
        <table id="requestsTable" class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Permintaan</th>
                    <th>Pemohon</th>
                    <th>BPDAS Tujuan</th>
                    <th>Jenis Bibit</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($requests)): ?>
                    <?php
                    // Get pagination data for row numbering
                    $currentPage = $pagination['page'] ?? 1;
                    $perPage = $pagination['perPage'] ?? ITEMS_PER_PAGE;
                    ?>
                    <?php foreach ($requests as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 + (($currentPage - 1) * $perPage) ?></td>
                            <td><strong><?= htmlspecialchars($item['request_number'] ?? '-') ?></strong></td>
                            <td>
                                <?= htmlspecialchars($item['requester_name'] ?? '-') ?><br>
                                <small class="text-muted"><?= htmlspecialchars($item['requester_email'] ?? '-') ?></small>
                            </td>
                            <td><?= htmlspecialchars($item['bpdas_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($item['seedling_name'] ?? '-') ?></td>
                            <td><strong><?= formatNumber($item['quantity'] ?? 0) ?></strong></td>
                            <td>
                                <?php $status = $item['status'] ?? 'pending'; ?>
                                <span class="badge badge-<?= status_badge_class($status) ?>">
                                    <?= status_text($status) ?>
                                </span>
                            </td>
                            <td><?= isset($item['created_at']) ? formatDate($item['created_at'], DATE_FORMAT) : '-' ?></td>
                            <td>
                                <a href="<?= url('admin/requestDetail/' . $item['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data permintaan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

<?php
// Render pagination using helper
if (isset($pagination)) {
    require_once VIEWS_PATH . 'helpers/pagination.php';
    
    // Preserve filter parameters in pagination
    $queryParams = [
        'status' => $filters['status'] ?? null,
        'province_id' => $filters['province_id'] ?? null,
        'bpdas_id' => $filters['bpdas_id'] ?? null
    ];
    
    renderPagination($pagination, 'admin/requests', $queryParams);
}
?>

<style>
.filter-form .form-group {
    margin-bottom: 1rem;
}
</style>
