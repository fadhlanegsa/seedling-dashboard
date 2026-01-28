<?php
/**
 * Admin - User Form View (Add/Edit)
 */
$isEdit = isset($user);
?>

<div class="page-header">
    <h1><i class="fas fa-user"></i> <?= $isEdit ? 'Edit' : 'Tambah' ?> Pengguna</h1>
    <a href="<?= url('admin/users') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= url('admin/saveUser') ?>" method="POST">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $user['id'] ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label required">Username</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?= $isEdit ? htmlspecialchars($user['username']) : '' ?>" 
                               <?= $isEdit ? 'readonly' : '' ?> required>
                        <?php if ($isEdit): ?>
                            <small class="text-muted">Username tidak dapat diubah</small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= $isEdit ? htmlspecialchars($user['email']) : '' ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label required">Nama Lengkap</label>
                <input type="text" name="full_name" class="form-control" 
                       value="<?= $isEdit ? htmlspecialchars($user['full_name']) : '' ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control" 
                               value="<?= $isEdit ? htmlspecialchars($user['phone']) : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label required">Role</label>
                        <select name="role" class="form-control" id="roleSelect" required>
                            <option value="">Pilih Role</option>
                            <?php foreach (USER_ROLES as $roleKey => $roleLabel): ?>
                                <option value="<?= $roleKey ?>" 
                                        <?= ($isEdit && $user['role'] == $roleKey) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($roleLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group" id="bpdasGroup" style="display: none;">
                <label class="form-label">BPDAS</label>
                <select name="bpdas_id" class="form-control">
                    <option value="">Pilih BPDAS</option>
                    <?php foreach ($bpdasList as $bpdas): ?>
                        <option value="<?= $bpdas['id'] ?>" 
                                <?= ($isEdit && $user['bpdas_id'] == $bpdas['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($bpdas['name']) ?> - <?= htmlspecialchars($bpdas['province_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Hanya untuk role BPDAS</small>
            </div>

            <div class="form-group">
                <label class="form-label <?= !$isEdit ? 'required' : '' ?>">Password</label>
                <input type="password" name="password" class="form-control" 
                       placeholder="<?= $isEdit ? 'Kosongkan jika tidak ingin mengubah password' : 'Minimal 6 karakter' ?>" 
                       <?= !$isEdit ? 'required' : '' ?>>
                <?php if ($isEdit): ?>
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center;">
                    <input type="checkbox" name="is_active" value="1" 
                           <?= (!$isEdit || $user['is_active']) ? 'checked' : '' ?> 
                           style="margin-right: 0.5rem;">
                    Akun Aktif
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="<?= url('admin/users') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function() {
    const bpdasGroup = document.getElementById('bpdasGroup');
    if (this.value === 'bpdas') {
        bpdasGroup.style.display = 'block';
    } else {
        bpdasGroup.style.display = 'none';
    }
});

// Trigger on page load
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('roleSelect');
    if (roleSelect.value === 'bpdas') {
        document.getElementById('bpdasGroup').style.display = 'block';
    }
});
</script>

<style>
.form-label.required::after {
    content: ' *';
    color: red;
}
</style>
