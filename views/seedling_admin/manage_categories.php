<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800 font-weight-bold text-uppercase text-primary">KELOLA KATEGORI BARANG</h2>
        <a href="<?= url('seedling-admin/master-data') ?>" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Database
        </a>
    </div>

    <?php if ($flash = $this->getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show shadow-sm">
            <?= $flash['message'] ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- List Categories -->
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Kategori Masa Kini</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light text-dark small font-weight-bold">
                                <tr>
                                    <th width="100">Kode</th>
                                    <th>Nama Kategori</th>
                                    <th width="120" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td class="font-weight-bold text-primary"><?= $cat['code'] ?></td>
                                        <td><?= $cat['name'] ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info mr-1" onclick="editCategory(<?= htmlspecialchars(json_encode($cat)) ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="<?= url('seedling-admin/delete-category/' . $cat['id']) ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Form -->
        <div class="col-lg-5">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary" id="formTitle">Tambah Kategori Baru</h6>
                </div>
                <div class="card-body">
                    <form action="<?= url('seedling-admin/save-category') ?>" method="POST" id="categoryForm">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                        <input type="hidden" name="id" id="cat_id" value="">

                        <div class="form-group">
                            <label class="font-weight-bold">Kode Kategori</label>
                            <input type="text" name="code" id="cat_code" class="form-control" placeholder="Contoh: G, H, AA" required maxlength="5">
                            <small class="text-muted">Gunakan huruf kapital (A, B, C, dst)</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Nama Kategori</label>
                            <input type="text" name="name" id="cat_name" class="form-control" placeholder="Contoh: PERALATAN, DLL" required>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-light border" onclick="resetForm()">Reset</button>
                            <button type="submit" class="btn btn-primary px-4 font-weight-bold">Simpan Kategori</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editCategory(cat) {
    document.getElementById('formTitle').innerText = 'Edit Kategori: ' + cat.name;
    document.getElementById('cat_id').value = cat.id;
    document.getElementById('cat_code').value = cat.code;
    document.getElementById('cat_name').value = cat.name;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('formTitle').innerText = 'Tambah Kategori Baru';
    document.getElementById('cat_id').value = '';
    document.getElementById('cat_code').value = '';
    document.getElementById('cat_name').value = '';
}
</script>
