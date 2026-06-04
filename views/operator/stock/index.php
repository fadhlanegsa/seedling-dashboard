<?php
$nurseryId = currentUser()['nursery_id'] ?? 'nursery';
$stocksJson = json_encode($stocks);
?>
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
                                    <?php $pt = $stock['program_type'] ?? 'Reguler'; ?>
                                    <?php if($pt === 'FOLU'): ?>
                                        <span class="badge" style="background-color: #39FF14; color: #000;">FOLU</span>
                                    <?php elseif($pt === 'RHL'): ?>
                                        <span class="badge badge-info text-white">RHL</span>
                                    <?php elseif($pt === 'bibitgratis' || $pt === 'PUB'): ?>
                                        <span class="badge badge-primary"><i class="fas fa-seedling mr-1"></i> PUB</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Reguler</span>
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
document.addEventListener('DOMContentLoaded', function() {
    const nurseryId = '<?= $nurseryId ?>';
    const isOnline = navigator.onLine;
    
    if (isOnline) {
        // Save current PHP-generated stock list to localStorage as snapshot
        const snapshot = {
            timestamp: new Date().toISOString(),
            stocks: <?= $stocksJson ?>
        };
        localStorage.setItem('stock_snapshot_' + nurseryId, JSON.stringify(snapshot));
    } else {
        // We are offline! Show offline banner and populate table from snapshot
        showOfflineStockBanner(nurseryId);
    }
});

function showOfflineStockBanner(nurseryId) {
    const cachedData = localStorage.getItem('stock_snapshot_' + nurseryId);
    
    // Add banner at the top of page
    const header = document.querySelector('.page-header');
    const banner = document.createElement('div');
    banner.className = 'alert alert-warning w-100 mb-4 shadow-sm d-flex align-items-center';
    banner.style.borderRadius = '12px';
    banner.style.borderLeft = '5px solid #ffc107';
    
    if (!cachedData) {
        banner.innerHTML = `
            <i class="fas fa-exclamation-triangle text-warning mr-3" style="font-size: 1.25rem;"></i>
            <div>
                <strong>Mode Offline Aktif</strong> — Data stok tidak tersedia secara offline. Hubungkan ke internet untuk menyegarkan data.
            </div>
        `;
        header.parentNode.insertBefore(banner, header.nextSibling);
        
        const tbody = document.querySelector('#stockTable tbody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Data offline tidak tersedia. Harap hubungkan ke internet terlebih dahulu.</td></tr>';
        }
        
        // Hide pagination
        const pagination = document.querySelector('.pagination');
        if (pagination) pagination.parentNode.style.display = 'none';
        return;
    }
    
    const snapshot = JSON.parse(cachedData);
    const date = new Date(snapshot.timestamp);
    const dateFormatted = date.toLocaleDateString('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    }) + ' WIB';
    
    banner.innerHTML = `
        <i class="fas fa-exclamation-triangle text-warning mr-3" style="font-size: 1.25rem;"></i>
        <div>
            <strong>Mode Offline Aktif</strong> — Menampilkan data terakhir per ${dateFormatted}
        </div>
    `;
    header.parentNode.insertBefore(banner, header.nextSibling);
    
    // Hide 'Tambah Stok' and disable filter form
    const filterForm = document.querySelector('.filter-form');
    if (filterForm) {
        const addBtn = filterForm.querySelector('a.btn-primary');
        if (addBtn) addBtn.style.display = 'none';
        
        const inputs = filterForm.querySelectorAll('select, button[type="submit"]');
        inputs.forEach(input => input.disabled = true);
    }
    
    // Repopulate table from snapshot
    const tbody = document.querySelector('#stockTable tbody');
    if (tbody && snapshot.stocks && snapshot.stocks.data) {
        tbody.innerHTML = '';
        if (snapshot.stocks.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Belum ada data stok</td></tr>';
        } else {
            snapshot.stocks.data.forEach((stock, index) => {
                const tr = document.createElement('tr');
                
                // Format quantity
                const qty = Number(stock.quantity).toLocaleString('id-ID');
                
                // Program badge
                const pt = stock.program_type || 'Reguler';
                let programBadge = '<span class="badge badge-success">Reguler</span>';
                if (pt === 'FOLU') {
                    programBadge = '<span class="badge" style="background-color: #39FF14; color: #000;">FOLU</span>';
                } else if (pt === 'RHL') {
                    programBadge = '<span class="badge badge-info text-white">RHL</span>';
                } else if (pt === 'bibitgratis' || pt === 'PUB') {
                    programBadge = '<span class="badge badge-primary"><i class="fas fa-seedling mr-1"></i> PUB</span>';
                }
                
                // Last update date formatting
                let updateDateFormatted = '-';
                if (stock.last_update_date) {
                    const updateDate = new Date(stock.last_update_date);
                    updateDateFormatted = updateDate.toLocaleDateString('id-ID', {
                        day: '2-digit', month: '2-digit', year: 'numeric'
                    });
                }
                
                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${stock.seedling_name}<br><small class="text-muted"><em>${stock.scientific_name || ''}</em></small></td>
                    <td><span class="badge badge-secondary">${stock.category}</span></td>
                    <td>${programBadge}</td>
                    <td class="font-weight-bold text-primary">${qty}</td>
                    <td>${stock.notes || '-'}</td>
                    <td>${updateDateFormatted}</td>
                    <td>
                        <span class="text-muted small"><i class="fas fa-lock mr-1"></i> Terkunci (Offline)</span>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    }
    
    // Update total quantity display from snapshot
    const totalQtyEl = document.querySelector('.total-stock-summary .value');
    if (totalQtyEl && snapshot.stocks && snapshot.stocks.total_quantity !== undefined) {
        totalQtyEl.innerHTML = `${Number(snapshot.stocks.total_quantity).toLocaleString('id-ID')} <span class="unit" style="font-size: 0.9rem; font-weight: 400; color: #666;">bibit</span>`;
    }
    
    // Hide pagination when offline
    const pagination = document.querySelector('.pagination');
    if (pagination) pagination.parentNode.style.display = 'none';
}

function deleteStock(id) {
    if (!navigator.onLine) {
        alert('Tidak dapat menghapus stok saat offline.');
        return;
    }
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
