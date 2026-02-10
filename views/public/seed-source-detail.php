<?php
/**
 * Public: Seed Source Detail Page
 */
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .detail-header { background: linear-gradient(135deg, #2c5530 0%, #4a7c4e 100%); color: white; padding: 2rem; border-radius: 8px; margin-bottom: 2rem; }
        .detail-card { background: white; padding: 2rem; margin-bottom: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .detail-card h3 { color: #2c5530; border-bottom: 2px solid #4a7c4e; padding-bottom: 0.5rem; margin-bottom: 1.5rem; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
        .info-row { display: flex; margin-bottom: 1rem; }
        .info-label { font-weight: bold; min-width: 180px; color: #555; }
        .info-value { flex: 1; color: #333; }
        .contact-box { background: #e8f5e9; border-left: 4px solid #4caf50; padding: 1.5rem; border-radius: 4px; margin: 1.5rem 0; }
        .contact-box h4 { color: #2e7d32; margin-bottom: 1rem; }
        .contact-phone { font-size: 1.5rem; color: #2e7d32; font-weight: bold; }
        .contact-phone a { color: #2e7d32; text-decoration: none; }
        .contact-phone a:hover { text-decoration: underline; }
        #detailMap { height: 350px; width: 100%; border-radius: 8px; }
        .badge-cert { background: #4caf50; color: white; padding: 0.5rem 1rem; border-radius: 20px; display: inline-block; margin-top: 0.5rem; }
        .production-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; }
        .stat-box { background: #f5f5f5; padding: 1rem; border-radius: 4px; text-align: center; }
        .stat-box .number { font-size: 2rem; font-weight: bold; color: #4a7c4e; }
        .stat-box .label { color: #666; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="container mt-4">
    <a href="<?= url('public/seed-source-directory') ?>" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Direktori
    </a>
    
    <div class="detail-header">
        <h1><i class="fas fa-tree"></i> <?= htmlspecialchars($seedSource['seed_source_name']) ?></h1>
        <?php if ($seedSource['local_name']): ?>
        <p class="lead mb-0"><em><?= htmlspecialchars($seedSource['local_name']) ?></em></p>
        <?php endif; ?>
        <?php if ($seedSource['botanical_name']): ?>
        <p class="mb-0"><i>Nama Botani: <?= htmlspecialchars($seedSource['botanical_name']) ?></i></p>
        <?php endif; ?>
    </div>
    
    <!-- Basic Information -->
    <div class="detail-card">
        <h3><i class="fas fa-info-circle"></i> Informasi Dasar</h3>
        <div class="info-grid">
            <div>
                <div class="info-row">
                    <span class="info-label">Jenis Pohon:</span>
                    <span class="info-value"><?= htmlspecialchars($seedSource['seedling_type_name'] ?? '-') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kelas SB:</span>
                    <span class="info-value"><?= htmlspecialchars($seedSource['seed_class'] ?? '-') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Luas Area:</span>
                    <span class="info-value"><?= $seedSource['area_hectares'] ? number_format($seedSource['area_hectares'], 2) . ' Ha' : '-' ?></span>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Provinsi:</span>
                    <span class="info-value"><?= htmlspecialchars($seedSource['province_name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Lokasi:</span>
                    <span class="info-value"><?= htmlspecialchars($seedSource['location'] ?? '-') ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Map -->
    <?php if ($seedSource['latitude'] && $seedSource['longitude']): ?>
    <div class="detail-card">
        <h3><i class="fas fa-map-marker-alt"></i> Lokasi</h3>
        <div id="detailMap"></div>
        <p class="mt-2 text-muted">
            <i class="fas fa-map-pin"></i> 
            Koordinat: <?= number_format($seedSource['latitude'], 6) ?>, <?= number_format($seedSource['longitude'], 6) ?>
        </p>
    </div>
    <?php endif; ?>
    
    <!-- Legality/Certification -->
    <?php if ($seedSource['certificate_number']): ?>
    <div class="detail-card">
        <h3><i class="fas fa-certificate"></i> Legalitas & Sertifikasi</h3>
        <div class="info-grid">
            <div>
                <div class="info-row">
                    <span class="info-label">Nomor Sertifikat:</span>
                    <span class="info-value"><strong><?= htmlspecialchars($seedSource['certificate_number']) ?></strong></span>
                </div>
                <?php if ($seedSource['certificate_date']): ?>
                <div class="info-row">
                    <span class="info-label">Tanggal Sertifikat:</span>
                    <span class="info-value"><?= date('d F Y', strtotime($seedSource['certificate_date'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
            <div>
                <?php if ($seedSource['certificate_validity']): ?>
                <div class="info-row">
                    <span class="info-label">Masa Berlaku:</span>
                    <span class="info-value"><?= date('d F Y', strtotime($seedSource['certificate_validity'])) ?></span>
                </div>
                <?php 
                $isValid = strtotime($seedSource['certificate_validity']) > time();
                if ($isValid): ?>
                <span class="badge-cert"><i class="fas fa-check-circle"></i> Sertifikat Aktif</span>
                <?php else: ?>
                <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Sertifikat Kadaluarsa</span>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Production -->
    <div class="detail-card">
        <h3><i class="fas fa-chart-line"></i> Informasi Produksi</h3>
        <div class="production-stats">
            <?php if ($seedSource['tree_count']): ?>
            <div class="stat-box">
                <div class="number"><?= number_format($seedSource['tree_count']) ?></div>
                <div class="label">Jumlah Pohon</div>
            </div>
            <?php endif; ?>
            
            <?php if ($seedSource['production_estimate_per_year']): ?>
            <div class="stat-box">
                <div class="number"><?= number_format($seedSource['production_estimate_per_year'], 2) ?> </div>
                <div class="label">Estimasi Produksi (Kg/tahun)</div>
            </div>
            <?php endif; ?>
            
            <?php if ($seedSource['seed_quantity_estimate']): ?>
            <div class="stat-box">
                <div class="number"><?= number_format($seedSource['seed_quantity_estimate']) ?></div>
                <div class="label">Estimasi Jumlah Benih (butir)</div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="info-grid mt-3">
            <?php if ($seedSource['flowering_season']): ?>
            <div class="info-row">
                <span class="info-label">Musim Pembungaan:</span>
                <span class="info-value"><?= htmlspecialchars($seedSource['flowering_season']) ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($seedSource['fruiting_season']): ?>
            <div class="info-row">
                <span class="info-label">Musim Buah Masak:</span>
                <span class="info-value"><?= htmlspecialchars($seedSource['fruiting_season']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Owner Contact -->
    <?php if ($seedSource['owner_name'] || $seedSource['owner_phone']): ?>
    <div class="detail-card">
        <h3><i class="fas fa-user"></i> Informasi Pemilik</h3>
        <div class="contact-box">
            <h4><i class="fas fa-handshake"></i> Hubungi Pemilik untuk Transaksi/Koordinasi</h4>
            <?php if ($seedSource['owner_name']): ?>
            <div class="info-row">
                <span class="info-label">Nama Pemilik:</span>
                <span class="info-value"><strong><?= htmlspecialchars($seedSource['owner_name']) ?></strong></span>
            </div>
            <?php endif; ?>
            
            <?php if ($seedSource['ownership_type']): ?>
            <div class="info-row">
                <span class="info-label">Jenis Kepemilikan:</span>
                <span class="info-value"><?= htmlspecialchars($seedSource['ownership_type']) ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($seedSource['owner_phone']): ?>
            <div class="info-row mt-3">
                <span class="info-label">Nomor Telepon:</span>
                <span class="info-value">
                    <div class="contact-phone">
                        <i class="fas fa-phone"></i> 
                        <a href="tel:<?= $seedSource['owner_phone'] ?>">
                            <?= htmlspecialchars($seedSource['owner_phone']) ?>
                        </a>
                    </div>
                    <small class="text-muted">Klik untuk menghubungi via telepon</small>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Utilization -->
    <?php if ($seedSource['utilization']): ?>
    <div class="detail-card">
        <h3><i class="fas fa-leaf"></i> Pemanfaatan</h3>
        <p><?= nl2br(htmlspecialchars($seedSource['utilization'])) ?></p>
    </div>
    <?php endif; ?>
</div>

<?php if ($seedSource['latitude'] && $seedSource['longitude']): ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const detailMap = L.map('detailMap').setView([<?= $seedSource['latitude'] ?>, <?= $seedSource['longitude'] ?>], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(detailMap);

L.marker([<?= $seedSource['latitude'] ?>, <?= $seedSource['longitude'] ?>])
    .addTo(detailMap)
    .bindPopup('<strong><?= htmlspecialchars($seedSource['seed_source_name']) ?></strong><br><?= htmlspecialchars($seedSource['location'] ?? '') ?>')
    .openPopup();
</script>
<?php endif; ?>

</body>
</html>
