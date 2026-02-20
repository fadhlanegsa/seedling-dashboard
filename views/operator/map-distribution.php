<?php
/**
 * Map Distribution View - Operator
 * Visualisasi peta distribusi bibit untuk persemaian
 */
?>

<div class="page-header">
    <h1><i class="fas fa-map-marked-alt"></i> Peta Distribusi Bibit</h1>
    <p>Visualisasi sebaran lokasi permintaan bibit untuk persemaian Anda</p>
</div>

<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Data</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Jenis Bibit</label>
                    <select id="filterSeedling" class="form-control">
                        <option value="">Semua Jenis</option>
                        <?php foreach ($seedling_types as $type): ?>
                            <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <select id="filterStatus" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Disetujui</option>
                        <option value="delivered" selected>Sudah Diserahkan</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button id="applyFilter" class="btn btn-primary">
                    <i class="fas fa-search"></i> Terapkan Filter
                </button>
                <button id="resetFilter" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-globe"></i> Peta Sebaran</h5>
    </div>
    <div class="card-body p-0">
        <div id="distributionMap" style="height: 600px; width: 100%;"></div>
    </div>
    <div class="card-footer">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-map-marker-alt text-primary"></i>
                    <h3 id="totalMarkers">0</h3>
                    <p>Total Lokasi</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-clock text-warning"></i>
                    <h3 id="pendingCount">0</h3>
                    <p>Pending</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-check-circle text-success"></i>
                    <h3 id="approvedCount">0</h3>
                    <p>Disetujui</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-box {
    padding: 1rem;
}
.stat-box i {
    font-size: 2rem;
}
.stat-box h3 {
    margin: 0.5rem 0;
    font-size: 2rem;
    font-weight: bold;
}
.stat-box p {
    margin: 0;
    color: #666;
}
.marker-cluster-small {
    background-color: rgba(181, 226, 140, 0.6);
}
.marker-cluster-small div {
    background-color: rgba(110, 204, 57, 0.6);
}
.marker-cluster-medium {
    background-color: rgba(241, 211, 87, 0.6);
}
.marker-cluster-medium div {
    background-color: rgba(240, 194, 12, 0.6);
}
.marker-cluster-large {
    background-color: rgba(253, 156, 115, 0.6);
}
.marker-cluster-large div {
    background-color: rgba(241, 128, 23, 0.6);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Indonesia
    const map = L.map('distributionMap').setView([-2.5489, 118.0149], 5);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Marker cluster group
    let markers = L.markerClusterGroup();
    
    // Function to get marker color based on status
    function getMarkerIcon(status) {
        let color = 'blue';
        switch(status) {
            case 'pending': color = 'orange'; break;
            case 'approved': color = 'green'; break;
            case 'delivered': color = 'violet'; break;
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
    
    // Function to load map data
    function loadMapData() {
        const filters = {
            seedling_type_id: document.getElementById('filterSeedling').value,
            status: document.getElementById('filterStatus').value
        };
        
        // Build query string
        const queryString = Object.keys(filters)
            .filter(key => filters[key])
            .map(key => `${key}=${filters[key]}`)
            .join('&');
        
        fetch('<?= url('operator/get-map-data') ?>?' + queryString)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear existing markers
                    markers.clearLayers();
                    
                    // Add new markers
                    let stats = {
                        total: 0,
                        pending: 0,
                        approved: 0,
                    };
                    
                    if (data.data.length === 0) {
                        // User might expect to see nothing if they have no requests, leaving simpler alert or no alert
                        // console.log("No data found");
                    }
                    
                    data.data.forEach(item => {
                        const marker = L.marker([item.latitude, item.longitude], {
                            icon: getMarkerIcon(item.status)
                        });
                        
                        // Create popup content
                        const popupContent = `
                            <div style="min-width: 200px;">
                                <h6><strong>${item.requester_name}</strong></h6>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-seedling"></i> <strong>Jenis Bibit:</strong><br>
                                    ${item.seedling_name} (${item.quantity} bibit)
                                </p>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-building"></i> <strong>BPDAS:</strong><br>
                                    ${item.bpdas_name}
                                </p>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-map-marker-alt"></i> <strong>Provinsi:</strong><br>
                                    ${item.province_name}
                                </p>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-calendar"></i> <strong>Tanggal:</strong><br>
                                    ${new Date(item.created_at).toLocaleDateString('id-ID')}
                                </p>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-info-circle"></i> <strong>Status:</strong><br>
                                    <span class="badge badge-${item.status === 'approved' ? 'success' : item.status === 'pending' ? 'warning' : item.status === 'completed' ? 'info' : 'danger'}">
                                        ${item.status.toUpperCase()}
                                    </span>
                                </p>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-hashtag"></i> <strong>No. Permintaan:</strong><br>
                                    ${item.request_number}
                                </p>
                            </div>
                        `;
                        
                        marker.bindPopup(popupContent);
                        markers.addLayer(marker);
                        
                        // Update stats
                        stats.total++;
                        if (item.status === 'pending') stats.pending++;
                        if (item.status === 'approved') stats.approved++;
                    });
                    
                    // Add markers to map
                    map.addLayer(markers);
                    
                    // Update statistics
                    document.getElementById('totalMarkers').textContent = stats.total;
                    document.getElementById('pendingCount').textContent = stats.pending;
                    document.getElementById('approvedCount').textContent = stats.approved;
                    
                    // Fit bounds if there are markers
                    if (stats.total > 0) {
                        map.fitBounds(markers.getBounds(), { padding: [50, 50] });
                    }
                } else {
                    alert('Gagal memuat data peta: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat data');
            });
    }
    
    // Event listeners
    document.getElementById('applyFilter').addEventListener('click', loadMapData);
    document.getElementById('resetFilter').addEventListener('click', function() {
        document.getElementById('filterSeedling').value = '';
        document.getElementById('filterStatus').value = '';
        loadMapData();
    });
    
    // Load initial data
    loadMapData();
});
</script>
