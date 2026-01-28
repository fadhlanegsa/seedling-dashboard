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
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-map-marked-alt"></i> Stok per Provinsi</h3>
            </div>
            <div class="card-body">
                <canvas id="stockByProvinceChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-bar"></i> Top 10 Jenis Bibit</h3>
            </div>
            <div class="card-body">
                <canvas id="topSeedlingsChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h3><i class="fas fa-map-marked-alt"></i> Peta Distribusi Bibit</h3>
            </div>
            <div class="card-body p-0">
                <div id="dashboardMap" style="height: 500px; width: 100%;"></div>
            </div>
            <div class="card-footer text-center">
                <a href="<?= url('admin/map-distribution') ?>" class="btn btn-primary">
                    <i class="fas fa-expand"></i> Lihat Peta Lengkap dengan Filter
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-bolt"></i> Aksi Cepat</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="<?= url('admin/bpdas') ?>" class="btn btn-primary">
                        <i class="fas fa-building"></i> Kelola BPDAS & BPTH
                    </a>
                    <a href="<?= url('admin/seedling-types') ?>" class="btn btn-success">
                        <i class="fas fa-seedling"></i> Kelola Jenis Bibit
                    </a>
                    <a href="<?= url('admin/stock') ?>" class="btn btn-info">
                        <i class="fas fa-boxes"></i> Lihat Stok Nasional
                    </a>
                    <a href="<?= url('admin/requests') ?>" class="btn btn-warning">
                        <i class="fas fa-file-alt"></i> Kelola Permintaan
                    </a>
                    <a href="<?= url('admin/users') ?>" class="btn btn-secondary">
                        <i class="fas fa-users"></i> Kelola Pengguna
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
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
    document.getElementById('stockByProvinceChart').parentElement.innerHTML = 
        '<p class="text-center text-muted py-5">Belum ada data stok per provinsi</p>';
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
    document.getElementById('topSeedlingsChart').parentElement.innerHTML = 
        '<p class="text-center text-muted py-5">Belum ada data jenis bibit</p>';
}

// Update Trend Chart - REMOVED, replaced with Distribution Map

// Initialize Distribution Map
document.addEventListener('DOMContentLoaded', function() {
    const mapElement = document.getElementById('dashboardMap');
    if (mapElement) {
        // Initialize map centered on Indonesia
        const map = L.map('dashboardMap').setView([-2.5489, 118.0149], 5);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Marker cluster group
        const markers = L.markerClusterGroup();
        
        // Function to get marker color based on status
        function getMarkerIcon(status) {
            let color = 'blue';
            switch(status) {
                case 'pending': color = 'orange'; break;
                case 'approved': color = 'green'; break;
                case 'completed': color = 'blue'; break;
                case 'rejected': color = 'red'; break;
            }
            
            return L.icon({
                iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
        }
        
        // Load map data
        fetch('<?= url('admin/get-map-data') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    data.data.forEach(item => {
                        const marker = L.marker([item.latitude, item.longitude], {
                            icon: getMarkerIcon(item.status)
                        });
                        
                        const popupContent = `
                            <div style="min-width: 180px;">
                                <h6><strong>${item.requester_name}</strong></h6>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-seedling"></i> ${item.seedling_name}<br>
                                    <small>${item.quantity} bibit</small>
                                </p>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-building"></i> ${item.bpdas_name}
                                </p>
                                <p style="margin: 5px 0;">
                                    <span class="badge badge-${item.status === 'approved' ? 'success' : item.status === 'pending' ? 'warning' : item.status === 'completed' ? 'info' : 'danger'}">
                                        ${item.status.toUpperCase()}
                                    </span>
                                </p>
                            </div>
                        `;
                        
                        marker.bindPopup(popupContent);
                        markers.addLayer(marker);
                    });
                    
                    map.addLayer(markers);
                    
                    // Fit bounds to show all markers
                    if (data.data.length > 0) {
                        map.fitBounds(markers.getBounds(), { padding: [50, 50] });
                    }
                } else {
                    mapElement.innerHTML = '<div class="text-center p-5"><p class="text-muted">Belum ada data lokasi penanaman</p></div>';
                }
            })
            .catch(error => {
                console.error('Error loading map data:', error);
                mapElement.innerHTML = '<div class="text-center p-5"><p class="text-danger">Gagal memuat data peta</p></div>';
            });
    }
});

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
