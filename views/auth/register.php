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
        <label class="form-label">Jenis Pemohon *</label>
        <select name="user_type" id="user_type" class="form-control" required>
            <option value="perorangan">Perorangan (Individu)</option>
            <option value="kelompok">Kelompok Masyarakat / Lembaga</option>
        </select>
        <small class="form-text text-muted">Batas kuota maksimal berbeda untuk tiap jenis pemohon.</small>
    </div>

    <div class="form-group">
        <label class="form-label" id="label_full_name">Nama Lengkap *</label>
        <input type="text" name="full_name" id="full_name" class="form-control" required>
    </div>

    <div class="form-group">
        <label class="form-label" id="label_nik">NIK (16 digit) *</label>
        <input type="text" name="nik" id="nik" class="form-control" pattern="\d{16}" maxlength="16" required>
        <small class="form-text" id="help_nik">Masukkan 16 digit NIK sesuai KTP</small>
    </div>

    <div class="form-group">
        <label class="form-label">Email (Opsional)</label>
        <input type="email" name="email" class="form-control">
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

<script nonce="<?= cspNonce() ?>">
document.addEventListener('DOMContentLoaded', function() {
    const userTypeSelect = document.getElementById('user_type');
    const labelFullName = document.getElementById('label_full_name');
    const labelNik = document.getElementById('label_nik');
    const helpNik = document.getElementById('help_nik');
    const fullNameInput = document.getElementById('full_name');

    function updateUserTypeLabels() {
        if (userTypeSelect.value === 'kelompok') {
            labelFullName.textContent = 'Nama Kelompok / Lembaga *';
            labelNik.textContent = 'NIK Ketua Kelompok *';
            helpNik.textContent = 'Masukkan 16 digit NIK Ketua Kelompok sesuai KTP';
            fullNameInput.placeholder = 'Contoh: Kelompok Tani Sukamaju';
        } else {
            labelFullName.textContent = 'Nama Lengkap *';
            labelNik.textContent = 'NIK (16 digit) *';
            helpNik.textContent = 'Masukkan 16 digit NIK sesuai KTP';
            fullNameInput.placeholder = '';
        }
    }

    userTypeSelect.addEventListener('change', updateUserTypeLabels);
    
    // Initialize labels based on current selection
    updateUserTypeLabels();
});
</script>
