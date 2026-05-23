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
            <div class="form-actions d-flex justify-content-between align-items-center">
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="<?= url('admin/requests') ?>" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
                <div>
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exportModal">
                        <i class="fas fa-file-export"></i> Export Data
                    </button>
                </div>
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
                    <th>Jenis/Program Bibit</th>
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
                            <td>
                                <?= htmlspecialchars($item['seedling_name'] ?? '-') ?><br>
                                <?php if(($item['program_type'] ?? 'Reguler') === 'FOLU'): ?>
                                    <span class="badge" style="background-color: #39FF14; color: #000;">FOLU</span>
                                <?php else: ?>
                                    <span class="badge badge-primary">Reguler</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= formatNumber($item['item_quantity'] ?: $item['quantity'] ?: 0) ?></strong></td>
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

<script nonce="<?= cspNonce() ?>">
document.addEventListener('DOMContentLoaded', function() {
    // Warn user when including photos
    const includePhoto = document.getElementById('include_photo');
    const photoWarning = document.getElementById('photoWarning');
    
    if (includePhoto) {
        includePhoto.addEventListener('change', function() {
            if (this.checked) {
                photoWarning.style.display = 'block';
            } else {
                photoWarning.style.display = 'none';
            }
        });
    }
    
    // Handle form action for Excel/PDF
    const btnExportExcel = document.getElementById('btnExportExcel');
    const btnExportPDF = document.getElementById('btnExportPDF');
    const exportForm = document.getElementById('exportForm');
    
    if (btnExportExcel) {
        btnExportExcel.addEventListener('click', function() {
            exportForm.action = '<?= url('export/requestsExcel') ?>';
            exportForm.target = '';
            exportForm.submit();
        });
    }
    
    if (btnExportPDF) {
        btnExportPDF.addEventListener('click', function() {
            exportForm.action = '<?= url('export/requestsPDF') ?>';
            exportForm.target = '_blank';
            exportForm.submit();
        });
    }
});
</script>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel"><i class="fas fa-file-export"></i> Export Rekapitulasi Permintaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="exportForm" method="GET">
                    <div class="mb-3">
                        <label class="form-label">Rentang Tanggal</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="start_date" id="start_date">
                            <span class="input-group-text">s/d</span>
                            <input type="date" class="form-control" name="end_date" id="end_date">
                        </div>
                        <small class="text-muted">Kosongkan jika ingin export semua tanggal.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status Permintaan</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <?php foreach (REQUEST_STATUS as $statusKey => $statusLabel): ?>
                                <option value="<?= $statusKey ?>">
                                    <?= htmlspecialchars($statusLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="include_photo" name="include_photo" value="yes">
                            <label class="form-check-label fw-bold" for="include_photo">Sertakan Foto Bukti/Lokasi (Ya/Tidak)</label>
                        </div>
                        <div id="photoWarning" class="alert alert-warning mt-2" style="display: none; padding: 0.5rem; font-size: 0.9em;">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Catatan:</strong> Mengunduh beserta foto akan memakan waktu lebih lama dan ukuran file menjadi lebih besar.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnExportPDF"><i class="fas fa-file-pdf"></i> PDF</button>
                <button type="button" class="btn btn-success" id="btnExportExcel"><i class="fas fa-file-excel"></i> Excel</button>
            </div>
        </div>
    </div>
</div>
