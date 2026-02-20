<?php
/**
 * BPDAS - Manage Stock View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-boxes"></i> Kelola Stok Bibit</h1>
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="<?= url('bpdas/stock') ?>" class="filter-form d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    <select name="month" class="form-control form-control-sm" style="width: 120px;">
                        <option value="">Semua Bulan</option>
                        <?php 
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        foreach ($months as $num => $name): 
                        ?>
                            <option value="<?= $num ?>" <?= (isset($filters['month']) && $filters['month'] == $num) ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="year" class="form-control form-control-sm" style="width: 100px;">
                        <option value="">Semua Tahun</option>
                        <?php 
                        $currentYear = date('Y');
                        for ($y = $currentYear; $y >= $currentYear - 2; $y--): 
                        ?>
                            <option value="<?= $y ?>" <?= (isset($filters['year']) && $filters['year'] == $y) ? 'selected' : '' ?>>
                                <?= $y ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    
                    <?php 
                    // Prepare filter query for export
                    $exportQuery = 'bpdas_id=' . currentUser()['bpdas_id'];
                    if (!empty($filters['month'])) $exportQuery .= '&month=' . $filters['month'];
                    if (!empty($filters['year'])) $exportQuery .= '&year=' . $filters['year'];
                    ?>
                    
                    <a href="<?= url('export/stockExcel') . '?' . $exportQuery ?>" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="<?= url('export/stockPDF') . '?' . $exportQuery ?>" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
                
                <a href="<?= url('bpdas/stock-form') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Stok
                </a>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Daftar Stok Bibit</h3>
            <div class="filter-group">
                <label>Tampilkan:</label>
                <select id="perPageSelect" class="form-control form-control-sm" style="width: auto; display: inline-block;">
                    <option value="10" <?= ($currentPerPage ?? ITEMS_PER_PAGE) == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= ($currentPerPage ?? ITEMS_PER_PAGE) == 20 ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= ($currentPerPage ?? ITEMS_PER_PAGE) == 50 ? 'selected' : '' ?>>50</option>
                    <option value="all" <?= ($currentPerPage ?? ITEMS_PER_PAGE) == 'all' ? 'selected' : '' ?>>Semua</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Persemaian</th>
                    <th>Jenis Bibit</th>
                    <th>Nama Ilmiah</th>
                    <th>Kategori</th>
                    <th>Jumlah Stok</th>
                    <th>Terakhir Update</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($stock)): ?>
                    <?php 
                    $currentPage = $pagination['page'] ?? 1;
                    $perPage = $pagination['perPage'] ?? ITEMS_PER_PAGE;
                    foreach ($stock as $index => $item): 
                        $rowNumber = $index + 1 + (($currentPage - 1) * $perPage);
                    ?>
                        <tr>
                            <td><?= $rowNumber ?></td>
                            <td><?= htmlspecialchars($item['nursery_name'] ?? '-') ?></td>
                            <td><strong><?= htmlspecialchars($item['seedling_name'] ?? '-') ?></strong></td>
                            <td><em><?= htmlspecialchars($item['scientific_name'] ?? '-') ?></em></td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($item['category'] ?? '-') ?></span></td>
                            <td><strong><?= formatNumber($item['quantity'] ?? 0) ?></strong> bibit</td>
                            <td><?= isset($item['last_update_date']) ? formatDate($item['last_update_date'], DATE_FORMAT) : '-' ?></td>
                            <td><?= htmlspecialchars(substr($item['notes'] ?? '', 0, 30)) ?><?= strlen($item['notes'] ?? '') > 30 ? '...' : '' ?></td>
                            <td>
                                <a href="<?= url('bpdas/stock-form/' . $item['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteStock(<?= $item['id'] ?>)" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Belum ada data stok</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if (isset($pagination) && ($pagination['totalPages'] ?? 0) > 1): ?>
            <?php
            $currentPage = $pagination['page'] ?? 1;
            $totalPages = $pagination['totalPages'] ?? 1;
            $perPageParam = isset($currentPerPage) && $currentPerPage != ITEMS_PER_PAGE ? '&per_page=' . $currentPerPage : '';
            ?>
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Halaman <?= $currentPage ?> dari <?= $totalPages ?> (Total: <?= formatNumber($pagination['total'] ?? 0) ?> data)
                </div>
                <div class="pagination">
                    <!-- Previous Button -->
                    <?php if ($currentPage > 1): ?>
                        <a href="<?= url('bpdas/stock?page=' . ($currentPage - 1) . $perPageParam) ?>" class="page-link">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php else: ?>
                        <span class="page-link disabled">
                            <i class="fas fa-chevron-left"></i> Previous
                        </span>
                    <?php endif; ?>

                    <!-- Page Numbers -->
                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                    
                    if ($startPage > 1): ?>
                        <a href="<?= url('bpdas/stock?page=1' . $perPageParam) ?>" class="page-link">1</a>
                        <?php if ($startPage > 2): ?>
                            <span class="page-link disabled">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a href="<?= url('bpdas/stock?page=' . $i . $perPageParam) ?>" 
                           class="page-link <?= $i == $currentPage ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <span class="page-link disabled">...</span>
                        <?php endif; ?>
                        <a href="<?= url('bpdas/stock?page=' . $totalPages . $perPageParam) ?>" class="page-link"><?= $totalPages ?></a>
                    <?php endif; ?>

                    <!-- Next Button -->
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= url('bpdas/stock?page=' . ($currentPage + 1) . $perPageParam) ?>" class="page-link">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="page-link disabled">
                            Next <i class="fas fa-chevron-right"></i>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Per page filter
document.getElementById('perPageSelect').addEventListener('change', function() {
    const perPage = this.value;
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('per_page', perPage);
    currentUrl.searchParams.set('page', '1'); // Reset to first page
    window.location.href = currentUrl.toString();
});

function deleteStock(id) {
    if (confirm('Yakin ingin menghapus stok ini?')) {
        fetch('<?= url('bpdas/delete-stock/') ?>' + id, {
            method: 'POST'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan: ' + error);
            });
    }
}
</script>

<style>
.filter-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-group label {
    margin: 0;
    font-weight: 500;
}

.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.pagination-info {
    color: var(--text-light);
    font-size: 0.875rem;
}

.pagination {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
}

.page-link {
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    color: var(--primary-color);
    text-decoration: none;
    transition: all 0.2s;
}

.page-link:hover:not(.disabled) {
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}

.page-link.active {
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
    font-weight: 600;
}

.page-link.disabled {
    color: var(--text-light);
    cursor: not-allowed;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .pagination-wrapper {
        flex-direction: column;
        gap: 1rem;
    }
    
    .pagination {
        justify-content: center;
    }
}
</style>
