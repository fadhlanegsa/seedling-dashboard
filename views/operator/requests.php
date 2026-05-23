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
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
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
            <div class="export-buttons">
                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#exportModal">
                    <i class="fas fa-file-export"></i> Export Data
                </button>
            </div>
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
                                    <?= htmlspecialchars($request['requester_name'] ?? '-') ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($request['requester_email'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <?= htmlspecialchars($request['seedling_name'] ?? '-') ?><br>
                                    <?php if(($request['program_type'] ?? 'Reguler') === 'FOLU'): ?>
                                        <span class="badge" style="background-color: #39FF14; color: #000;">FOLU</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary">Reguler</span>
                                    <?php endif; ?>
                                </td>
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
