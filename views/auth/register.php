<?php
/**
 * Registration Page
 */
?>

<h2 class="text-center">Registrasi</h2>
<p class="text-center text-light">Buat akun baru untuk mengajukan permintaan bibit</p>

<form action="<?= url('auth/process-register') ?>" method="POST">
    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
    
    <div class="form-group">
        <label class="form-label">Nama Lengkap *</label>
        <input type="text" name="full_name" class="form-control" required>
    </div>

    <div class="form-group">
        <label class="form-label">NIK (16 digit) *</label>
        <input type="text" name="nik" class="form-control" pattern="\d{16}" maxlength="16" required>
        <small class="form-text">Masukkan 16 digit NIK sesuai KTP</small>
    </div>

    <div class="form-group">
        <label class="form-label">Email *</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="form-group">
        <label class="form-label">No. Telepon *</label>
        <input type="tel" name="phone" class="form-control" required>
        <small class="form-text">Contoh: 081234567890</small>
    </div>

    <div class="form-group">
        <label class="form-label">Username *</label>
        <input type="text" name="username" class="form-control" required>
        <small class="form-text">Username untuk login</small>
    </div>

    <div class="form-group">
        <label class="form-label">Password *</label>
        <input type="password" name="password" class="form-control" minlength="6" required>
        <small class="form-text">Minimal 6 karakter</small>
    </div>

    <div class="form-group">
        <label class="form-label">Konfirmasi Password *</label>
        <input type="password" name="password_confirm" class="form-control" minlength="6" required>
    </div>

    <div class="form-group">
        <label style="display: flex; align-items: flex-start;">
            <input type="checkbox" required style="margin-right: 0.5rem; margin-top: 0.25rem;">
            <span style="font-size: 0.875rem;">
                Saya menyetujui bahwa bibit yang diterima hanya untuk tujuan penghijauan/reboisasi 
                dan tidak untuk diperjualbelikan
            </span>
        </label>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%;">
        <i class="fas fa-user-plus"></i> Daftar
    </button>
</form>

<div class="text-center mt-3">
    <p>Sudah punya akun? <a href="<?= url('auth/login') ?>">Login di sini</a></p>
</div>
