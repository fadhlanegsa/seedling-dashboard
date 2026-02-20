<?php
/**
 * Admin Dashboard View
 * Displays analytics and statistics for administrators
 */
?>

<div class="page-header">
    <h1><i class="fas fa-chart-line"></i> Dashboard Admin</h1>
    <p>Selamat datang di Dashboard Stok Bibit Persemaian Indonesia</p>
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





<script>
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
