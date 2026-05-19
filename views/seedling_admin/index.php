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

    <!-- Summary & Production Navigation -->
    <div class="row mb-4">
        <!-- Stock Summary Small Card -->
        <div class="col-xl-3 col-md-4 mb-3">
            <div class="card stat-card border-0 shadow-sm h-100 overflow-hidden bg-white">
                <div class="card-body position-relative">
                    <div class="text-xs font-weight-bold text-primary text-uppercase letter-spacing-1 mb-1">Status Stok Bahan</div>
                    <div class="d-flex align-items-baseline">
                        <span class="h2 mb-0 font-weight-bold text-gray-900"><?= count($stockBalance) ?></span>
                        <span class="ml-2 text-muted small">Item Aktif</span>
                    </div>
                    <i class="fas fa-cubes stat-icon"></i>
                </div>
                <div class="progress progress-sm rounded-0">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Production Flow Actions -->
        <!-- Production Flow Actions -->
        <div class="col-xl-9 col-md-8 mb-3">
            <div class="card border-0 shadow-sm bg-white overflow-hidden">
                <div class="card-header bg-white py-2 border-bottom-0">
                    <div class="text-xs font-weight-bold text-muted text-uppercase">Menu Cepat Produksi (PUB)</div>
                </div>
                <div class="card-body p-2 bg-light d-flex flex-wrap justify-content-start" style="gap: 10px;">
                    <?php if ($user['role'] === 'operator_persemaian'): ?>
                        <a href="<?= url('seedling-admin/bahan-baku-form') ?>" class="action-btn-modern bg-primary text-white">
                            <i class="fas fa-plus-circle"></i>
                            <div class="content">
                                <span class="title">Bahan Baku</span>
                                <span class="code">PG-IN</span>
                            </div>
                        </a>
                        <a href="<?= url('seedling-admin/media-mixing-form') ?>" class="action-btn-modern bg-success text-white">
                            <i class="fas fa-mortar-pestle"></i>
                            <div class="content">
                                <span class="title">Mixing</span>
                                <span class="code">MT-MEDIA</span>
                            </div>
                        </a>
                        <a href="<?= url('seedling-admin/seed-sowing-form') ?>" class="action-btn-modern bg-warning text-dark">
                            <i class="fas fa-seedling"></i>
                            <div class="content">
                                <span class="title">Tanam</span>
                                <span class="code">PC-BENIH</span>
                            </div>
                        </a>
                        <a href="<?= url('seedling-admin/harvesting-form') ?>" class="action-btn-modern bg-info text-white">
                            <i class="fas fa-hand-holding-seedling"></i>
                            <div class="content">
                                <span class="title">Panen</span>
                                <span class="code">PA-SEMAI</span>
                            </div>
                        </a>
                        <a href="<?= url('seedling-admin/weaning-form') ?>" class="action-btn-modern bg-teal text-white">
                            <i class="fas fa-expand-arrows-alt"></i>
                            <div class="content">
                                <span class="title">Sapih</span>
                                <span class="code">PE/ET-BIBIT</span>
                            </div>
                        </a>
                        <a href="<?= url('seedling-admin/mutation-form') ?>" class="action-btn-modern bg-danger text-white">
                            <i class="fas fa-graduation-cap"></i>
                            <div class="content">
                                <span class="title">Naik Kelas</span>
                                <span class="code">BO-PUB</span>
                            </div>
                        </a>
                    <?php else: ?>
                        <?php if ($user['role'] === 'admin' || $user['role'] === 'bpdas'): ?>
                            <a href="<?= url('seedling-audit') ?>" class="action-btn-modern bg-dark text-white">
                                <i class="fas fa-history"></i>
                                <div class="content">
                                    <span class="title">Audit Trail</span>
                                    <span class="code">HISTORY</span>
                                </div>
                            </a>
                        <?php endif; ?>
                        <?php if ($user['role'] === 'bpdas'): ?>
                            <div class="py-2 px-4 d-flex align-items-center text-muted">
                                <i class="fas fa-eye mr-2"></i> Mode Monitoring BPDAS
                            </div>
                        <?php else: ?>
                            <div class="py-2 px-4 d-flex align-items-center text-muted">
                                <i class="fas fa-eye mr-2"></i> Mode Monitoring Aktif
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- TABBED CONTENT NAVIGATION -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded shadow-sm border" id="dashboardTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active font-weight-bold px-4" id="ready-stock-tab" data-toggle="pill" href="#ready-stock" role="tab">
                <i class="fas fa-store-alt mr-2"></i> Stok Bibit Jadi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold px-4" id="history-tab" data-toggle="pill" href="#history" role="tab">
                <i class="fas fa-history mr-2"></i> Log Produksi & Mutasi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold px-4" id="inventory-tab" data-toggle="pill" href="#inventory" role="tab">
                <i class="fas fa-archive mr-2"></i> Inventory & Bahan Baku
            </a>
        </li>
    </ul>

    <div class="tab-content" id="dashboardTabsContent">
        <!-- TAB 1: READY STOCK -->
        <div class="tab-pane fade show active" id="ready-stock" role="tabpanel">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 border-top-primary">
                        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <h5 class="m-0 font-weight-bold text-gray-800"><i class="fas fa-store-alt text-primary mr-2"></i> Stok Bibit Jadi (Siap Salur)</h5>
                            </div>
                            
                            <div class="d-flex align-items-center mt-2 mt-md-0" style="gap: 10px;">
                                <!-- Search Box -->
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted small"></i></span>
                                    </div>
                                    <input type="text" id="searchStock" class="form-control border-left-0 shadow-none" placeholder="Cari nama bibit atau program...">
                                </div>
                                
                                <!-- View Toggle -->
                                <div class="btn-group btn-group-sm border rounded shadow-sm overflow-hidden" role="group">
                                    <button type="button" class="btn btn-white border-0 py-1 px-3 active-view" id="setViewCard" title="Card View">
                                        <i class="fas fa-th-large"></i>
                                    </button>
                                    <button type="button" class="btn btn-white border-0 py-1 px-3" id="setViewTable" title="Table View">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0 bg-white">
                            <!-- CARD VIEW CONTAINER -->
                            <div id="stockCardView" class="p-4 bg-light-soft">
                                <div class="row" id="stockCardContainer">
                                    <?php if (empty($readyStock)): ?>
                                        <div class="col-12 text-center py-5 text-muted no-results">
                                            <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                                            <p>Belum ada stok bibit siap salur.</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($readyStock as $rs): ?>
                                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4 stock-item-card" data-name="<?= strtolower($rs['seedling_name']) ?>" data-program="<?= strtolower($rs['program_type']) ?>">
                                                <div class="seedling-card h-100 shadow-hover transition-all">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start justify-content-between mb-3">
                                                            <div class="seedling-icon-wrapper">
                                                                <i class="fas fa-leaf"></i>
                                                            </div>
                                                            <div class="text-right">
                                                                <div class="qty-badge"><?= number_format($rs['quantity'], 0) ?> <small>btg</small></div>
                                                            </div>
                                                        </div>
                                                        <h6 class="font-weight-bold text-dark mb-1"><?= $rs['seedling_name'] ?></h6>
                                                        <div class="d-flex flex-wrap" style="gap: 5px;">
                                                            <span class="<?= $rs['program_type'] === 'bibitgratis' ? 'badge-custom-green' : 'badge-custom-blue' ?>">
                                                                <?= $rs['program_type'] === 'bibitgratis' ? 'PUB' : strtoupper($rs['program_type']) ?>
                                                            </span>
                                                            <?php if($user['role'] === 'admin'): ?>
                                                                <span class="badge-custom-info">
                                                                    <i class="fas fa-map-marker-alt mr-1"></i> <?= $rs['nursery_name'] ?? 'Persemaian' ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- TABLE VIEW CONTAINER (Hidden by default) -->
                            <div id="stockTableView" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead class="bg-light x-small text-uppercase font-weight-bold text-dark">
                                            <tr>
                                                <th class="pl-4">Nama Bibit</th>
                                                <th>BPDAS / Persemaian</th>
                                                <th>Program</th>
                                                <th class="text-right pr-4">Jumlah Stok (btg)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small" id="stockTableBody">
                                            <?php if (empty($readyStock)): ?>
                                                <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada data</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($readyStock as $rs): ?>
                                                    <tr class="stock-item-row" data-name="<?= strtolower($rs['seedling_name']) ?>" data-program="<?= strtolower($rs['program_type']) ?>">
                                                        <td class="pl-4">
                                                            <div class="font-weight-bold text-dark"><?= $rs['seedling_name'] ?></div>
                                                        </td>
                                                        <td>
                                                            <div class="x-small font-weight-bold"><?= $rs['bpdas_name'] ?? '-' ?></div>
                                                            <div class="x-small text-muted"><?= $rs['nursery_name'] ?? '-' ?></div>
                                                        </td>
                                                        <td>
                                                            <span class="<?= $rs['program_type'] === 'bibitgratis' ? 'badge-custom-green' : 'badge-custom-blue' ?>">
                                                                <?= $rs['program_type'] === 'bibitgratis' ? 'PUB' : strtoupper($rs['program_type']) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-right pr-4 font-weight-bold text-primary h6 mb-0">
                                                            <?= number_format($rs['quantity'], 0) ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- No Results Placeholder -->
                            <div id="searchNoResults" class="text-center py-5 text-muted d-none">
                                <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
                                <p>Tidak ada hasil yang cocok dengan pencarian Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- TAB 2: PRODUCTION HISTORY -->
        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="row">
                <!-- Mixing (MT) -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card shadow-sm h-100 border-0 overflow-hidden">
                        <div class="card-header py-2 bg-success text-white small font-weight-bold">
                            <i class="fas fa-mortar-pestle mr-1"></i> Mixing Media (MT)
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0 table-hover table-striped">
                                <tbody class="small">
                                    <?php if (!empty($recentProductions['data'])): ?>
                                        <?php foreach ($recentProductions['data'] as $prod): ?>
                                            <tr>
                                                <td class="p-2 border-bottom">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="badge badge-success px-2"><?= $prod['production_code'] ?></span>
                                                        <div class="d-flex align-items-center">
                                                            <a href="<?= url('seedling-edit/edit-media-mixing/' . $prod['id']) ?>" class="btn btn-xs btn-outline-primary py-0 px-1 mr-1" title="Edit"><i class="fas fa-edit"></i></a>
                                                            <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 btn-delete" data-url="<?= url('seedling-edit/delete-media-mixing/' . $prod['id']) ?>" data-title="Mixing Media <?= $prod['production_code'] ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="font-weight-bold text-dark"><?= number_format($prod['total_production'] ?? 0, 2) ?> <small>m3</small></div>
                                                    <div class="text-muted x-small italic"><?= $prod['notes'] ?: '-' ?></div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td class="text-center py-4 text-muted">Belum ada record</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($recentProductions['totalPages'] > 1): ?>
                            <div class="card-footer py-2 bg-white border-top-0 d-flex justify-content-center">
                                <nav>
                                    <ul class="pagination pagination-xs mb-0">
                                        <li class="page-item <?= ($recentProductions['page'] <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_mt' => $recentProductions['page'] - 1])) ?>#history"><i class="fas fa-chevron-left"></i></a>
                                        </li>
                                        <li class="page-item disabled"><span class="page-link"><?= $recentProductions['page'] ?> / <?= $recentProductions['totalPages'] ?></span></li>
                                        <li class="page-item <?= ($recentProductions['page'] >= $recentProductions['totalPages']) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_mt' => $recentProductions['page'] + 1])) ?>#history"><i class="fas fa-chevron-right"></i></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Polybag Fillings (PB) -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card shadow-sm h-100 border-0 overflow-hidden">
                        <div class="card-header py-2 bg-info text-white small font-weight-bold">
                            <i class="fas fa-fill-drip mr-1"></i> Pengisian Kantong (PB)
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0 table-hover table-striped">
                                <tbody class="small">
                                    <?php if (!empty($recentFillings['data'])): ?>
                                        <?php foreach ($recentFillings['data'] as $pb): ?>
                                            <tr>
                                                <td class="p-2 border-bottom">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="badge badge-info px-2"><?= $pb['filling_code'] ?></span>
                                                        <div class="d-flex align-items-center">
                                                            <span class="text-muted x-small font-weight-bold mr-2"><?= formatDate($pb['filling_date']) ?></span>
                                                            <a href="<?= url('seedling-edit/edit-bag-filling/' . $pb['id']) ?>" class="btn btn-xs btn-outline-primary py-0 px-1 mr-1" title="Edit"><i class="fas fa-edit"></i></a>
                                                            <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 btn-delete" data-url="<?= url('seedling-edit/delete-bag-filling/' . $pb['id']) ?>" data-title="Pengisian Kantong <?= $pb['filling_code'] ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="font-weight-bold text-dark"><?= $pb['bag_name'] ?></div>
                                                    <div class="d-flex justify-content-between align-items-center x-small mt-1">
                                                        <span class="text-muted">Stok Tersedia:</span>
                                                        <span class="text-primary font-weight-bold"><?= number_format($pb['remaining_stock'] ?? 0, 0) ?> poly</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td class="text-center py-4 text-muted">Belum ada record</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($recentFillings['totalPages'] > 1): ?>
                            <div class="card-footer py-2 bg-white border-top-0 d-flex justify-content-center">
                                <nav>
                                    <ul class="pagination pagination-xs mb-0">
                                        <li class="page-item <?= ($recentFillings['page'] <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_pb' => $recentFillings['page'] - 1])) ?>#history"><i class="fas fa-chevron-left"></i></a>
                                        </li>
                                        <li class="page-item disabled"><span class="page-link"><?= $recentFillings['page'] ?> / <?= $recentFillings['totalPages'] ?></span></li>
                                        <li class="page-item <?= ($recentFillings['page'] >= $recentFillings['totalPages']) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_pb' => $recentFillings['page'] + 1])) ?>#history"><i class="fas fa-chevron-right"></i></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tanam Benih (PC) -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card shadow-sm h-100 border-0 overflow-hidden">
                        <div class="card-header py-2 bg-warning text-dark font-weight-bold small">
                            <i class="fas fa-seedling mr-1"></i> Penaburan Benih (PC)
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0 table-hover table-striped">
                                <tbody class="small">
                                    <?php if (!empty($recentSowings['data'])): ?>
                                        <?php foreach ($recentSowings['data'] as $pc): ?>
                                            <tr>
                                                <td class="p-2 border-bottom">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="badge badge-warning text-dark px-2 font-weight-bold"><?= $pc['sowing_code'] ?></span>
                                                        <div class="d-flex align-items-center">
                                                            <span class="text-muted x-small font-weight-bold mr-2"><?= formatDate($pc['sowing_date']) ?></span>
                                                            <a href="<?= url('seedling-edit/edit-seed-sowing/' . $pc['id']) ?>" class="btn btn-xs btn-outline-primary py-0 px-1 mr-1" title="Edit"><i class="fas fa-edit"></i></a>
                                                            <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 btn-delete" data-url="<?= url('seedling-edit/delete-seed-sowing/' . $pc['id']) ?>" data-title="Penaburan Benih <?= $pc['sowing_code'] ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="font-weight-bold text-dark"><?= $pc['seed_name'] ?></div>
                                                    <div class="d-flex justify-content-between x-small">
                                                        <span class="font-weight-bold text-primary"><?= number_format($pc['total_polybags'] ?? 0, 0) ?> btg</span>
                                                        <span class="text-muted"><?= number_format($pc['seed_quantity'] ?? 0, 1) ?> <?= $pc['seed_unit'] ?></span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td class="text-center py-4 text-muted">Belum ada record</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($recentSowings['totalPages'] > 1): ?>
                            <div class="card-footer py-2 bg-white border-top-0 d-flex justify-content-center">
                                <nav>
                                    <ul class="pagination pagination-xs mb-0">
                                        <li class="page-item <?= ($recentSowings['page'] <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_pc' => $recentSowings['page'] - 1])) ?>#history"><i class="fas fa-chevron-left"></i></a>
                                        </li>
                                        <li class="page-item disabled"><span class="page-link"><?= $recentSowings['page'] ?> / <?= $recentSowings['totalPages'] ?></span></li>
                                        <li class="page-item <?= ($recentSowings['page'] >= $recentSowings['totalPages']) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_pc' => $recentSowings['page'] + 1])) ?>#history"><i class="fas fa-chevron-right"></i></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Panen (PA) -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card shadow-sm h-100 border-0 overflow-hidden">
                        <div class="card-header py-2 bg-primary text-white small font-weight-bold">
                            <i class="fas fa-leaf mr-1"></i> Panen Anakan (PA)
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0 table-hover table-striped">
                                <tbody class="small">
                                    <?php if (!empty($recentHarvests['data'])): ?>
                                        <?php foreach ($recentHarvests['data'] as $pa): ?>
                                            <tr>
                                                <td class="p-2 border-bottom">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="badge badge-primary px-2"><?= $pa['harvest_code'] ?></span>
                                                        <div class="d-flex align-items-center">
                                                            <span class="text-muted x-small font-weight-bold mr-2"><?= formatDate($pa['harvest_date']) ?></span>
                                                            <a href="<?= url('seedling-edit/edit-harvesting/' . $pa['id']) ?>" class="btn btn-xs btn-outline-primary py-0 px-1 mr-1" title="Edit"><i class="fas fa-edit"></i></a>
                                                            <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 btn-delete" data-url="<?= url('seedling-edit/delete-harvesting/' . $pa['id']) ?>" data-title="Panen Anakan <?= $pa['harvest_code'] ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="font-weight-bold text-dark"><?= $pa['seed_name'] ?></div>
                                                    <div class="d-flex justify-content-between x-small">
                                                        <span class="text-muted">PA-Code: <?= $pa['harvest_code'] ?></span>
                                                        <span class="text-success font-weight-bold text-success"><?= number_format($pa['remaining_stock'] ?? 0, 0) ?> sisa</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td class="text-center py-4 text-muted">Belum ada record</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($recentHarvests['totalPages'] > 1): ?>
                            <div class="card-footer py-2 bg-white border-top-0 d-flex justify-content-center">
                                <nav>
                                    <ul class="pagination pagination-xs mb-0">
                                        <li class="page-item <?= ($recentHarvests['page'] <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_pa' => $recentHarvests['page'] - 1])) ?>#history"><i class="fas fa-chevron-left"></i></a>
                                        </li>
                                        <li class="page-item disabled"><span class="page-link"><?= $recentHarvests['page'] ?> / <?= $recentHarvests['totalPages'] ?></span></li>
                                        <li class="page-item <?= ($recentHarvests['page'] >= $recentHarvests['totalPages']) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_pa' => $recentHarvests['page'] + 1])) ?>#history"><i class="fas fa-chevron-right"></i></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Weaning (PE/ET) -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card shadow-sm h-100 border-0 overflow-hidden">
                        <div class="card-header py-2 bg-teal text-white small font-weight-bold">
                            <i class="fas fa-expand-arrows-alt mr-1"></i> Sapih Bibit (PE/ET)
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0 table-hover table-striped">
                                <tbody class="small">
                                    <?php if (!empty($recentWeanings['data'])): ?>
                                        <?php foreach ($recentWeanings['data'] as $pe): ?>
                                            <tr>
                                                <td class="p-2 border-bottom">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="badge badge-teal text-white px-2"><?= $pe['weaning_code'] ?></span>
                                                        <div class="d-flex align-items-center">
                                                            <span class="text-muted x-small font-weight-bold mr-2"><?= formatDate($pe['weaning_date']) ?></span>
                                                            <?php $isEntres = strpos($pe['weaning_code'], 'ET-') === 0; ?>
                                                            <a href="<?= url('seedling-edit/' . ($isEntres ? 'edit-entres' : 'edit-weaning') . '/' . $pe['id']) ?>" class="btn btn-xs btn-outline-primary py-0 px-1 mr-1" title="Edit"><i class="fas fa-edit"></i></a>
                                                            <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 btn-delete" data-url="<?= url('seedling-edit/' . ($isEntres ? 'delete-entres' : 'delete-weaning') . '/' . $pe['id']) ?>" data-title="Sapih/Entres <?= $pe['weaning_code'] ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="font-weight-bold text-dark"><?= $pe['result_name'] ?></div>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="x-small text-muted">Dari: <?= $pe['harvest_code'] ?></span>
                                                        <span class="text-success font-weight-bold"><?= number_format($pe['remaining_stock'] ?? 0, 0) ?> btg</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td class="text-center py-4 text-muted">Belum ada record</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($recentWeanings['totalPages'] > 1): ?>
                            <div class="card-footer py-2 bg-white border-top-0 d-flex justify-content-center">
                                <nav>
                                    <ul class="pagination pagination-xs mb-0">
                                        <li class="page-item <?= ($recentWeanings['page'] <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_pe' => $recentWeanings['page'] - 1])) ?>#history"><i class="fas fa-chevron-left"></i></a>
                                        </li>
                                        <li class="page-item disabled"><span class="page-link"><?= $recentWeanings['page'] ?> / <?= $recentWeanings['totalPages'] ?></span></li>
                                        <li class="page-item <?= ($recentWeanings['page'] >= $recentWeanings['totalPages']) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_pe' => $recentWeanings['page'] + 1])) ?>#history"><i class="fas fa-chevron-right"></i></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Out Mutation (BO) -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card shadow-sm h-100 border-0 overflow-hidden">
                        <div class="card-header py-2 bg-danger text-white small font-weight-bold">
                            <i class="fas fa-exchange-alt mr-1"></i> Log Mutasi (BO-PUB)
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0 table-hover table-striped">
                                <tbody class="small">
                                    <?php if (!empty($recentMutations['data'])): ?>
                                        <?php foreach ($recentMutations['data'] as $bo): ?>
                                            <tr>
                                                <td class="p-2 border-bottom">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="badge-source"><?= $bo['source_code'] ?></span>
                                                        <div class="d-flex align-items-center">
                                                            <span class="text-muted x-small font-weight-bold mr-2"><?= formatDate($bo['mutation_date']) ?></span>
                                                            <?php if ($bo['mutation_type'] === 'NAIK KELAS'): ?>
                                                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1 mr-1" title="Cetak QR Code" onclick="openQRModalDashboard('<?= $bo['source_type'] ?>', <?= $bo['source_id'] ?>, '<?= $bo['source_code'] ?>', '<?= $bo['seedling_name'] ?>')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-xs btn-outline-info py-0 px-1 mr-1" title="Lacak Asal-Usul" onclick="openTraceabilityModalDashboard('<?= $bo['source_type'] ?>', <?= $bo['source_id'] ?>, '<?= $bo['source_code'] ?> - <?= $bo['seedling_name'] ?>')">
                                                                    <i class="fas fa-search-location"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                            <a href="<?= url('seedling-edit/edit-mutation/' . $bo['id']) ?>" class="btn btn-xs btn-outline-primary py-0 px-1 mr-1" title="Edit"><i class="fas fa-edit"></i></a>
                                                            <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 btn-delete" data-url="<?= url('seedling-edit/delete-mutation/' . $bo['id']) ?>" data-title="Mutasi <?= $bo['source_code'] ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="font-weight-bold text-dark"><?= $bo['seedling_name'] ?></div>
                                                    <div class="d-flex justify-content-between x-small">
                                                        <span class="font-weight-bold <?= $bo['mutation_type'] === 'MATI' ? 'text-danger' : 'text-primary' ?>"><?= $bo['mutation_type'] ?></span>
                                                        <span class="text-danger font-weight-bold"><?= number_format($bo['quantity'] ?? 0, 0) ?> btg</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td class="text-center py-4 text-muted">Belum ada record</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($recentMutations['totalPages'] > 1): ?>
                            <div class="card-footer py-2 bg-white border-top-0 d-flex justify-content-center">
                                <nav>
                                    <ul class="pagination pagination-xs mb-0">
                                        <li class="page-item <?= ($recentMutations['page'] <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_bo' => $recentMutations['page'] - 1])) ?>#history"><i class="fas fa-chevron-left"></i></a>
                                        </li>
                                        <li class="page-item disabled"><span class="page-link"><?= $recentMutations['page'] ?> / <?= $recentMutations['totalPages'] ?></span></li>
                                        <li class="page-item <?= ($recentMutations['page'] >= $recentMutations['totalPages']) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_bo' => $recentMutations['page'] + 1])) ?>#history"><i class="fas fa-chevron-right"></i></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: INVENTORY & TRANSACTIONS -->
        <div class="tab-pane fade" id="inventory" role="tabpanel">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header py-3 bg-white border-bottom">
                            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-archive mr-2 text-primary"></i> Saldo Stok Bahan Baku (Inventory)</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="bg-light x-small text-uppercase font-weight-bold text-dark">
                                        <tr>
                                            <th>Bahan Baku / Kategori</th>
                                            <th class="text-right">Total Masuk</th>
                                            <th class="text-right">Total Keluar</th>
                                            <th class="text-right">Sisa Stok</th>
                                            <th>Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        <?php foreach ($stockBalance as $stock): ?>
                                            <tr>
                                                <td>
                                                    <div class="font-weight-bold"><?= $stock['name'] ?></div>
                                                    <span class="text-muted x-small"><?= $stock['category'] ?></span>
                                                </td>
                                                <td class="text-right font-weight-bold text-muted"><?= number_format($stock['total_in'] ?? 0, 2) ?></td>
                                                <td class="text-right font-weight-bold text-danger"><?= number_format($stock['total_out'] ?? 0, 2) ?></td>
                                                <td class="text-right font-weight-bold <?= $stock['current_stock'] <= 0 ? 'text-danger' : 'text-success' ?>" style="font-size: 1.1rem;">
                                                    <?= number_format($stock['current_stock'] ?? 0, 2) ?>
                                                </td>
                                                <td><small class="font-weight-bold text-uppercase text-muted"><?= $stock['unit'] ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bahan Baku IN Full Row -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow-sm border-0 border-top-primary">
                        <div class="card-header py-2 bg-light d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold small text-dark"><i class="fas fa-receipt mr-2 text-primary"></i> Transaksi Pengadaan Bahan Terbaru</h6>
                            <a href="<?= url('seedling-admin/bahan-baku-form') ?>" class="btn btn-sm btn-link font-weight-bold p-0">Lihat Semua</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="bg-light x-small text-muted font-weight-bold text-uppercase text-dark">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Item Barang</th>
                                            <th class="text-right">Jumlah Masuk</th>
                                            <th>Gudang/Unit</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        <?php if (!empty($recentTransactions['data'])): ?>
                                            <?php foreach ($recentTransactions['data'] as $row): ?>
                                                <tr>
                                                    <td class="p-2"><?= formatDate($row['transaction_date']) ?></td>
                                                    <td class="p-2 font-weight-bold"><?= $row['item_name'] ?></td>
                                                    <td class="p-2 text-right font-weight-bold text-primary"><?= number_format($row['quantity'] ?? 0, 2) ?></td>
                                                    <td class="p-2"><?= $row['item_unit'] ?></td>
                                                    <td class="p-2 text-center">
                                                        <a href="<?= url('seedling-edit/edit-bahan-baku/' . $row['id']) ?>" class="btn btn-xs btn-outline-primary py-0 px-1 mr-1" title="Edit"><i class="fas fa-edit"></i></a>
                                                        <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 btn-delete" data-url="<?= url('seedling-edit/delete-bahan-baku/' . $row['id']) ?>" data-title="Bahan Baku <?= $row['item_name'] ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if ($recentTransactions['totalPages'] > 1): ?>
                            <div class="card-footer py-2 bg-white border-top-0 d-flex justify-content-center">
                                <nav>
                                    <ul class="pagination pagination-xs mb-0">
                                        <li class="page-item <?= ($recentTransactions['page'] <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_in' => $recentTransactions['page'] - 1])) ?>#inventory"><i class="fas fa-chevron-left"></i></a>
                                        </li>
                                        <li class="page-item disabled"><span class="page-link"><?= $recentTransactions['page'] ?> / <?= $recentTransactions['totalPages'] ?></span></li>
                                        <li class="page-item <?= ($recentTransactions['page'] >= $recentTransactions['totalPages']) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= url('seedling-admin') . '?' . http_build_query(array_merge($_GET, ['page_in' => $recentTransactions['page'] + 1])) ?>#inventory"><i class="fas fa-chevron-right"></i></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Traceability -->
