<?php
/**
 * Admin: Seed Source Detail View
 */
?>
<div class="page-header">
    <h1><i class="fas fa-tree"></i> Detail Sumber Benih</h1>
    <div>
        <a href="<?= url('admin/seed-sources/edit/' . $seedSource['id']) ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="<?= url('admin/seed-sources') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Informasi Dasar -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Informasi Dasar</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Nama Sumber Benih</th>
                        <td><?= htmlspecialchars($seedSource['seed_source_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Nama Lokal</th>
                        <td><?= htmlspecialchars($seedSource['local_name'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Nama Botani</th>
                        <td><?= htmlspecialchars($seedSource['botanical_name'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Bibit</th>
                        <td><?= htmlspecialchars($seedSource['seedling_type_name'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Provinsi</th>
                        <td><?= htmlspecialchars($seedSource['province_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Kelas SB</th>
                        <td><?= htmlspecialchars($seedSource['seed_class'] ?? '-') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Lokasi & Area -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Lokasi & Area</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Lokasi</th>
                        <td><?= htmlspecialchars($seedSource['location'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Luas (Ha)</th>
                        <td><?= $seedSource['area_hectares'] ? number_format($seedSource['area_hectares'], 3) : '-' ?></td>
                    </tr>
                    <tr>
                        <th>Latitude</th>
                        <td><?= htmlspecialchars($seedSource['latitude'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Longitude</th>
                        <td><?= htmlspecialchars($seedSource['longitude'] ?? '-') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <!-- Informasi Pemilik -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Informasi Pemilik</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Nama Pemilik</th>
                        <td><?= htmlspecialchars($seedSource['owner_name'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Telepon</th>
                        <td><?= htmlspecialchars($seedSource['owner_phone'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Kepemilikan</th>
                        <td><?= htmlspecialchars($seedSource['ownership_type'] ?? '-') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Sertifikasi -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Sertifikasi</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Nomor Sertifikat</th>
                        <td><?= htmlspecialchars($seedSource['certificate_number'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Sertifikat</th>
                        <td><?= formatDate($seedSource['certificate_date'] ?? null) ?></td>
                    </tr>
                    <tr>
                        <th>Masa Berlaku</th>
                        <td>
                            <?= formatDate($seedSource['certificate_validity'] ?? null) ?>
                            <?php if (!empty($seedSource['certificate_validity'])): ?>
                                <?php if (strtotime($seedSource['certificate_validity']) >= time()): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Kadaluarsa</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <!-- Produksi -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>Informasi Produksi</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="20%">Jumlah Pohon</th>
                        <td><?= formatNumber($seedSource['tree_count'] ?? 0) ?> pohon</td>
                    </tr>
                    <tr>
                        <th>Estimasi Produksi</th>
                        <td><?= $seedSource['production_estimate_per_year'] ? number_format($seedSource['production_estimate_per_year'], 2) . ' Kg/tahun' : '-' ?></td>
                    </tr>
                    <tr>
                        <th>Estimasi Jumlah Benih</th>
                        <td><?= formatNumber($seedSource['seed_quantity_estimate'] ?? 0) ?> butir</td>
                    </tr>
                    <tr>
                        <th>Musim Pembungaan</th>
                        <td><?= htmlspecialchars($seedSource['flowering_season'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Musim Buah Masak</th>
                        <td><?= htmlspecialchars($seedSource['fruiting_season'] ?? '-') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <!-- Pemanfaatan -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>Pemanfaatan</h3>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($seedSource['utilization'] ?? '-')) ?></p>
            </div>
        </div>
    </div>
</div>
