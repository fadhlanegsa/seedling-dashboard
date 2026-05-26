<style>
@keyframes pulse {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4); }
    70% { transform: scale(1.03); box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
}
.bg-white-10 { background-color: rgba(255, 255, 255, 0.1); }
.border-white-20 { border-color: rgba(255, 255, 255, 0.2); }
.border-white-10 { border-color: rgba(255, 255, 255, 0.1); }
</style>

<div class="container py-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-lg overflow-hidden">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0 font-weight-bold text-center">
                        <i class="fas fa-project-diagram mr-2"></i> Lacak Asal-Usul Bibit (Traceability)
                    </h5>
                </div>
                
                <div class="card-body p-4 bg-light">
                    <div class="text-center mb-4">
                        <?php if (isset($seedlingIndex) && $seedlingIndex !== null): ?>
                            <!-- Premium Seedling Visual Card -->
                            <div class="card border-0 shadow rounded-lg overflow-hidden mb-4" style="background: linear-gradient(135deg, #1e3c0d 0%, #2d5016 100%);">
                                <div class="card-body p-4 text-white position-relative text-left">
                                    <!-- Decorative forestry background element -->
                                    <div class="position-absolute" style="right: 10px; bottom: -20px; opacity: 0.08; font-size: 150px; transform: rotate(15deg); pointer-events: none;">
                                        <i class="fas fa-seedling text-white"></i>
                                    </div>
                                    
                                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                                        <div class="mb-3 mb-md-0">
                                            <span class="badge badge-warning text-dark font-weight-bold mb-2 p-2 px-3 text-uppercase shadow-sm" style="letter-spacing: 1px; animation: pulse 2s infinite; font-size: 0.75rem;">
                                                <i class="fas fa-certificate mr-1"></i> Bibit Individual Terverifikasi
                                            </span>
                                            <h2 class="font-weight-bold mb-1" style="font-family: 'Outfit', 'Inter', sans-serif;"><?= htmlspecialchars($seedName) ?></h2>
                                            <p class="lead mb-2 opacity-90" style="font-style: italic; font-size: 1.1rem;"><?= htmlspecialchars($traceData['sowing']['seed_scientific_name'] ?? 'Magnolia champaca') ?></p>
                                            
                                            <div class="mt-3 p-3 rounded shadow-inner" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(5px);">
                                                <div class="row">
                                                    <div class="col-6 border-right" style="border-color: rgba(255,255,255,0.15) !important;">
                                                        <small class="d-block opacity-75 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Nomor Seri Bibit</small>
                                                        <span class="h3 font-weight-bold text-warning mb-0">#<?= htmlspecialchars($seedlingIndex) ?></span>
                                                        <small class="opacity-75"> dari <?= htmlspecialchars($batchQuantity) ?></small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="d-block opacity-75 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Batch Induk</small>
                                                        <span class="h4 font-weight-bold mb-0" style="letter-spacing: 0.5px;"><?= htmlspecialchars($batchCode) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Simulated premium QR/Barcode element -->
                                        <div class="bg-white p-3 rounded shadow-sm text-center d-flex flex-column align-items-center" style="min-width: 160px; max-width: 170px;">
                                            <!-- Draw a realistic barcode simulation with CSS -->
                                            <div class="d-flex align-items-end mb-2 justify-content-center" style="height: 55px; width: 130px; background: #fff; padding: 2px;">
                                                <div style="width: 3px; height: 100%; background: #000; margin-right: 2px;"></div>
                                                <div style="width: 1px; height: 90%; background: #000; margin-right: 3px;"></div>
                                                <div style="width: 5px; height: 100%; background: #000; margin-right: 1px;"></div>
                                                <div style="width: 1px; height: 85%; background: #000; margin-right: 2px;"></div>
                                                <div style="width: 3px; height: 95%; background: #000; margin-right: 4px;"></div>
                                                <div style="width: 4px; height: 100%; background: #000; margin-right: 2px;"></div>
                                                <div style="width: 1px; height: 90%; background: #000; margin-right: 1px;"></div>
                                                <div style="width: 3px; height: 100%; background: #000; margin-right: 3px;"></div>
                                                <div style="width: 1px; height: 85%; background: #000; margin-right: 2px;"></div>
                                                <div style="width: 4px; height: 95%; background: #000; margin-right: 2px;"></div>
                                                <div style="width: 2px; height: 100%; background: #000;"></div>
                                            </div>
                                            <small class="text-dark font-weight-bold" style="font-family: monospace; font-size: 0.65rem; letter-spacing: 0.2px;"><?= htmlspecialchars($smartCode) ?></small>
                                            <span class="badge badge-success mt-2 py-1 px-2" style="font-size: 0.6rem; letter-spacing: 0.5px;"><i class="fas fa-shield-alt mr-1"></i> SECURE TAG</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Standard Batch Card -->
                            <h4 class="font-weight-bold text-dark"><?= htmlspecialchars($batchCode) ?> - <?= htmlspecialchars($seedName) ?></h4>
                            <span class="badge badge-success p-2 px-3 shadow-sm">
                                <i class="fas fa-check-circle mr-1"></i> Data Terverifikasi
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!$hasData): ?>
                        <div class="alert alert-warning text-center p-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3 text-warning"></i>
                            <h5 class="font-weight-bold">Data Tidak Ditemukan</h5>
                            <p class="mb-0">Maaf, riwayat traceability untuk batch ini tidak tersedia atau batch tidak valid.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6 mb-3">
                                <!-- 1. Sumber Benih -->
                                <?php if ($traceData['seed_source']): ?>
                                    <div class="card shadow-sm border-left-primary h-100 mb-3">
                                        <div class="card-body p-3">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><i class="fas fa-seedling mr-1"></i> Asal Benih / Genetik</div>
                                            <div class="h6 mb-1 font-weight-bold text-gray-800"><?= htmlspecialchars($traceData['seed_source']['name']) ?></div>
                                            <div class="small text-muted">
                                                Vendor: <?= htmlspecialchars($traceData['seed_source']['vendor'] ?: '-') ?><br>
                                                Lokasi: <?= htmlspecialchars($traceData['seed_source']['kabupaten'] ?: '-') ?><br>
                                                Sertifikat: <?= htmlspecialchars($traceData['seed_source']['sertifikat'] ?: '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="card shadow-sm border-left-secondary h-100 mb-3">
                                        <div class="card-body p-3">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1"><i class="fas fa-seedling mr-1"></i> Asal Benih / Genetik</div>
                                            <div class="small text-muted">Tidak ada data sumber benih yang dilacak untuk batch ini.</div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <!-- 2. Riwayat Penaburan -->
                                <?php if ($traceData['sowing']): ?>
                                    <div class="card shadow-sm border-left-warning h-100 mb-3">
                                        <div class="card-body p-3">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"><i class="fas fa-hand-holding-water mr-1"></i> Riwayat Penaburan (PC)</div>
                                            <div class="h6 mb-1 font-weight-bold text-gray-800"><?= htmlspecialchars($traceData['sowing']['code']) ?></div>
                                            <div class="small text-muted">
                                                Tanggal: <?= formatDate($traceData['sowing']['date']) ?><br>
                                                Jumlah Benih: <?= number_format($traceData['sowing']['seed_quantity'], 0, ',', '.') ?> <?= htmlspecialchars($traceData['sowing']['seed_unit']) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Kolom Bawah Kiri -->
                            <div class="col-md-6 mb-3">
                                <!-- 3. Riwayat Penyapihan -->
                                <?php if ($traceData['weaning']): ?>
                                    <div class="card shadow-sm border-left-success h-100 mb-3">
                                        <div class="card-body p-3">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><i class="fas fa-expand-arrows-alt mr-1"></i> Riwayat Sapih/Entres</div>
                                            <div class="h6 mb-1 font-weight-bold text-gray-800"><?= htmlspecialchars($traceData['weaning']['code']) ?></div>
                                            <div class="small text-muted">
                                                Tanggal: <?= formatDate($traceData['weaning']['date']) ?><br>
                                                Lokasi Awal: <?= htmlspecialchars($traceData['weaning']['location'] ?: '-') ?><br>
                                                Qty Sapih: <?= number_format($traceData['weaning']['quantity'], 0, ',', '.') ?> btg
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Kolom Bawah Kanan -->
                            <div class="col-md-6 mb-3">
                                <!-- 4. Komposisi Media -->
                                <?php if ($traceData['media'] && count($traceData['media']['items']) > 0): ?>
                                    <div class="card shadow-sm border-left-info h-100 mb-3">
                                        <div class="card-body p-3">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1"><i class="fas fa-mortar-pestle mr-1"></i> Media Tanam (MT)</div>
                                            <div class="h6 mb-1 font-weight-bold text-gray-800"><?= htmlspecialchars($traceData['media']['code']) ?></div>
                                            <div class="small text-muted mt-2">Komposisi:</div>
                                            <ul class="small text-muted pl-3 mb-0">
                                                <?php foreach ($traceData['media']['items'] as $m): ?>
                                                    <li><?= htmlspecialchars($m['name']) ?> &mdash; <?= number_format($m['quantity'], 2, ',', '.') ?> <?= htmlspecialchars($m['unit']) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php elseif ($sourceType === 'PE'): ?>
                                    <div class="card shadow-sm border-left-secondary h-100 mb-3">
                                        <div class="card-body p-3">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1"><i class="fas fa-mortar-pestle mr-1"></i> Media Tanam (MT)</div>
                                            <div class="small text-muted">Data komposisi media tidak ditemukan untuk batch ini.</div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4 border-top pt-4">
                        <img src="<?= asset('images/logo-kementerian.png') ?>" alt="Logo Kementerian" width="60" class="mb-2 opacity-50">
                        <p class="small text-muted mb-0">Diselenggarakan oleh<br><strong>Kementerian Kehutanan Republik Indonesia</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
