<?php
/**
 * Admin Dashboard View
 * Displays analytics and statistics for administrators
 */
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-chart-line"></i> Dashboard Admin</h1>
        <p>Selamat datang di Dashboard Stok Bibit Persemaian Indonesia</p>
    </div>
    
    <!-- Global Program Filter -->
    <div class="program-filter">
        <form action="<?= url('admin/dashboard') ?>" method="GET" class="form-inline">
            <label for="program_type" class="mr-2 font-weight-bold">Filter Program:</label>
            <select name="program_type" id="program_type" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Program</option>
                <option value="Reguler" <?= (($currentProgram ?? '') === 'Reguler') ? 'selected' : '' ?>>Reguler</option>
                <option value="FOLU" <?= (($currentProgram ?? '') === 'FOLU') ? 'selected' : '' ?>>FOLU Net Sink 2030</option>
            </select>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--primary-color);">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-content">
            <h3><?= formatNumber($stats['total_bpdas'] ?? 0) ?></h3>
            <p>Total BPDAS & BPTH</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--success-color);">
            <i class="fas fa-seedling"></i>
        </div>
        <div class="stat-content">
            <h3><?= formatNumber($stats['total_seedling_types'] ?? 0) ?></h3>
            <p>Jenis Bibit</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--info-color);">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-content">
            <h3><?= formatNumber($stats['total_national_stock'] ?? 0) ?></h3>
            <p>Total Stok Nasional</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--warning-color);">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h3><?= formatNumber($stats['pending_requests'] ?? 0) ?></h3>
            <p>Permintaan Pending</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #8e44ad;">
            <i class="fas fa-truck"></i>
        </div>
        <div class="stat-content">
            <h3><?= formatNumber($stats['total_distributed'] ?? 0) ?></h3>
            <p>Total Bibit Terdistribusi</p>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mt-4">
    <!-- Chart 1: Stock per Province -->
    <!-- Chart 1: Stock per Province -->
    <div class="col-lg-4 col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h3><i class="fas fa-map-marked-alt"></i> Stok per Provinsi</h3>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                    <canvas id="stockByProvinceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 2: Top 10 Seedlings -->
    <div class="col-lg-4 col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h3><i class="fas fa-chart-bar"></i> Top 10 Jenis Bibit</h3>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                    <canvas id="topSeedlingsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart 3: Monthly Distribution -->
    <div class="col-lg-4 col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h3><i class="fas fa-chart-area"></i> Distribusi Bulanan</h3>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Distribution Recap Section -->
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:0.5rem;">
        <h3 style="margin:0;"><i class="fas fa-truck" style="color:#8e44ad;"></i> Rekap Bibit Terdistribusi per BPDAS</h3>
        <form action="<?= url('admin/dashboard') ?>" method="GET" class="form-inline" style="gap:0.5rem;">
            <?php if (!empty($currentProgram)): ?>
                <input type="hidden" name="program_type" value="<?= htmlspecialchars($currentProgram) ?>">
            <?php endif; ?>
            <select name="bpdas_id" id="filter_bpdas" class="form-control form-control-sm" style="min-width:180px;">
                <option value="">Semua BPDAS & BPTH</option>
                <?php foreach ($bpdasList as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= ($filterBpdasId == $b['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="year" id="filter_year" class="form-control form-control-sm">
                <?php foreach ($availableYears as $yr): ?>
                    <option value="<?= $yr ?>" <?= ($filterYear == $yr) ? 'selected' : '' ?>><?= $yr ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Terapkan</button>
            <a href="<?= url('admin/dashboard') ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-undo"></i> Reset</a>
        </form>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($distributedByBPDAS)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>BPDAS / BPTH</th>
                        <th>Provinsi</th>
                        <th class="text-center">Jumlah Permintaan</th>
                        <th class="text-right">Total Bibit Diserahkan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $grandTotal = 0; ?>
                    <?php foreach ($distributedByBPDAS as $i => $row): ?>
                        <?php $grandTotal += $row['total_distributed']; ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= htmlspecialchars($row['bpdas_name']) ?></strong></td>
                            <td><small class="text-muted"><?= htmlspecialchars($row['province_name']) ?></small></td>
                            <td class="text-center"><?= formatNumber($row['total_requests']) ?></td>
                            <td class="text-right"><span class="badge badge-pill" style="background:#8e44ad;color:white;font-size:1em;"><?= formatNumber($row['total_distributed']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold" style="background:#f0eaf5;">
                        <td colspan="4" class="text-right">TOTAL (Tahun <?= $filterYear ?>)</td>
                        <td class="text-right" style="color:#8e44ad;font-size:1.1em;"><?= formatNumber($grandTotal) ?> bibit</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-truck fa-3x mb-3" style="opacity:0.2;"></i>
                <p>Belum ada data distribusi untuk filter yang dipilih.</p>
            </div>
        <?php endif; ?>
    </div>
</div>


<script nonce="<?= cspNonce() ?>">
document.addEventListener('DOMContentLoaded', function() {
    // Prepare data for charts
    const stockByProvinceData = <?= json_encode($stockByProvince ?? []) ?>;
    const topSeedlingsData = <?= json_encode($topSeedlings ?? []) ?>;

    console.log('Stock by Province:', stockByProvinceData);
    console.log('Top Seedlings:', topSeedlingsData);

    // Stock by Province Chart
    if (stockByProvinceData && stockByProvinceData.length > 0) {
        const ctx1 = document.getElementById('stockByProvinceChart');
        if (ctx1) {
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: stockByProvinceData.map(item => item.province_name || 'Unknown'),
                    datasets: [{
                        label: 'Total Stok',
                        data: stockByProvinceData.map(item => parseInt(item.total_stock) || 0),
                        backgroundColor: 'rgba(46, 125, 50, 0.7)',
                        borderColor: 'rgba(46, 125, 50, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Stok: ' + context.parsed.y.toLocaleString() + ' bibit';
                                }
                            }
                        }
                    }
                }
            });
        }
    } else {
        const chartEl = document.getElementById('stockByProvinceChart');
        if (chartEl) {
            chartEl.parentElement.innerHTML = 
            '<p class="text-center text-muted py-5">Belum ada data stok per provinsi</p>';
        }
    }

    // Prepare Distribution Data
    const distributionStats = <?= json_encode($distributionStats ?? []) ?>;

    if (distributionStats && distributionStats.length > 0) {
        // Process data for Stacked Bar Chart
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        // Get unique provinces
        const provinces = [...new Set(distributionStats.map(item => item.province_name))];
        
        // Generate distinct colors for provinces
        const colors = [
            '#4e79a7', '#f28e2b', '#e15759', '#76b7b2', '#59a14f', 
            '#edc948', '#b07aa1', '#ff9da7', '#9c755f', '#bab0ac',
            '#882d17', '#8175aa', '#6baa2c', '#d6a319', '#be514b'
        ];
        
        // Create datasets
        const datasets = provinces.map((province, index) => {
            const data = months.map((_, monthIndex) => {
                const monthNum = monthIndex + 1;
                const record = distributionStats.find(item => 
                    item.province_name === province && parseInt(item.month) === monthNum
                );
                return record ? parseInt(record.total_distributed) : 0;
            });
            
            return {
                label: province,
                data: data,
                backgroundColor: colors[index % colors.length],
                stack: 'Stack 0'
            };
        });
        
        const ctxDist = document.getElementById('distributionChart');
        if (ctxDist) {
            new Chart(ctxDist, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                footer: (tooltipItems) => {
                                    let total = 0;
                                    tooltipItems.forEach((item) => {
                                        total += item.parsed.y;
                                    });
                                    return 'Total Bulan Ini: ' + total.toLocaleString();
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Bibit per Bulan (Tahun <?= date("Y") ?>)'
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    } else {
         const chartContainer = document.getElementById('distributionChart');
         if(chartContainer) {
            chartContainer.parentElement.innerHTML = 
                '<p class="text-center text-muted py-5">Belum ada data distribusi (Requests Delivered) tahun ini</p>';
         }
    }

    // Top Seedlings Chart
    if (topSeedlingsData && topSeedlingsData.length > 0) {
        const ctx2 = document.getElementById('topSeedlingsChart');
        if (ctx2) {
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: topSeedlingsData.map(item => item.seedling_name || 'Unknown'),
                    datasets: [{
                        label: 'Total Stok',
                        data: topSeedlingsData.map(item => parseInt(item.total_stock) || 0),
                        backgroundColor: 'rgba(33, 150, 243, 0.7)',
                        borderColor: 'rgba(33, 150, 243, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Stok: ' + context.parsed.x.toLocaleString() + ' bibit';
                                }
                            }
                        }
                    }
                }
            });
        }
    } else {
        const topChartEl = document.getElementById('topSeedlingsChart');
        if (topChartEl) {
            topChartEl.parentElement.innerHTML = 
            '<p class="text-center text-muted py-5">Belum ada data jenis bibit</p>';
        }
    }
});

// Update Trend Chart - REMOVED, replaced with Distribution Map

// Initialize Map Code Removed

</script>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--white);
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.5rem;
}

.stat-content h3 {
    margin: 0;
    font-size: 2rem;
    color: var(--primary-dark);
}

.stat-content p {
    margin: 0.25rem 0 0 0;
    color: var(--text-light);
    font-size: 0.875rem;
}

.quick-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.quick-actions .btn {
    flex: 1;
    min-width: 150px;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions .btn {
        flex: 1 1 100%;
    }
}
</style>
