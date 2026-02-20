<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-tree"></i> Kelola Persemaian</h1>
        <p>Manajemen data persemaian di bawah BPDAS</p>
    </div>
    <a href="<?= url('admin/nurseries/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Persemaian
    </a>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="" method="GET" class="form-inline">
            <div class="form-group mr-2">
                <label for="bpdas_id" class="mr-2">Filter BPDAS:</label>
                <select name="bpdas_id" id="bpdas_id" class="form-control">
                    <option value="">-- Semua BPDAS --</option>
                    <?php foreach ($bpdas_list as $bpdas): ?>
                        <option value="<?= $bpdas['id'] ?>" <?= $selected_bpdas == $bpdas['id'] ? 'selected' : '' ?>>
                            <?= $bpdas['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Persemaian</th>
                        <th>Induk BPDAS</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($nurseries)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">Belum ada data persemaian</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($nurseries as $nursery): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td class="font-weight-bold"><?= $nursery['name'] ?></td>
                                <td><?= $nursery['bpdas_name'] ?></td>
                                <td><?= $nursery['address'] ?? '-' ?></td>
                                <td>
                                    <?php if ($nursery['is_active']): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Non-aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= url('admin/nurseries/edit/' . $nursery['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= url('admin/nurseries/delete/' . $nursery['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus persemaian ini?');">
                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>
