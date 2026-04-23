<?php
/**
 * Operator Persemaian - Profile View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-user-edit"></i> Profil Operator Persemaian</h1>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Informasi Persemaian</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Nama Persemaian:</th>
                        <td><strong><?= htmlspecialchars($user['nursery_name'] ?? '-') ?></strong></td>
                    </tr>
                    <tr>
                        <th>BPDAS Induk:</th>
                        <td><?= htmlspecialchars($user['bpdas_name'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Provinsi:</th>
                        <td><?= htmlspecialchars($user['province_name'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Status Wewenang (ACC):</th>
                        <td>
                            <?php if (!empty($can_approve)): ?>
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Aktif</span>
                                <div class="small text-muted mt-1">Anda diizinkan memproses ACC/Tolak permintaan bibit.</div>
                            <?php else: ?>
                                <span class="badge badge-secondary"><i class="fas fa-times-circle"></i> Tidak Aktif</span>
                                <div class="small text-muted mt-1">Wewenang ACC/Tolak hanya dilakukan oleh BPDAS Induk.</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Pengaturan wewenang (ACC/Tolak) dikelola sepenuhnya oleh BPDAS Induk.
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Edit Profil Akun</h3>
            </div>
            <div class="card-body">
                <form action="<?= url('operator/updateProfile') ?>" method="POST">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">

                    <div class="form-group">
                        <label class="form-label required">Username</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Nama Lengkap</label>
                        <input type="text" name="full_name" class="form-control" 
                               value="<?= htmlspecialchars($user['full_name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" 
                               value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>

                    <hr>

                    <h4>Ubah Password</h4>
                    <p class="text-muted">Kosongkan jika tidak ingin mengubah password</p>

                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" 
                               placeholder="Minimal 6 karakter">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.form-label.required::after {
    content: ' *';
    color: red;
}
</style>
