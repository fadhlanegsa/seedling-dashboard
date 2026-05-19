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
                        <h4 class="font-weight-bold text-dark"><?= htmlspecialchars($batchCode) ?> - <?= htmlspecialchars($seedName) ?></h4>
                        <span class="badge badge-success p-2 px-3 shadow-sm">
                            <i class="fas fa-check-circle mr-1"></i> Data Terverifikasi
                        </span>
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
