<?php
/**
 * BPDAS - Stock Form View (Add/Edit)
 */
$isEdit = isset($stock);
?>

<div class="page-header">
    <h1><i class="fas fa-boxes"></i> <?= $isEdit ? 'Edit' : 'Tambah' ?> Stok Bibit</h1>
    <a href="<?= url('bpdas/stock') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= url('bpdas/saveStock') ?>" method="POST">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $stock['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label required">Jenis Bibit</label>
                <select name="seedling_type_id" class="form-control" <?= $isEdit ? 'disabled' : '' ?> required>
                    <option value="">Pilih Jenis Bibit</option>
                    <?php foreach ($seedlingTypes as $type): ?>
                        <option value="<?= $type['id'] ?>" 
                                <?= ($isEdit && $stock['seedling_type_id'] == $type['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['name']) ?>
                            <?php if ($type['scientific_name']): ?>
                                (<?= htmlspecialchars($type['scientific_name']) ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($isEdit): ?>
                    <input type="hidden" name="seedling_type_id" value="<?= $stock['seedling_type_id'] ?>">
                    <small class="text-muted">Jenis bibit tidak dapat diubah. Untuk mengubah jenis, hapus stok ini dan buat yang baru.</small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label required">Jumlah Stok</label>
                <input type="number" name="quantity" class="form-control" 
                       value="<?= $isEdit ? $stock['quantity'] : '' ?>" 
                       min="0" step="1" required>
                <small class="text-muted">Masukkan jumlah total stok yang tersedia</small>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3" 
                          placeholder="Catatan tambahan tentang stok ini (opsional)"><?= $isEdit ? htmlspecialchars($stock['notes']) : '' ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="<?= url('bpdas/stock') ?>" class="btn btn-secondary">
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
