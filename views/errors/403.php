<?php
/**
 * 403 Forbidden Error Page
 */
?>

<div class="error-code">403</div>
<h2 class="error-message">Akses Ditolak</h2>
<p style="color: var(--text-light); margin-bottom: 2rem;">
    Anda tidak memiliki izin untuk mengakses halaman ini.
</p>

<a href="<?= url('') ?>" class="btn btn-primary btn-lg">
    <i class="fas fa-home"></i> Kembali ke Beranda
</a>