<div class="modal fade" id="traceabilityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white border-bottom-0">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-project-diagram mr-2"></i> Lacak Asal-Usul Bibit (Traceability)</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body bg-light p-4" id="traceabilityBody">
                <!-- Content injected via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Code Dashboard -->
<div class="modal fade" id="qrModalDashboard" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white border-bottom-0">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-qrcode mr-2"></i> Cetak QR Code</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body bg-light p-4 text-center">
                <div id="qrcodeDashboard" class="d-inline-block bg-white p-2 rounded shadow-sm mb-3"></div>
                <h6 class="font-weight-bold mb-1" id="qr_batch_code_dashboard">-</h6>
                <p class="small text-muted mb-3" id="qr_seed_name_dashboard">-</p>
                <button class="btn btn-primary btn-block shadow-sm" onclick="printThermalQRDashboard()"><i class="fas fa-print mr-1"></i> Print Stiker</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Auto-activate tab from URL hash (#history / #inventory) ──
    const hash = window.location.hash;
    if (hash) {
        const tabLink = document.querySelector('#dashboardTabs a[href="' + hash + '"]');
        if (tabLink) {
            document.querySelectorAll('#dashboardTabs .nav-link').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('#dashboardTabsContent .tab-pane').forEach(el => el.classList.remove('show', 'active'));
            tabLink.classList.add('active');
            const target = document.querySelector(hash);
            if (target) target.classList.add('show', 'active');
        }
    }

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

// Traceability Modal Script
window.openTraceabilityModalDashboard = function(sourceType, sourceId, titleLabel) {
    if (!sourceId || !sourceType) return;

    $('#traceabilityModal').modal('show');
    const modalBody = document.getElementById('traceabilityBody');
    modalBody.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-info mb-3"></i><p class="text-muted">Melacak riwayat bibit...</p></div>';

    fetch(`<?= url('seedling-admin/get-batch-traceability-ajax') ?>?source_type=${sourceType}&source_id=${sourceId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                renderTraceabilityDataDashboard(data.data, titleLabel, sourceType);
            } else {
                modalBody.innerHTML = `<div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-2"></i> ${data.message || 'Data tidak ditemukan.'}</div>`;
            }
        })
        .catch(err => {
            modalBody.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle mr-2"></i> Terjadi kesalahan jaringan.</div>';
        });
};

