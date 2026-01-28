<?php
/**
 * Admin - Manage National Stock View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-boxes"></i> Kelola Stok Nasional</h1>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?= url('admin/stock') ?>" class="filter-form">
            <div class="row">
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Jenis Bibit</label>
                        <select name="seedling_type_id" class="form-control">
                            <option value="">Semua Jenis</option>
                            <?php foreach ($seedlingTypes as $type): ?>
                                <option value="<?= $type['id'] ?>" 
                                        <?= ($filters['seedling_type_id'] == $type['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($type['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-control">
                            <option value="">Semua Kategori</option>
                            <?php foreach (SEEDLING_CATEGORIES as $cat): ?>
                                <option value="<?= $cat ?>" 
                                        <?= ($filters['category'] == $cat) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat) ?>
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
                <a href="<?= url('admin/stock') ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Stock Table -->
<div class="card">
    <div class="card-body">
        <table id="stockTable" class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>BPDAS</th>
                    <th>Provinsi</th>
                    <th>Jenis Bibit</th>
                    <th>Kategori</th>
                    <th>Jumlah Stok</th>
                    <th>Terakhir Update</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($stock)): ?>
                    <?php
                    // Get pagination data for row numbering
                    $currentPage = $pagination['page'] ?? 1;
                    $perPage = $pagination['perPage'] ?? ITEMS_PER_PAGE;
                    ?>
                    <?php foreach ($stock as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 + (($currentPage - 1) * $perPage) ?></td>
                            <td><?= htmlspecialchars($item['bpdas_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($item['province_name'] ?? '-') ?></td>
                            <td>
                                <strong><?= htmlspecialchars($item['seedling_name'] ?? '-') ?></strong>
                                <?php if (!empty($item['scientific_name'])): ?>
                                    <br><em class="text-muted"><?= htmlspecialchars($item['scientific_name']) ?></em>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($item['category'] ?? '-') ?></span></td>
                            <td><strong><?= formatNumber($item['quantity'] ?? 0) ?></strong> bibit</td>
                            <td><?= isset($item['last_update_date']) ? formatDate($item['last_update_date'], DATETIME_FORMAT) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data stok</td>
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
        'province_id' => $filters['province_id'] ?? null,
        'bpdas_id' => $filters['bpdas_id'] ?? null,
        'seedling_type_id' => $filters['seedling_type_id'] ?? null,
        'category' => $filters['category'] ?? null
    ];
    
    renderPagination($pagination, 'admin/stock', $queryParams);
}
?>

<style>
.filter-form .form-group {
    margin-bottom: 1rem;
}
</style>
