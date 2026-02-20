<?php
/**
 * Public User Profile Page
 */
?>

<div class="page-header">
    <h1><i class="fas fa-user"></i> Profil Saya</h1>
    <p>Kelola informasi akun Anda</p>
</div>

<div class="row">
    <div class="col-md-8">
        <?php if (isset($user['nursery_id']) && $user['nursery_id']): ?>
        <!-- Nursery Information -->
        <div class="card mb-4 border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-leaf"></i> Informasi Persemaian</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="text-muted mb-1">Nama Persemaian</label>
                            <h5 class="font-weight-bold"><?= htmlspecialchars($user['nursery_name'] ?? '-') ?></h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="text-muted mb-1">BPDAS Pembina</label>
                            <h5 class="font-weight-bold"><?= htmlspecialchars($user['bpdas_name'] ?? '-') ?></h5>
                        </div>
                    </div>
                </div>
                <?php if (isset($user['nursery_address'])): ?>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label class="text-muted mb-1">Alamat</label>
                            <p class="mb-0"><?= htmlspecialchars($user['nursery_address']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Profile Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-edit"></i> Informasi Profil</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('public/update-profile') ?>" method="POST">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                    
                    <div class="form-group">
                        <label class="form-label required">Username</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                        <small class="form-text text-muted">Username tidak dapat diubah</small>
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
                        <label class="form-label required">No. Telepon</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="<?= htmlspecialchars($user['phone']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required">NIK</label>
                        <input type="text" name="nik" class="form-control" 
                               value="<?= htmlspecialchars($user['nik']) ?>" 
                               pattern="\d{16}" maxlength="16" required>
                        <small class="form-text text-muted">16 digit angka</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" 
                               value="<?= ucfirst($user['role']) ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tanggal Registrasi</label>
                        <input type="text" class="form-control" 
                               value="<?= formatDate($user['created_at'], DATETIME_FORMAT) ?>" disabled>
                    </div>
                    
                    <hr>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Change Password -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-key"></i> Ubah Password</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('public/update-profile') ?>" method="POST" id="passwordForm">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Kosongkan jika tidak ingin mengubah password
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" 
                               minlength="<?= PASSWORD_MIN_LENGTH ?>" 
                               placeholder="Minimal <?= PASSWORD_MIN_LENGTH ?> karakter">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" 
                               placeholder="Ulangi password baru">
                        <small class="form-text text-danger" id="passwordError" style="display: none;">
                            Password tidak cocok
                        </small>
                    </div>
                    
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key"></i> Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Account Status -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-check-circle"></i> Status Akun</h5>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h5><?= htmlspecialchars($user['full_name']) ?></h5>
                    <p class="text-muted">@<?= htmlspecialchars($user['username']) ?></p>
                    
                    <span class="badge badge-success badge-lg">
                        <i class="fas fa-check"></i> Akun Aktif
                    </span>
                </div>
                
                <hr>
                
                <div class="text-left">
                    <p class="mb-2">
                        <i class="fas fa-envelope"></i> 
                        <?= htmlspecialchars($user['email']) ?>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-phone"></i> 
                        <?= htmlspecialchars($user['phone']) ?>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-id-card"></i> 
                        <?= htmlspecialchars($user['nik']) ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-link"></i> Tautan Cepat</h5>
            </div>
            <div class="card-body">
                <a href="<?= url('public/dashboard') ?>" class="btn btn-outline-primary btn-block">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="<?= url('public/my-requests') ?>" class="btn btn-outline-info btn-block">
                    <i class="fas fa-list"></i> Permintaan Saya
                </a>
                <a href="<?= url('public/request-form') ?>" class="btn btn-outline-success btn-block">
                    <i class="fas fa-plus"></i> Ajukan Permintaan
                </a>
                <a href="<?= url('search') ?>" class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-search"></i> Cari Stok Bibit
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.getElementById('passwordForm');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordError = document.getElementById('passwordError');
    
    // Validate password match
    function validatePassword() {
        if (newPassword.value && confirmPassword.value) {
            if (newPassword.value !== confirmPassword.value) {
                passwordError.style.display = 'block';
                confirmPassword.setCustomValidity('Passwords do not match');
                return false;
            } else {
                passwordError.style.display = 'none';
                confirmPassword.setCustomValidity('');
                return true;
            }
        }
        return true;
    }
    
    newPassword.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);
    
    passwordForm.addEventListener('submit', function(e) {
        if (!validatePassword()) {
            e.preventDefault();
        }
    });
});
</script>

<style>
.badge-lg {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}
</style>