function renderTraceabilityDataDashboard(data, title, sourceType) {
    const modalBody = document.getElementById('traceabilityBody');
    
    let html = `
        <h5 class="font-weight-bold text-dark mb-4 border-bottom pb-2">${title}</h5>
        <div class="row">
    `;

    // Kolom Kiri
    html += `<div class="col-md-6 mb-3">`;

    // 1. Sumber Benih
    if (data.seed_source) {
        html += `
            <div class="card shadow-sm border-left-primary mb-3">
                <div class="card-body p-3">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><i class="fas fa-seedling mr-1"></i> Asal Benih / Genetik</div>
                    <div class="h6 mb-1 font-weight-bold text-gray-800">${data.seed_source.name}</div>
                    <div class="small text-muted">
                        Vendor: ${data.seed_source.vendor || '-'}<br>
                        Kab/Kota: ${data.seed_source.kabupaten || '-'}<br>
                        Sertifikat: ${data.seed_source.sertifikat || '-'}
                    </div>
                </div>
            </div>
        `;
    } else {
        html += `
            <div class="card shadow-sm border-left-secondary mb-3">
                <div class="card-body p-3">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1"><i class="fas fa-seedling mr-1"></i> Asal Benih / Genetik</div>
                    <div class="small text-muted">Tidak ada data sumber benih yang dilacak untuk batch ini.</div>
                </div>
            </div>
        `;
    }

    // 2. Riwayat Penaburan
    if (data.sowing) {
        html += `
            <div class="card shadow-sm border-left-warning mb-3">
                <div class="card-body p-3">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"><i class="fas fa-hand-holding-water mr-1"></i> Riwayat Penaburan (PC)</div>
                    <div class="h6 mb-1 font-weight-bold text-gray-800">${data.sowing.code}</div>
                    <div class="small text-muted">
                        Tanggal: ${data.sowing.date}<br>
                        Jumlah Benih: ${data.sowing.seed_quantity} ${data.sowing.seed_unit}
                    </div>
                </div>
            </div>
        `;
    }
    
    html += `</div>`; // End Kolom Kiri

    // Kolom Kanan
    html += `<div class="col-md-6 mb-3">`;

    // 3. Riwayat Penyapihan
    if (data.weaning) {
        html += `
            <div class="card shadow-sm border-left-success mb-3">
                <div class="card-body p-3">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><i class="fas fa-expand-arrows-alt mr-1"></i> Riwayat Sapih/Entres (PE/ET)</div>
                    <div class="h6 mb-1 font-weight-bold text-gray-800">${data.weaning.code}</div>
                    <div class="small text-muted">
                        Tanggal: ${data.weaning.date}<br>
                        Lokasi Awal: ${data.weaning.location || '-'}<br>
                        Qty Sapih/Potong: ${Number(data.weaning.quantity).toLocaleString('id-ID')} btg
                    </div>
                </div>
            </div>
        `;
    }

    // 4. Komposisi Media
    if (data.media && data.media.items.length > 0) {
        let mediaItemsHtml = data.media.items.map(m => `<li>${m.name} &mdash; ${m.quantity} ${m.unit}</li>`).join('');
        html += `
            <div class="card shadow-sm border-left-info mb-3">
                <div class="card-body p-3">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1"><i class="fas fa-mortar-pestle mr-1"></i> Media Tanam (MT)</div>
                    <div class="h6 mb-1 font-weight-bold text-gray-800">${data.media.code}</div>
                    <div class="small text-muted mt-2">Komposisi:</div>
                    <ul class="small text-muted pl-3 mb-0">
                        ${mediaItemsHtml}
                    </ul>
                </div>
            </div>
        `;
    } else if (sourceType === 'PE') {
        html += `
            <div class="card shadow-sm border-left-secondary mb-3">
                <div class="card-body p-3">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1"><i class="fas fa-mortar-pestle mr-1"></i> Media Tanam (MT)</div>
                    <div class="small text-muted">Data komposisi media tidak ditemukan untuk batch ini.</div>
                </div>
            </div>
        `;
    }

    html += `</div>`; // End Kolom Kanan
    html += `</div>`; // End Row

    modalBody.innerHTML = html;
}

