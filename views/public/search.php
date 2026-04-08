<?php
/**
 * Search Results Page
 * Display BPDAS search results with filters
 */
?>

<div class="container search-page-container">
    <h1>Hasil Pencarian BPDAS</h1>
    <p class="text-light">Temukan BPDAS terdekat dengan stok bibit yang Anda butuhkan</p>

    <!-- Search Layout: filter sidebar + results -->
    <div class="search-layout">

        <!-- Filter Panel -->
        <aside class="search-filter-panel">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Filter Pencarian</h4>
                </div>
                <div class="card-body">
                    <form action="<?= url('home/search') ?>" method="GET">
                        <div class="form-group">
                            <label class="form-label">Provinsi</label>
                            <select name="province_id" class="form-control">
                                <option value="">-- Semua Provinsi --</option>
                                <?php foreach ($provinces as $province): ?>
                                    <option value="<?= $province['id'] ?>" <?= ($filters['province_id'] == $province['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($province['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">BPDAS</label>
                            <select name="bpdas_id" class="form-control">
                                <option value="">-- Semua BPDAS --</option>
                                <?php foreach ($bpdasList as $bpdas): ?>
                                    <option value="<?= $bpdas['id'] ?>" <?= ($filters['bpdas_id'] == $bpdas['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($bpdas['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Persemaian</label>
                            <select name="nursery_id" class="form-control">
                                <option value="">-- Semua Persemaian --</option>
                                <?php foreach ($nurseries as $nursery): ?>
                                    <option value="<?= $nursery['id'] ?>" <?= ($filters['nursery_id'] == $nursery['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($nursery['name']) ?> (<?= htmlspecialchars($nursery['bpdas_name']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Jenis Bibit</label>
                            <select name="seedling_type_id" class="form-control">
                                <option value="">-- Semua Jenis --</option>
                                <?php foreach ($seedlingTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>" <?= ($filters['seedling_type_id'] == $type['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Stok Minimal</label>
                            <input type="number" name="min_stock" class="form-control"
                                   value="<?= htmlspecialchars($filters['min_stock'] ?? '') ?>"
                                   placeholder="Contoh: 100">
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;">
                            <i class="fas fa-search"></i> Terapkan Filter
                        </button>

                        <a href="<?= url('home/search') ?>" class="btn btn-outline mt-2" style="width:100%;">
                            Reset Filter
                        </a>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Results Panel -->
        <section class="search-results-panel">
            <?php if (empty($results['data'])): ?>
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-search" style="font-size:4rem; color:var(--text-light);"></i>
                        <h3>Tidak Ada Hasil</h3>
                        <p>Tidak ditemukan BPDAS yang sesuai dengan kriteria pencarian Anda.</p>
                        <a href="<?= url('home/search') ?>" class="btn btn-primary">Reset Pencarian</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <p>Menampilkan <strong><?= count($results['data']) ?></strong> dari <strong><?= $results['total'] ?></strong> BPDAS</p>
                </div>

                <div class="search-results-grid">
                    <?php foreach ($results['data'] as $bpdas): ?>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?= htmlspecialchars($bpdas['name']) ?></h4>
                                <span class="badge badge-primary"><?= htmlspecialchars($bpdas['province_name']) ?></span>
                            </div>
                            <div class="card-body">
                                <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($bpdas['address']) ?></p>
                                <p><i class="fas fa-phone"></i> <?= htmlspecialchars($bpdas['phone'] ?? '-') ?></p>

                                <div class="search-stat-row">
                                    <div class="stats-card">
                                        <span class="stats-number"><?= $bpdas['seedling_types_count'] ?? 0 ?></span>
                                        <span class="stats-label">Jenis Bibit</span>
                                    </div>
                                    <div class="stats-card success">
                                        <span class="stats-number"><?= formatNumber($bpdas['total_stock'] ?? 0) ?></span>
                                        <span class="stats-label">Total Stok</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="<?= url('home/detail/' . $bpdas['id']) ?>" class="btn btn-primary" style="width:100%;">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($results['totalPages'] > 1): ?>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $results['totalPages']; $i++): ?>
                            <li>
                                <a href="<?= url('home/search?page=' . $i . '&' . http_build_query($filters)) ?>"
                                   class="<?= ($i == $results['page']) ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>
        </section>

    </div><!-- /.search-layout -->
</div><!-- /.container -->
