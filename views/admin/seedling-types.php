<?php
/**
 * Admin - Manage Seedling Types View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-seedling"></i> Kelola Jenis Bibit</h1>
    <a href="<?= url('admin/seedling-form') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Jenis Bibit
    </a>
</div>

<!-- Category Filter -->
<div class="card mb-3">
    <div class="card-body">
        <div class="filter-buttons">
            <a href="<?= url('admin/seedling-types') ?>" 
               class="btn btn-sm <?= !$currentCategory ? 'btn-primary' : 'btn-outline-primary' ?>">
                Semua (<?= array_sum(array_column($categories, 'count')) ?>)
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= url('admin/seedling-types?category=' . urlencode($cat['category'])) ?>" 
                   class="btn btn-sm <?= $currentCategory == $cat['category'] ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <?= htmlspecialchars($cat['category']) ?> (<?= $cat['count'] ?>)
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table id="seedlingTypesTable" class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Bibit</th>
                    <th>Nama Ilmiah</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($seedlingTypes)): ?>
                    <?php
                    // Get pagination data for row numbering
                    $currentPage = $pagination['page'] ?? 1;
                    $perPage = $pagination['perPage'] ?? ITEMS_PER_PAGE;
                    ?>
                    <?php foreach ($seedlingTypes as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 + (($currentPage - 1) * $perPage) ?></td>
                            <td><strong><?= htmlspecialchars($item['name'] ?? '-') ?></strong></td>
                            <td><em><?= htmlspecialchars($item['scientific_name'] ?? '-') ?></em></td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($item['category'] ?? '-') ?></span></td>
                            <td><?= htmlspecialchars(substr($item['description'] ?? '', 0, 50)) ?><?= strlen($item['description'] ?? '') > 50 ? '...' : '' ?></td>
                            <td>
                                <?php if (!empty($item['is_active'])): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= url('admin/seedling-form/' . $item['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteSeedlingType(<?= $item['id'] ?>)" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data jenis bibit</td>
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
    
    // Preserve category filter in pagination
    $queryParams = [
        'category' => $currentCategory ?? null
    ];
    
    renderPagination($pagination, 'admin/seedling-types', $queryParams);
}
?>

<script>
function deleteSeedlingType(id) {
    if (confirm('Yakin ingin menghapus jenis bibit ini?')) {
        fetch('<?= url('admin/delete-seedling-type/') ?>' + id, {
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
.filter-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
</style>