let qrcodeDashboard = null;

window.openQRModalDashboard = function(sourceType, sourceId, code, name) {
    if (!sourceId || !sourceType) return;

    document.getElementById('qr_batch_code_dashboard').innerText = code;
    document.getElementById('qr_seed_name_dashboard').innerText = name;

    // Generate URL (dynamic base URL)
    const traceUrl = `<?= url('public/trace') ?>/${sourceType}/${sourceId}`;

    const qrContainer = document.getElementById('qrcodeDashboard');
    qrContainer.innerHTML = ''; // Clear previous

    qrcodeDashboard = new QRCode(qrContainer, {
        text: traceUrl,
        width: 150,
        height: 150,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    // Store current state for print
    qrContainer.dataset.code = code;
    qrContainer.dataset.name = name;

    $('#qrModalDashboard').modal('show');
};

window.printThermalQRDashboard = function() {
    const qrContainer = document.getElementById('qrcodeDashboard');
    const code = qrContainer.dataset.code;
    const name = qrContainer.dataset.name;
    const date = new Date().toLocaleDateString('id-ID');
    
    const qrImg = document.querySelector('#qrcodeDashboard img').src;
    
    const printWindow = window.open('', '_blank', 'width=400,height=400');
    printWindow.document.write(`
        <html>
        <head>
            <title>Print QR Code</title>
            <style>
                @page { margin: 0; size: 58mm 40mm; }
                body { 
                    margin: 0; 
                    padding: 2mm; 
                    width: 54mm; 
                    height: 36mm;
                    font-family: monospace; 
                    box-sizing: border-box;
                }
                .container {
                    display: flex;
                    width: 100%;
                    height: 100%;
                }
                .qr-box {
                    width: 25mm;
                    height: 25mm;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .qr-box img {
                    width: 100%;
                    height: 100%;
                }
                .info-box {
                    flex: 1;
                    padding-left: 2mm;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                }
                .title { font-weight: bold; font-size: 10px; margin-bottom: 2px; border-bottom: 1px solid #000; padding-bottom: 2px; }
                .detail { font-size: 8px; line-height: 1.2; }
                .url { font-size: 6px; margin-top: auto; text-align: center; word-break: break-all; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="qr-box">
                    <img src="${qrImg}" />
                </div>
                <div class="info-box">
                    <div class="title">🌳 BIBIT<br>${name.substring(0, 15)}</div>
                    <div class="detail">
                        Batch:<br>${code}<br>
                        Tgl: ${date}
                    </div>
                </div>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
};
</script>

<style>
    :root {
        --teal: #008080;
        --primary-soft: rgba(78, 115, 223, 0.1);
        --bg-gray-100: #f8f9fc;
    }

    .letter-spacing-1 { letter-spacing: 1px; }
    .bg-teal { background-color: var(--teal) !important; }
    .transition-all { transition: all 0.3s ease; }

    /* Stat Card Improvement */
    .stat-card {
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,0.05) !important;
    }
    .stat-icon {
        position: absolute;
        right: 15px;
        bottom: 15px;
        font-size: 3rem;
        color: rgba(0,0,0,0.05);
        z-index: 0;
    }

    /* Action Buttons Modern */
    .action-btn-modern {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border-radius: 8px;
        text-decoration: none !important;
        min-width: 140px;
        transition: all 0.2s;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .action-btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    .action-btn-modern i {
        font-size: 1.5rem;
        margin-right: 12px;
        opacity: 0.8;
    }
    .action-btn-modern .content {
        display: flex;
        flex-direction: column;
        line-height: 1.1;
    }
    .action-btn-modern .content .title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .action-btn-modern .content .code {
        font-size: 0.65rem;
        opacity: 0.7;
    }

    /* Seedling Cards Modern */
    .seedling-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e3e6f0;
        overflow: hidden;
    }
    .seedling-card:hover {
        border-color: #4e73df;
        transform: scale(1.02);
    }
    .seedling-icon-wrapper {
        width: 40px;
        height: 40px;
        background: var(--primary-soft);
        color: #4e73df;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.2rem;
    }
    .qty-badge {
        font-size: 1.25rem;
        font-weight: 800;
        color: #2e3b4e;
    }
    .qty-badge small {
        font-weight: 400;
        color: #858796;
        font-size: 0.7rem;
        text-transform: uppercase;
    }

    /* Badges Custom */
    .badge-custom-green {
        background-color: #e6fffa;
        color: #2c7a7b;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 700;
        border: 1px solid #b2f5ea;
    }
    .badge-custom-blue {
        background-color: #ebf4ff;
        color: #2b6cb0;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 700;
        border: 1px solid #bee3f8;
    }
    .badge-custom-info {
        background-color: #f7fafc;
        color: #4a5568;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 400;
        border: 1px solid #e2e8f0;
    }

    .shadow-hover:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .bg-teal { background-color: var(--teal) !important; }
    .badge-teal { background-color: var(--teal) !important; color: white !important; }
    .border-top-primary { border-top: 3px solid #4e73df !important; }

    .badge-source {
        background-color: #334155;
        color: #f8fafc;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        display: inline-block;
        margin-bottom: 2px;
    }
    .x-small { font-size: 0.75rem; }
    .border-left-teal { border-left: 0.25rem solid var(--teal) !important; }

    @media (max-width: 768px) {
        .action-btn-modern {
            min-width: 120px;
            padding: 8px 10px;
        }
    }
    .active-view { background-color: #4e73df !important; color: white !important; }
    .btn-white { background-color: white; color: #4e73df; border: 1px solid #4e73df; }
    .btn-white:hover { background-color: #f8f9fc; }
    
    .bg-light-soft { background-color: #fbfcfe; }
    /* Tabs Styling */
    .nav-pills .nav-link {
        color: #4a5568;
        border-radius: 8px;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .nav-pills .nav-link.active {
        background-color: #ebf4ff !important;
        color: #4e73df !important;
        border-color: #4e73df !important;
    }
    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f8f9fc;
    }
</style>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="deleteForm" method="POST" action="">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i> Konfirmasi Hapus Data</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus <strong><span id="deleteItemTitle"></span></strong>?</p>
                    <div class="alert alert-warning small">
                        <i class="fas fa-info-circle mr-1"></i> Data yang dihapus tidak dapat dikembalikan. Stok yang berkaitan dengan transaksi ini akan otomatis direvert (dikembalikan) ke posisi awal. History penghapusan akan tercatat.
                    </div>
                    <div class="form-group mb-0 mt-3">
                        <label class="small font-weight-bold text-danger">Alasan Hapus <span class="text-danger">*</span></label>
                        <textarea name="delete_reason" class="form-control border-danger" rows="2" required placeholder="Wajib diisi. Contoh: Salah input, data ganda, dll..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash mr-1"></i> Hapus Permanen</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete Button Click Handler
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            const title = this.getAttribute('data-title');
            
            document.getElementById('deleteForm').setAttribute('action', url);
            document.getElementById('deleteItemTitle').textContent = title;
            
            $('#deleteModal').modal('show');
        });
    });

    // --- STOCK VIEW TOGGLE & SEARCH ---
    const searchInput = document.getElementById('searchStock');
    const btnSetCard = document.getElementById('setViewCard');
    const btnSetTable = document.getElementById('setViewTable');
    const cardView = document.getElementById('stockCardView');
    const tableView = document.getElementById('stockTableView');
    const noResults = document.getElementById('searchNoResults');

    // Toggle logic
    function switchView(to) {
        if (to === 'table') {
            cardView.classList.add('d-none');
            tableView.classList.remove('d-none');
            btnSetTable.classList.add('active-view');
            btnSetCard.classList.remove('active-view');
            localStorage.setItem('stock_view_pref', 'table');
        } else {
            tableView.classList.add('d-none');
            cardView.classList.remove('d-none');
            btnSetCard.classList.add('active-view');
            btnSetTable.classList.remove('active-view');
            localStorage.setItem('stock_view_pref', 'card');
        }
    }

    btnSetCard.addEventListener('click', () => switchView('card'));
    btnSetTable.addEventListener('click', () => switchView('table'));

    // Persistent preference
    const pref = localStorage.getItem('stock_view_pref');
    if (pref) switchView(pref);

    // Live Search logic
    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.stock-item-card');
        const rows = document.querySelectorAll('.stock-item-row');
        let visibleCount = 0;

        // Filter cards
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const program = card.getAttribute('data-program');
            if (name.includes(term) || program.includes(term)) {
                card.classList.remove('d-none');
                visibleCount++;
            } else {
                card.classList.add('d-none');
            }
        });

        // Filter rows
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const program = row.getAttribute('data-program');
            if (name.includes(term) || program.includes(term)) {
                row.classList.remove('d-none');
            } else {
                row.classList.add('d-none');
            }
        });

        // Toggle no results
        if (visibleCount === 0 && term !== '') {
            noResults.classList.remove('d-none');
            cardView.classList.add('d-none');
            tableView.classList.add('d-none');
        } else {
            noResults.classList.add('d-none');
            if (btnSetCard.classList.contains('active-view')) {
                cardView.classList.remove('d-none');
            } else {
                tableView.classList.remove('d-none');
            }
        }
    });
});
</script>
