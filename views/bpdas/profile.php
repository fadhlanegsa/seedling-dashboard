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

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark">
                <h3><i class="fas fa-users-cog"></i> Pengaturan Pendelegasian Wewenang</h3>
            </div>
            <div class="card-body">
                <form action="<?= url('bpdas/updateDelegation') ?>" method="POST">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Info:</strong> Aktifkan fitur ini jika Anda ingin memberikan wewenang kepada 
                        <strong>Operator Persemaian</strong> untuk menyetujui atau menolak permintaan bibit secara mandiri 
                        melalui dashboard mereka.
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="can_operator_approve" id="can_operator_approve" 
                               value="1" <?= ($bpdas['can_operator_approve'] ?? 0) ? 'checked' : '' ?> 
                               style="width: 50px; height: 25px; cursor: pointer;">
                        <label class="form-check-label ps-2" for="can_operator_approve" style="cursor: pointer; font-weight: 600;">
                            Izinkan Operator Persemaian menyetujui permintaan bibit secara mandiri
                        </label>
                    </div>

                    <p class="text-muted small">
                        <i class="fas fa-exclamation-triangle"></i> Jika dimatikan (default), 
                        seluruh persetujuan bibit harus dilakukan oleh akun BPDAS.
                    </p>

                    <div class="form-actions mt-3">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-check-circle"></i> Simpan Pengaturan Delegasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.form-check-input:checked {
    background-color: #ffc107;
    border-color: #ffc107;
}
.form-label.required::after {
    content: ' *';
    color: red;
}
</style>
