<?php
/**
 * BPDAS - Profile View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-user"></i> Profil BPDAS</h1>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Informasi BPDAS</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Nama BPDAS:</th>
                        <td><strong><?= htmlspecialchars($bpdas['name']) ?></strong></td>
                    </tr>
                    <tr>
                        <th>Provinsi:</th>
                        <td><?= htmlspecialchars($bpdas['province_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Alamat:</th>
                        <td><?= nl2br(htmlspecialchars($bpdas['address'])) ?></td>
                    </tr>
                    <tr>
                        <th>Telepon:</th>
                        <td><?= htmlspecialchars($bpdas['phone'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= htmlspecialchars($bpdas['email'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Kontak Person:</th>
                        <td><?= htmlspecialchars($bpdas['contact_person'] ?? '-') ?></td>
                    </tr>
                    <?php if ($bpdas['latitude'] && $bpdas['longitude']): ?>
                    <tr>
                        <th>Koordinat:</th>
                        <td><?= htmlspecialchars($bpdas['latitude']) ?>, <?= htmlspecialchars($bpdas['longitude']) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Edit Profil Akun</h3>
            </div>
            <div class="card-body">
                <form action="<?= url('bpdas/updateProfile') ?>" method="POST">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">

                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>

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
                        <label class="form-label">Telepon</label>
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
