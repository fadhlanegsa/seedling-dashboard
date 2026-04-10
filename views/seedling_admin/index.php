<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-microscope text-primary mr-2"></i> Penatausahaan Bibit</h2>
        <?php if (!empty($filters['nursery_id']) || !empty($filters['bpdas_id'])): ?>
            <div class="small">
                <span class="badge badge-pill badge-warning px-3 py-2">
                    <i class="fas fa-filter mr-1"></i> Data Terfilter
                </span>
            </div>
        <?php endif; ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 border-0 bg-transparent">
                <li class="breadcrumb-item"><a href="<?= url('') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penatausahaan Bibit</li>
            </ol>
        </nav>
    </div>

    <?php if ($user['role'] !== 'operator_persemaian'): ?>
        <!-- Filter Card -->
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body bg-light rounded py-3">
                <form action="<?= url('seedling-admin') ?>" method="GET" id="filterForm">
                    <div class="row align-items-end">
                        <?php if ($user['role'] === 'admin'): ?>
                        <div class="col-md-4">
                            <label class="small font-weight-bold text-muted text-uppercase mb-1">BPDAS</label>
                            <select name="filter_bpdas" id="filter_bpdas" class="form-control form-control-sm shadow-none">
                                <option value="">-- Semua BPDAS --</option>
                                <?php foreach ($bpdasList as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= ($filters['bpdas_id'] == $b['id']) ? 'selected' : '' ?>>
                                        <?= $b['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-md-4">
                            <label class="small font-weight-bold text-muted text-uppercase mb-1">Persemaian</label>
                            <select name="filter_nursery" id="filter_nursery" class="form-control form-control-sm shadow-none">
                                <option value="">-- Semua Persemaian --</option>
                                <?php foreach ($nurseryList as $n): ?>
                                    <option value="<?= $n['id'] ?>" <?= ($filters['nursery_id'] == $n['id']) ? 'selected' : '' ?>>
                                        <?= $n['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 d-flex mt-md-0 mt-3">
                            <button type="submit" class="btn btn-sm btn-primary px-4 mr-2 shadow-sm font-weight-bold">
                                <i class="fas fa-search mr-1"></i> FILTER
                            </button>
                            <a href="<?= url('seedling-admin') ?>" class="btn btn-sm btn-outline-secondary px-4 shadow-sm font-weight-bold">
                                <i class="fas fa-undo mr-1"></i> RESET
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Summary Level & Navigation -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Status Stok Bahan Baku</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($stockBalance) ?> Item</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body d-flex align-items-center justify-content-around">
                    <?php if ($user['role'] === 'operator_persemaian'): ?>
                        <div class="text-center px-2">
                            <a href="<?= url('seedling-admin/bahan-baku-form') ?>" class="btn btn-primary shadow-sm mb-2">
                                <i class="fas fa-plus mr-2"></i> Bahan Baku IN
                            </a>
                            <p class="small text-muted mb-0">Input pengadaan bahan</p>
                        </div>
                        <div class="border-left h-75"></div>
                        <div class="text-center px-2">
                            <a href="<?= url('seedling-admin/media-mixing-form') ?>" class="btn btn-success shadow-sm mb-2">
                                <i class="fas fa-blender mr-2"></i> Pencampuran Media
                            </a>
                            <p class="small text-muted mb-0">Input hasil campuran (MT)</p>
                        </div>
                        <div class="border-left h-75"></div>
                        <div class="text-center px-2">
                            <a href="<?= url('seedling-admin/seed-sowing-form') ?>" class="btn btn-warning text-dark shadow-sm mb-2 font-weight-bold">
                                <i class="fas fa-seedling mr-2"></i> Penaburan Benih
                            </a>
                            <p class="small text-muted mb-0">Input tanam benih (PC)</p>
                        </div>
                        <div class="border-left h-75"></div>
                        <div class="text-center px-2">
                            <a href="<?= url('seedling-admin/harvesting-form') ?>" class="btn btn-info shadow-sm mb-2 font-weight-bold">
                                <i class="fas fa-leaf mr-2"></i> Pemanenan Semai
                            </a>
                            <p class="small text-muted mb-0">Input panen anakan (PA)</p>
                        </div>
                        <div class="border-left h-75"></div>
                        <div class="text-center px-2">
                            <a href="<?= url('seedling-admin/weaning-form') ?>" class="btn shadow-sm mb-2 font-weight-bold" style="background-color: #4CAF50; color: white;">
                                <i class="fas fa-seedling mr-2"></i> Penyapihan Bibit
                            </a>
                            <p class="small text-muted mb-0">Input pindah tanam (PE)</p>
                        </div>
                        <div class="border-left h-75 d-none d-lg-block"></div>
                        <div class="text-center px-2 d-none d-lg-block">
                            <a href="<?= url('seedling-admin/mutation-form') ?>" class="btn shadow-sm mb-2 font-weight-bold" style="background-color: #f44336; color: white;">
                                <i class="fas fa-exchange-alt mr-2"></i> Mutasi Keluar
                            </a>
                            <p class="small text-muted mb-0">Log Mati/Naik Kelas (BO)</p>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-info-circle text-info fa-2x mb-3"></i>
                            <h6 class="font-weight-bold mb-0">Mode Monitoring Aktif</h6>
                            <p class="small text-muted mb-0">Hanya Operator Persemaian yang memiliki akses fitur input data fisik.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Ready Stock (Bibit Jadi) Table -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow border-left-primary">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-warehouse mr-2"></i> Stok Bibit Jadi (Siap Salur)</h6>
                    <span class="badge badge-light border small text-muted">Data stok yang tersedia untuk masyarakat</span>
                </div>
                <div class="card-body p-0">
                    <div class="row no-gutters">
                        <?php if (empty($readyStock)): ?>
                            <div class="col-12 text-center py-4 text-muted small">Belum ada stok bibit jadi di persemaian ini.</div>
                        <?php else: ?>
                            <?php foreach ($readyStock as $rs): ?>
                                <div class="col-md-3 p-3 border-right border-bottom">
                                    <div class="d-flex align-items-top justify-content-between">
                                        <div>
                                            <div class="h6 font-weight-bold mb-1">
                                                <?= $rs['seedling_name'] ?>
                                                <?php if (($rs['source_type'] ?? '') === 'automated_pub'): ?>
                                                    <span class="badge badge-primary ml-1 shadow-sm" style="font-size: 0.6rem; vertical-align: middle;">PUB</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-xs text-uppercase mb-2">
                                                <?php if ($rs['program_type'] === 'bibitgratis'): ?>
                                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-certificate mr-1"></i> Gratis (PUB)</span>
                                                <?php else: ?>
                                                    <span class="text-muted"><?= $rs['program_type'] ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($rs['quantity'], 0) ?> <small class="text-muted" style="font-size: 0.6rem;">btg</small></div>
                                        </div>
                                        <div class="text-primary opacity-25">
                                            <i class="fas fa-box fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Stock Balance Table -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-box-open mr-2 text-primary"></i> Saldo Stok Bahan Baku</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead class="bg-light small text-dark font-weight-bold">
                                <tr>
                                    <th>Bahan Baku</th>
                                    <th class="text-right" title="Total Masuk (Pengadaan)">Masuk</th>
                                    <th class="text-right" title="Total Keluar (Dipakai)">Dipakai</th>
                                    <th class="text-right" title="Sisa Stok Tersedia Saat Ini">Sisa</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if (empty($stockBalance)): ?>
                                    <tr><td colspan="4" class="text-center text-muted">Belum ada stok.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($stockBalance as $stock): ?>
                                        <tr>
                                            <td>
                                                <?= $stock['name'] ?> <small class="text-muted">(<?= $stock['unit'] ?>)</small><br>
                                                <span class="badge badge-light border text-muted" style="font-size: 0.65rem; padding: 2px 4px;"><?= $stock['category'] ?></span>
                                            </td>
                                            <td class="text-right text-muted"><?= number_format($stock['total_in'], 2) ?></td>
                                            <td class="text-right text-danger"><?= number_format($stock['total_out'], 2) ?></td>
                                            <td class="text-right font-weight-bold <?= $stock['current_stock'] <= 0 ? 'text-danger' : 'text-success' ?>">
                                                <?= number_format($stock['current_stock'], 2) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions & Productions -->
        <div class="col-lg-7">
            <!-- Mixing Productions -->
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-history mr-2"></i> Produksi Campuran Terbaru (MT)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light small text-dark font-weight-bold">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kode Produksi</th>
                                    <th class="text-right">Hasil (m3)</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="small text-dark">
                                <?php if (empty($recentProductions)): ?>
                                    <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada catatan produksi.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentProductions as $prod): ?>
                                        <tr>
                                            <td><?= formatDate($prod['production_date']) ?></td>
                                            <td><span class="badge badge-success"><?= $prod['production_code'] ?></span></td>
                                            <td class="text-right font-weight-bold"><?= number_format($prod['total_production'], 2) ?></td>
                                            <td><?= $prod['notes'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bag Fillings -->
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-fill-drip mr-2"></i> Pengisian Kantong Terbaru (PB)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light small text-dark font-weight-bold">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kode</th>
                                    <th>Kantong</th>
                                    <th class="text-right">Awal</th>
                                    <th class="text-right">Stok</th>
                                </tr>
                            </thead>
                            <tbody class="small text-dark">
                                <?php if (empty($recentFillings)): ?>
                                    <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada pengisian kantong.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentFillings as $pb): ?>
                                        <tr>
                                            <td><?= formatDate($pb['filling_date']) ?></td>
                                            <td><span class="badge badge-info"><?= $pb['filling_code'] ?></span></td>
                                            <td><?= $pb['bag_name'] ?></td>
                                            <td class="text-right text-muted small"><?= number_format($pb['total_production'], 0) ?></td>
                                            <td class="text-right font-weight-bold text-primary"><?= number_format($pb['remaining_stock'], 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Seed Sowings (PC-) -->
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-seedling mr-2"></i> Penaburan Benih Terbaru (PC)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light small text-dark font-weight-bold">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kode (PC)</th>
                                    <th>Jenis Benih</th>
                                    <th class="text-right">Jml Benih</th>
                                    <th class="text-right">Polybags Jd</th>
                                </tr>
                            </thead>
                            <tbody class="small text-dark">
                                <?php if (empty($recentSowings)): ?>
                                    <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada penaburan benih.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentSowings as $pc): ?>
                                        <tr>
                                            <td><?= formatDate($pc['sowing_date']) ?></td>
                                            <td><span class="badge badge-warning text-dark"><?= $pc['sowing_code'] ?></span></td>
                                            <td><?= $pc['seed_name'] ?></td>
                                            <td class="text-right font-weight-bold"><?= number_format($pc['seed_quantity'], 2) ?> <?= $pc['seed_unit'] ?></td>
                                            <td class="text-right font-weight-bold text-primary"><?= number_format($pc['total_polybags'], 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Seedling Harvests (PA-) -->
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-leaf mr-2"></i> Stok Anakan Semai Tersedia (PA)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light small text-dark font-weight-bold">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kode (PA)</th>
                                    <th>Ref (PC)</th>
                                    <th>Jenis Benih</th>
                                    <th class="text-right">Sisa (Btg)</th>
                                </tr>
                            </thead>
                            <tbody class="small text-dark">
                                <?php if (empty($recentHarvests)): ?>
                                    <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada pemanenan semai.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentHarvests as $pa): ?>
                                        <tr>
                                            <td><?= formatDate($pa['harvest_date']) ?></td>
                                            <td><span class="badge badge-primary"><?= $pa['harvest_code'] ?></span></td>
                                            <td><small class="text-muted"><?= $pa['sowing_code'] ?></small></td>
                                            <td><?= $pa['seed_name'] ?></td>
                                            <td class="text-right font-weight-bold text-success"><?= number_format($pa['remaining_stock'], 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Penyapihan Bibit (PE) -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-left-success">
                    <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-seedling mr-2"></i> Penyapihan Bibit Terbaru (PE)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light small text-dark font-weight-bold">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kode (PE)</th>
                                        <th>Asal (PA)</th>
                                        <th>Bibit Jadi</th>
                                        <th class="text-right">Sisa (Btg)</th>
                                    </tr>
                                </thead>
                                <tbody class="small text-dark">
                                    <?php if (empty($recentWeanings)): ?>
                                        <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada penyapihan bibit.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recentWeanings as $pe): ?>
                                            <tr>
                                                <td><?= formatDate($pe['weaning_date']) ?></td>
                                                <td><span class="badge badge-success"><?= $pe['weaning_code'] ?></span></td>
                                                <td><small class="text-muted"><?= $pe['harvest_code'] ?></small></td>
                                                <td><?= $pe['result_name'] ?></td>
                                                <td class="text-right font-weight-bold text-success"><?= number_format($pe['remaining_stock'], 0) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Penggunaan Entres (ET) -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-left-warning">
                    <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-cut mr-2"></i> Penggunaan Entres Terbaru (ET)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light small text-dark font-weight-bold">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kode (ET)</th>
                                    <th>Asal PA</th>
                                    <th>Hasil</th>
                                    <th class="text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="small text-dark">
                                <?php if (empty($recentEntres)): ?>
                                    <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada penggunaan entres.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentEntres as $et): ?>
                                        <tr>
                                            <td><?= formatDate($et['entres_date']) ?></td>
                                            <td><span class="badge badge-warning text-dark"><?= $et['entres_code'] ?></span></td>
                                            <td><small class="text-muted"><?= $et['weaning_code'] ?></small></td>
                                            <td><?= $et['result_name'] ?></td>
                                            <td class="text-right font-weight-bold text-warning"><?= number_format($et['used_quantity'], 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mutasi Bibit Terbaru (BO) -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-left-danger">
                    <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-exchange-alt mr-2"></i> Mutasi Bibit Terbaru (BO)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light small text-dark font-weight-bold">
                                <tr>
                                    <th>Kode</th>
                                    <th>Asal</th>
                                    <th>Tgl</th>
                                    <th>Tipe</th>
                                    <th class="text-right">Jml</th>
                                    <th>Tujuan</th>
                                </tr>
                            </thead>
                            <tbody class="small text-dark">
                                <?php if (empty($recentMutations)): ?>
                                    <tr><td colspan="6" class="text-center py-3 text-muted">Belum ada catatan mutasi.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentMutations as $bo): ?>
                                        <tr>
                                            <td class="font-weight-bold"><?= $bo['mutation_code'] ?></td>
                                            <td><small class="badge badge-light border"><?= $bo['source_code'] ?></small><br><?= $bo['seedling_name'] ?></td>
                                            <td><?= formatDate($bo['mutation_date']) ?></td>
                                            <td>
                                                <?php if($bo['mutation_type'] === 'MATI'): ?>
                                                    <span class="text-danger font-weight-bold">MATI</span>
                                                <?php elseif($bo['mutation_type'] === 'NAIK KELAS'): ?>
                                                    <span class="text-success font-weight-bold">LULUS</span>
                                                <?php else: ?>
                                                    <span class="text-info font-weight-bold">PINDAH</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right font-weight-bold"><?= number_format($bo['quantity'], 0) ?></td>
                                            <td><small class="text-muted"><?= $bo['target_location'] ?: '-' ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bahan Baku IN -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-receipt mr-2"></i> Transaksi Masuk (Bahan Baku IN)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light small text-dark font-weight-bold">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Item</th>
                                    <th class="text-right">Jumlah</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody class="small text-dark">
                                <?php if (empty($recentTransactions)): ?>
                                    <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada transaksi masuk.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentTransactions as $row): ?>
                                        <tr>
                                            <td><?= formatDate($row['transaction_date']) ?></td>
                                            <td><?= $row['item_name'] ?></td>
                                            <td class="text-right"><?= number_format($row['quantity'], 2) ?></td>
                                            <td><?= $row['item_unit'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBpdas = document.getElementById('filter_bpdas');
    const filterNursery = document.getElementById('filter_nursery');

    if (filterBpdas) {
        filterBpdas.addEventListener('change', function() {
            const bpdasId = this.value;
            
            // Clear current nurseries
            filterNursery.innerHTML = '<option value="">-- Semua Persemaian --</option>';
            
            if (bpdasId) {
                // Fetch nurseries for this BPDAS
                fetch(`<?= url('seedling-admin/get-nurseries-by-bpdas') ?>?bpdas_id=${bpdasId}`)
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            res.data.forEach(n => {
                                const opt = document.createElement('option');
                                opt.value = n.id;
                                opt.textContent = n.name;
                                filterNursery.appendChild(opt);
                            });
                        }
                    });
            }
        });
    }
});
</script>

<style>
    .border-left-primary {
        border-left: 0.25rem solid var(--primary-color) !important;
    }
    .text-gray-800 {
        color: #5a5c69 !important;
    }
    .text-gray-300 {
        color: #dddfeb !important;
    }
</style>
