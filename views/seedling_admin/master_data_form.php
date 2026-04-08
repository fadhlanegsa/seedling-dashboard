<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="<?= url('seedling-admin') ?>">Penatausahaan</a></li>
            <li class="breadcrumb-item"><a href="<?= url('seedling-admin/master-data') ?>">Database</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $item ? 'Edit' : 'Tambah' ?> Data Master</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-bottom-primary">
                <div class="card-header py-3 bg-white border-bottom-0">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas <?= $item ? 'fa-edit' : 'fa-plus' ?> mr-2"></i> 
                        <?= $item ? 'Update Data Master' : 'Tambah Jenis Barang Baru' ?>
                    </h6>
                </div>
                <div class="card-body px-5 py-4">
                    <form action="<?= url('seedling-admin/save-master-data') ?>" method="POST">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                        <?php if ($item): ?>
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <?php endif; ?>

                        <div class="form-group row mb-4">
                            <label class="col-sm-4 col-form-label font-weight-bold">Kategori Barang</label>
                            <div class="col-sm-8">
                                <select name="category_code" id="category_code" class="form-control custom-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['code'] ?>" data-name="<?= $cat['name'] ?>" <?= ($item && $item['category_code'] == $cat['code']) ? 'selected' : '' ?>>
                                            <?= $cat['code'] ?>. <?= $cat['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="category" id="category_name" value="<?= $item ? $item['category'] : '' ?>">
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-sm-4 col-form-label font-weight-bold">Kode Item</label>
                            <div class="col-sm-3">
                                <input type="text" name="code" class="form-control" placeholder="A01" value="<?= $item ? $item['code'] : '' ?>">
                                <small class="text-muted">Opsional (Ref Blangko)</small>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-sm-4 col-form-label font-weight-bold">Nama Item (Spesifik)</label>
                            <div class="col-sm-8">
                                <input type="text" name="name" class="form-control" placeholder="Contoh: Benih Cempaka" value="<?= $item ? $item['name'] : '' ?>" required>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-sm-4 col-form-label font-weight-bold">Nama Ilmiah</label>
                            <div class="col-sm-8">
                                <input type="text" name="scientific_name" class="form-control" placeholder="Opsional" value="<?= $item ? $item['scientific_name'] : '' ?>">
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-sm-4 col-form-label font-weight-bold">Satuan Standar</label>
                            <div class="col-sm-4">
                                <select name="unit" class="form-control custom-select">
                                    <option value="kg" <?= ($item && $item['unit'] == 'kg') ? 'selected' : '' ?>>Berat (Kg, gr)</option>
                                    <option value="pcs" <?= ($item && $item['unit'] == 'pcs') ? 'selected' : '' ?>>Unit (Pcs)</option>
                                    <option value="m3" <?= ($item && $item['unit'] == 'm3') ? 'selected' : '' ?>>Volume (m3)</option>
                                    <option value="ml" <?= ($item && $item['unit'] == 'ml') ? 'selected' : '' ?>>Cair (ml/cc)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-sm-4 col-form-label font-weight-bold">Keterangan</label>
                            <div class="col-sm-8">
                                <textarea name="description" class="form-control" rows="3" placeholder="Opsional: Penjelasan barang"><?= $item ? $item['description'] : '' ?></textarea>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end mt-4">
                            <a href="<?= url('seedling-admin/master-data') ?>" class="btn btn-secondary mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm font-weight-bold">
                                <i class="fas fa-save mr-1"></i> Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryCode = document.getElementById('category_code');
    const categoryName = document.getElementById('category_name');

    categoryCode.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        categoryName.value = option.dataset.name || '';
    });
});
</script>
