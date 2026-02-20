<div class="page-header">
    <h1><i class="fas fa-home"></i> Dashboard Operator</h1>
    <p>Selamat datang, <strong><?= $user['full_name'] ?></strong></p>
    <p class="text-muted"><i class="fas fa-building"></i> <?= $bpdas_name ?> - <i class="fas fa-leaf"></i> <?= $nursery_name ?></p>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Stok Bibit</h6>
                        <h2 class="mt-2 mb-0">
                            <?= number_format(array_sum(array_column($stocks['data'], 'quantity'))) ?>
                        </h2>
                    </div>
                    <i class="fas fa-boxes fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Jenis Bibit</h6>
                        <h2 class="mt-2 mb-0"><?= count($stocks['data']) ?></h2>
                    </div>
                    <i class="fas fa-seedling fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Stok Bibit di <?= $nursery_name ?></h5>
        <a href="<?= url('operator/stock') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i> Kelola Stok
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Jenis Bibit</th>
                        <th>Nama Ilmiah</th>
                        <th>Kategori</th>
                        <th>Jumlah Stok</th>
                        <th>Terakhir Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stocks['data'])): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data stok</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($stocks['data'] as $stock): ?>
                            <tr>
                                <td><?= $stock['seedling_name'] ?></td>
                                <td><em><?= $stock['scientific_name'] ?></em></td>
                                <td><span class="badge badge-info"><?= $stock['category'] ?></span></td>
                                <td class="font-weight-bold"><?= number_format($stock['quantity']) ?></td>
                                <td><?= date('d M Y', strtotime($stock['last_update_date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
