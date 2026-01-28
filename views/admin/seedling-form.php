<?php
/**
 * Admin - Seedling Type Form View (Add/Edit)
 */
$isEdit = isset($seedlingType);
?>

<div class="page-header">
    <h1><i class="fas fa-seedling"></i> <?= $isEdit ? 'Edit' : 'Tambah' ?> Jenis Bibit</h1>
    <a href="<?= url('admin/seedling-types') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= url('admin/saveSeedlingType') ?>" method="POST">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $seedlingType['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label required">Nama Bibit</label>
                <input type="text" name="name" class="form-control" 
                       value="<?= $isEdit ? htmlspecialchars($seedlingType['name']) : '' ?>" 
                       placeholder="Contoh: Jati" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Ilmiah</label>
                <input type="text" name="scientific_name" class="form-control" 
                       value="<?= $isEdit ? htmlspecialchars($seedlingType['scientific_name']) : '' ?>" 
                       placeholder="Contoh: Tectona grandis">
            </div>

            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="category" class="form-control">
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" 
                                <?= ($isEdit && $seedlingType['category'] == $cat) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="4" 
                          placeholder="Deskripsi singkat tentang jenis bibit ini"><?= $isEdit ? htmlspecialchars($seedlingType['description']) : '' ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="<?= url('admin/seedling-types') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.form-label.required::after {
    content: ' *';
    color: red;
}
</style>
