<?php
/**
 * BPDAS - Profile View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-user"></i> Profil BPDAS</h1>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-edit"></i> Edit Profil BPDAS & Akun</h3>
            </div>
            <div class="card-body">
                <form action="<?= url('bpdas/updateProfile') ?>" method="POST">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                    
                    <div class="row">
                        <!-- BPDAS Information -->
                        <div class="col-md-6">
                            <h4>Informasi BPDAS</h4>
                            <div class="form-group mb-3">
                                <label class="form-label required">Nama BPDAS</label>
                                <input type="text" name="bpdas_name" class="form-control" value="<?= htmlspecialchars($bpdas['name']) ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="bpdas_address" class="form-control" rows="3"><?= htmlspecialchars($bpdas['address']) ?></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Telepon BPDAS</label>
                                <input type="text" name="bpdas_phone" class="form-control" value="<?= htmlspecialchars($bpdas['phone'] ?? '') ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Email BPDAS</label>
                                <input type="email" name="bpdas_email" class="form-control" value="<?= htmlspecialchars($bpdas['email'] ?? '') ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Kontak Person</label>
                                <input type="text" name="bpdas_contact_person" class="form-control" value="<?= htmlspecialchars($bpdas['contact_person'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="col-md-6 border-left">
                            <h4>Informasi Akun</h4>
                            <div class="form-group mb-3">
                                <label class="form-label required">Username</label>
                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                            </div>
        
                            <div class="form-group mb-3">
                                <label class="form-label required">Nama Lengkap Pengguna</label>
                                <input type="text" name="full_name" class="form-control" 
                                       value="<?= htmlspecialchars($user['full_name']) ?>" required>
                            </div>
        
                            <div class="form-group mb-3">
                                <label class="form-label required">Email Akun</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
        
                            <div class="form-group mb-3">
                                <label class="form-label">Telepon Pengguna</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="<?= htmlspecialchars($user['phone']) ?>">
                            </div>
        
                            <hr>
        
                            <h4>Ubah Password</h4>
                            <p class="text-muted small">Kosongkan jika tidak ingin mengubah password</p>
        
                            <div class="form-group mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="new_password" class="form-control" 
                                       placeholder="Minimal 6 karakter">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions mt-4 text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Simpan Semua Perubahan
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
