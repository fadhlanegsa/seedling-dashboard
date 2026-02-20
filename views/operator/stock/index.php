<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-boxes"></i> Kelola Stok</h1>
        <p class="text-muted"><?= $nursery_name ?></p>
    </div>
    <a href="<?= url('operator/stock/add') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah / Update Stok
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="stockTable">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Jenis Bibit</th>
                        <th>Nama Ilmiah</th>
                        <th>Kategori</th>
                        <th>Jumlah Stok</th>
                        <th>Catatan</th>
                        <th>Terakhir Update</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stocks['data'])): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">Belum ada data stok</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($stocks['data'] as $index => $stock): ?>
                            <tr>
                                <td><?= $index + 1 + (($stocks['page'] - 1) * $stocks['perPage']) ?></td>
                                <td><?= $stock['seedling_name'] ?></td>
                                <td><em><?= $stock['scientific_name'] ?></em></td>
                                <td><span class="badge badge-secondary"><?= $stock['category'] ?></span></td>
                                <td class="font-weight-bold text-primary"><?= number_format($stock['quantity']) ?></td>
                                <td><?= $stock['notes'] ?? '-' ?></td>
                                <td><?= date('d/m/Y', strtotime($stock['last_update_date'])) ?></td>
                                <td>
                                    <a href="<?= url('operator/stock/edit/' . $stock['id']) ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
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
                    <?php for ($i = 1; $i <= $stocks['totalPages']; $i++): ?>
                        <li class="page-item <?= $i == $stocks['page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
