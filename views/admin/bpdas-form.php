<?php
/**
 * Admin - BPDAS Form View (Add/Edit)
 */
$isEdit = isset($bpdas);
?>

<div class="page-header">
    <h1><i class="fas fa-building"></i> <?= $isEdit ? 'Edit' : 'Tambah' ?> BPDAS</h1>
    <a href="<?= url('admin/bpdas') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= url('admin/saveBPDAS') ?>" method="POST">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $bpdas['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label required">Nama BPDAS</label>
                <input type="text" name="name" class="form-control" 
                       value="<?= $isEdit ? htmlspecialchars($bpdas['name']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label required">Provinsi</label>
                <select name="province_id" class="form-control" required>
                    <option value="">Pilih Provinsi</option>
                    <?php foreach ($provinces as $province): ?>
                        <option value="<?= $province['id'] ?>" 
                                <?= ($isEdit && $bpdas['province_id'] == $province['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($province['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label required">Alamat</label>
                <textarea name="address" class="form-control" rows="3" required><?= $isEdit ? htmlspecialchars($bpdas['address']) : '' ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control" 
                               value="<?= $isEdit ? htmlspecialchars($bpdas['phone']) : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= $isEdit ? htmlspecialchars($bpdas['email']) : '' ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Kontak Person</label>
                <input type="text" name="contact_person" class="form-control" 
                       value="<?= $isEdit ? htmlspecialchars($bpdas['contact_person']) : '' ?>">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control" 
                               value="<?= $isEdit ? htmlspecialchars($bpdas['latitude']) : '' ?>" 
                               placeholder="Contoh: -6.200000">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control" 
                               value="<?= $isEdit ? htmlspecialchars($bpdas['longitude']) : '' ?>" 
                               placeholder="Contoh: 106.816666">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="<?= url('admin/bpdas') ?>" class="btn btn-secondary">
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
