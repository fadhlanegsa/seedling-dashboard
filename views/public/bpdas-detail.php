<?php
/**
 * BPDAS Detail Page
 * Display detailed information about a BPDAS and its stock
 */
?>

<div class="container" style="padding: 3rem 0;">
    <!-- BPDAS Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="card-title"><?= htmlspecialchars($bpdas['name']) ?></h2>
            <span class="badge badge-primary"><?= htmlspecialchars($bpdas['province_name']) ?></span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <h4>Informasi Kontak</h4>
                    <p><i class="fas fa-map-marker-alt"></i> <strong>Alamat:</strong><br><?= nl2br(htmlspecialchars($bpdas['address'])) ?></p>
                    <p><i class="fas fa-phone"></i> <strong>Telepon:</strong> <?= htmlspecialchars($bpdas['phone'] ?? '-') ?></p>
                    <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?= htmlspecialchars($bpdas['email'] ?? '-') ?></p>
                    <p><i class="fas fa-user"></i> <strong>Contact Person:</strong> <?= htmlspecialchars($bpdas['contact_person'] ?? '-') ?></p>
                </div>
                <div class="col-6">
                    <h4>Statistik Stok</h4>
                    <div class="row">
                        <div class="col-6">
                            <div class="stats-card">
                                <span class="stats-number"><?= $bpdas['seedling_types_count'] ?? 0 ?></span>
                                <span class="stats-label">Jenis Bibit Tersedia</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card success">
                                <span class="stats-number"><?= formatNumber($bpdas['total_stock'] ?? 0) ?></span>
                                <span class="stats-label">Total Stok</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Stok Bibit</h3>
        </div>
        <div class="card-body">
            <?php if (empty($stock)): ?>
                <p class="text-center">Belum ada data stok tersedia.</p>
            <?php else: ?>
                <table class="table" id="stockTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Bibit</th>
                            <th>Nama Ilmiah</th>
                            <th>Kategori</th>
                            <th>Stok (batang)</th>
                            <th>Update Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stock as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= htmlspecialchars($item['seedling_name']) ?></strong></td>
                                <td><em><?= htmlspecialchars($item['scientific_name'] ?? '-') ?></em></td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($item['category']) ?></span></td>
                                <td><strong><?= formatNumber($item['quantity']) ?></strong></td>
                                <td><?= formatDate($item['last_update_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <?php if (isLoggedIn() && hasRole('public')): ?>
                <a href="<?= url('public/request-form') ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane"></i> Ajukan Permintaan Bibit
                </a>
            <?php else: ?>
                <a href="<?= url('auth/login') ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Login untuk Mengajukan Permintaan
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#stockTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 10,
        order: [[1, 'asc']]
    });
});
</script>
