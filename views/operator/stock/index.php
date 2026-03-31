<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-boxes"></i> Kelola Stok</h1>
        <p class="text-muted"><?= $nursery_name ?></p>
        
        <div class="total-stock-summary mt-2 shadow-sm">
            <div class="label">TOTAL STOK KESELURUHAN</div>
            <div class="value"><?= number_format($stocks['total_quantity'] ?? 0) ?> <span class="unit">bibit</span></div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="<?= url('operator/stock') ?>" class="filter-form d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
            <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                <!-- Filter Types -->
                <select name="seedling_type_id" class="form-control form-control-sm" style="width: 150px;">
                    <option value="">Semua Bibit</option>
                    <?php if(!empty($seedlingTypes)): foreach ($seedlingTypes as $type): ?>
                        <option value="<?= $type['id'] ?>" <?= (isset($filters['seedling_type_id']) && $filters['seedling_type_id'] == $type['id']) ? 'selected' : '' ?>><?= $type['name'] ?></option>
                    <?php endforeach; endif; ?>
                </select>
                
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
                $exportQuery = '';
                if (!empty($filters['seedling_type_id'])) $exportQuery .= '&seedling_type_id=' . $filters['seedling_type_id'];
                if (!empty($filters['month'])) $exportQuery .= '&month=' . $filters['month'];
                if (!empty($filters['year'])) $exportQuery .= '&year=' . $filters['year'];
                if (!empty($currentProgram)) $exportQuery .= '&program_type=' . $currentProgram;
                ?>
                <a href="<?= url('export/stockExcel') . '?' . ltrim($exportQuery, '&') ?>" class="btn btn-success btn-sm" target="_blank">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="<?= url('export/stockPDF') . '?' . ltrim($exportQuery, '&') ?>" class="btn btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
            
            <a href="<?= url('operator/stock/add') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Stok
            </a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="stockTable">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Jenis Bibit</th>
                        <th>Kategori</th>
                        <th>Program</th>
                        <th>Jumlah Stok</th>
                        <th>Catatan</th>
                        <th>Terakhir Update</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($stocks['data'])): ?>
                        <?php foreach ($stocks['data'] as $index => $stock): ?>
                            <tr>
                                <td><?= $index + 1 + (($stocks['page'] - 1) * $stocks['perPage']) ?></td>
                                <td><?= $stock['seedling_name'] ?><br><small class="text-muted"><em><?= $stock['scientific_name'] ?></em></small></td>
                                <td><span class="badge badge-secondary"><?= $stock['category'] ?></span></td>
                                <td>
                                    <?php if(($stock['program_type'] ?? 'Reguler') === 'FOLU'): ?>
                                        <span class="badge" style="background-color: #39FF14; color: #000;">FOLU</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary">Reguler</span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-weight-bold text-primary"><?= number_format($stock['quantity']) ?></td>
                                <td><?= $stock['notes'] ?? '-' ?></td>
                                <td><?= date('d/m/Y', strtotime($stock['last_update_date'])) ?></td>
                                <td>
                                    <a href="<?= url('operator/stock/edit/' . $stock['id']) ?>" class="btn btn-sm btn-warning mb-1" title="Edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button onclick="deleteStock(<?= $stock['id'] ?>)" class="btn btn-sm btn-danger mb-1" title="Hapus">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($stocks['totalPages'] > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php 
                        $queryParams = $_GET;
                        unset($queryParams['page']);
                        $queryString = http_build_query($queryParams);
                    ?>
                    <?php for ($i = 1; $i <= $stocks['totalPages']; $i++): ?>
                        <li class="page-item <?= $i == $stocks['page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= !empty($queryString) ? '&' . $queryString : '' ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script nonce="<?= cspNonce() ?>">
function deleteStock(id) {
    if (confirm('Yakin ingin menghapus stok ini?')) {
        fetch('<?= url('operator/delete-stock/') ?>' + id, {
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
.total-stock-summary {
    background: #fff;
    border-left: 4px solid #2d5016;
    padding: 10px 20px;
    display: inline-block;
    border-radius: 4px;
}

.total-stock-summary .label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #666;
    letter-spacing: 1px;
}

.total-stock-summary .value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #2d5016;
}

.total-stock-summary .unit {
    font-size: 0.9rem;
    font-weight: 400;
    color: #666;
}
</style>
