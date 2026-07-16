<?php
/**
 * Admin — Rekapitulasi PDB (Biaya Produksi Bibit) Pelaku Usaha
 * Filter per pelaku usaha & per tahun, ringkasan master, detail flat, export Excel.
 */
$qs = http_build_query(array_filter([
    'user_id' => $filterUserId ?? null,
    'year'    => $filterYear ?? null,
]));
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <h1 class="mb-0"><i class="fas fa-file-invoice-dollar text-primary"></i> Rekap PDB — Biaya Produksi Bibit</h1>
    <a href="<?= url('admin/pdb-export' . ($qs ? '?' . $qs : '')) ?>" class="btn btn-success">
        <i class="fas fa-file-excel"></i> Export Excel
    </a>
</div>

<?php if ($flash = $this->getFlash()): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
        <?= $flash['message'] ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form action="<?= url('admin/pdb-recap') ?>" method="GET" class="form-row align-items-end">
            <div class="col-md-5 mb-2">
                <label class="font-weight-bold small">Pelaku Usaha</label>
                <select name="user_id" class="form-control">
                    <option value="">— Semua Pelaku Usaha —</option>
                    <?php foreach ($pelakuUsahaList as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($filterUserId == $p['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['full_name']) ?> (<?= htmlspecialchars($p['username']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label class="font-weight-bold small">Tahun</label>
                <select name="year" class="form-control">
                    <option value="">— Semua Tahun —</option>
                    <?php foreach ($years as $y): ?>
                        <option value="<?= $y['periode_tahun'] ?>" <?= ($filterYear == $y['periode_tahun']) ? 'selected' : '' ?>>
                            <?= $y['periode_tahun'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Terapkan</button>
                <a href="<?= url('admin/pdb-recap') ?>" class="btn btn-outline-secondary"><i class="fas fa-times"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Ringkasan Master per Pelaku Usaha -->
<div class="card mb-4">
    <div class="card-header bg-white"><h6 class="m-0 font-weight-bold text-primary text-uppercase">Ringkasan Biaya Operasional</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="bg-light small">
                    <tr>
                        <th>Pelaku Usaha</th>
                        <th class="text-center">Tahun</th>
                        <th class="text-right">Total Biaya A</th>
                        <th class="text-right">Total Biaya B</th>
                        <th class="text-right">Target Produksi</th>
                        <th class="text-right">C12 / Batang</th>
                        <th class="text-center">Jml Jenis Bibit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recap)): ?>
                        <?php foreach ($recap as $m): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($m['full_name']) ?></strong><br><small class="text-muted"><?= htmlspecialchars($m['username']) ?></small></td>
                                <td class="text-center"><?= $m['periode_tahun'] ?></td>
                                <td class="text-right">Rp <?= formatNumber($m['total_biaya_a']) ?></td>
                                <td class="text-right">Rp <?= formatNumber($m['total_biaya_b']) ?></td>
                                <td class="text-right"><?= formatNumber($m['target_produksi_total']) ?></td>
                                <td class="text-right font-weight-bold text-success">Rp <?= formatNumber(round($m['biaya_produksi_per_batang_c12'])) ?></td>
                                <td class="text-center"><span class="badge badge-info"><?= $m['jml_bibit'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data untuk filter ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Bibit (flat report) -->
<div class="card mb-4">
    <div class="card-header bg-white"><h6 class="m-0 font-weight-bold text-primary text-uppercase">Detail Harga per Jenis Bibit</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="pdbDetailTable" class="table table-sm table-bordered table-hover mb-0">
                <thead class="bg-light small">
                    <tr>
                        <th>Pelaku Usaha</th>
                        <th class="text-center">Tahun</th>
                        <th>Jenis Bibit</th>
                        <th class="text-right">Harga Benih</th>
                        <th class="text-right">Berat 1000 (gr)</th>
                        <th class="text-center">Daya Kecambah</th>
                        <th class="text-center">Bibit Jadi</th>
                        <th class="text-right">Jml Benih/Kg</th>
                        <th class="text-right">Jml Jadi</th>
                        <th class="text-right">Harga Benih/Butir</th>
                        <th class="text-right">Harga Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($details)): ?>
                        <?php foreach ($details as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['full_name']) ?></td>
                                <td class="text-center"><?= $d['periode_tahun'] ?></td>
                                <td><strong><?= htmlspecialchars($d['seedling_name']) ?></strong></td>
                                <td class="text-right">Rp <?= formatNumber($d['harga_benih']) ?></td>
                                <td class="text-right"><?= formatNumber($d['berat_1000_butir']) ?></td>
                                <td class="text-center"><?= rtrim(rtrim(number_format($d['daya_kecambah'] * 100, 2, ',', '.'), '0'), ',') ?>%</td>
                                <td class="text-center"><?= rtrim(rtrim(number_format($d['bibit_jadi'] * 100, 2, ',', '.'), '0'), ',') ?>%</td>
                                <td class="text-right"><?= formatNumber($d['jml_benih_per_kg']) ?></td>
                                <td class="text-right"><?= formatNumber($d['jml_bibit_jadi']) ?></td>
                                <td class="text-right">Rp <?= formatNumber($d['harga_benih_per_butir']) ?></td>
                                <td class="text-right font-weight-bold text-success">Rp <?= formatNumber($d['harga_bibit_per_batang_final']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="11" class="text-center text-muted py-4">Belum ada detail bibit untuk filter ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
