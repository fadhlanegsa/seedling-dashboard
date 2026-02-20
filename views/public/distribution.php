<?php
/**
 * Map Distribution View - Public
 * Visualisasi peta distribusi bibit
 */
?>

<!-- Hero Section for Map -->
<section class="page-hero" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); padding: 3rem 0; color: white !important; margin-bottom: 2rem;">
    <div class="container text-center">
        <h1 style="font-family: 'Poppins', sans-serif; font-weight: 700; margin-bottom: 1rem; color: white !important;"><i class="fas fa-map-marked-alt"></i> Peta Sebaran Bibit</h1>
        <p style="font-size: 1.2rem; opacity: 0.9; color: white !important;">Pantau lokasi penanaman bibit di seluruh Indonesia secara real-time</p>
    </div>
</section>

<div class="container mb-5">
    <!-- Filter Card -->
    <div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; overflow: hidden;">
        <div class="card-body p-4">
            <h5 class="mb-4" style="color: var(--primary-dark); font-weight: 600;"><i class="fas fa-filter"></i> Filter Data Sebaran</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-weight: 500;">Provinsi</label>
                        <select id="filterProvince" class="form-control" style="border-radius: 8px;">
                            <option value="">Semua Provinsi</option>
                            <?php foreach ($provinces as $province): ?>
                                <option value="<?= $province['id'] ?>"><?= htmlspecialchars($province['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-weight: 500;">BPDAS</label>
                        <select id="filterBPDAS" class="form-control" style="border-radius: 8px;">
                            <option value="">Semua BPDAS</option>
                            <?php foreach ($bpdas_list as $bpdas): ?>
                                <option value="<?= $bpdas['id'] ?>"><?= htmlspecialchars($bpdas['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-weight: 500;">Jenis Bibit</label>
                        <select id="filterSeedling" class="form-control" style="border-radius: 8px;">
                            <option value="">Semua Jenis</option>
                            <?php foreach ($seedling_types as $type): ?>
                                <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12 text-right">
                    <button id="resetFilter" class="btn btn-outline-secondary mr-2" style="border-radius: 50px; padding: 0.5rem 1.5rem;">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                    <button id="applyFilter" class="btn btn-success" style="background-color: var(--primary-color); border: none; border-radius: 50px; padding: 0.5rem 1.5rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                        <i class="fas fa-search"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Card -->
    <div class="card shadow-lg" style="border: none; border-radius: 20px; overflow: hidden;">
        <div class="card-body p-0">
            <div id="distributionMap" style="height: 600px; width: 100%;"></div>
        </div>
        <div class="card-footer bg-white p-4">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="stat-box">
                        <i class="fas fa-map-marker-alt text-primary" style="color: var(--primary-color) !important;"></i>
                        <h3 id="totalMarkers" style="color: var(--text-dark);">0</h3>
                        <p>Total Lokasi Tanam</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <i class="fas fa-tree text-success" style="color: var(--success-color) !important;"></i>
                        <h3 id="approvedCount" style="color: var(--text-dark);">0</h3>
                        <p>Total Bibit Disetujui</p>
                    </div>
                </div>
                 <div class="col-md-4">
                    <div class="stat-box">
                        <i class="fas fa-check-double text-info" style="color: var(--info-color) !important;"></i>
                        <h3 id="deliveredCount" style="color: var(--text-dark);">0</h3>
                        <p>Total Bibit Diterima</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<style>
.stat-box {
    padding: 1rem;
    transition: transform 0.3s ease;
}
.stat-box:hover {
    transform: translateY(-5px);
}
.stat-box i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}
.stat-box h3 {
    margin: 0.5rem 0;
    font-size: 2.5rem;
    font-weight: 800;
}
.stat-box p {
    margin: 0;
    color: #666;
    font-size: 1.1rem;
}
/* Custom Popup Style */
.leaflet-popup-content-wrapper {
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
.leaflet-popup-content {
    margin: 15px;
}
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

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
    
    // Function to get marker color based on seedling category (fun tweak for public)
    // Or just strictly by status
    function getMarkerIcon(status) {
        // Public map: Green for everything successful, Orange for pending.
        let color = 'green';
        if (status === 'pending') color = 'orange';
        if (status === 'rejected') color = 'red';
        
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
        // Show loading state if desired
        
        const filters = {
            province_id: document.getElementById('filterProvince').value,
            bpdas_id: document.getElementById('filterBPDAS').value,
            seedling_type_id: document.getElementById('filterSeedling').value,
            status: '' // Get all statuses, we'll filter visually or let controller handle
        };
        
        // Build query string
        const queryString = Object.keys(filters)
            .filter(key => filters[key])
            .map(key => `${key}=${filters[key]}`)
            .join('&');
        
        // Ensure this URL matches the public route
        fetch('<?= url('home/get-map-data') ?>?' + queryString)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear existing markers
                    markers.clearLayers();
                    
                    // Add new markers
                    let stats = {
                        total: 0,
                        approved: 0,
                        delivered: 0
                    };
                    
                    data.data.forEach(item => {
                        // For public map, maybe hide rejected?
                        if (item.status === 'rejected') return;
                        
                        const marker = L.marker([item.latitude, item.longitude], {
                            icon: getMarkerIcon(item.status)
                        });
                        
                        // Create popup content
                        // Privacy: Don't show full requester name? Let's show "Bpk/Ibu [First Name]" or masked
                        const maskedName = item.requester_name.substr(0, 1) + '***';
                        
                        const popupContent = `
                            <div style="min-width: 200px;">
                                <h6 style="color: var(--primary-color);"><strong>Lokasi Penanaman</strong></h6>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-seedling" style="color: green;"></i> <strong>Bibit:</strong><br>
                                    ${item.seedling_name} (${item.quantity} bbt)
                                </p>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-map-marker-alt" style="color: red;"></i> <strong>Provinsi:</strong><br>
                                    ${item.province_name}
                                </p>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-check-circle" style="color: blue;"></i> <strong>Status:</strong><br>
                                    <span class="badge badge-success">
                                        ${item.status === 'delivered' ? 'Diterima' : (item.status === 'approved' ? 'Disetujui' : item.status)}
                                    </span>
                                </p>
                            </div>
                        `;
                        
                        marker.bindPopup(popupContent);
                        markers.addLayer(marker);
                        
                        // Update stats
                        stats.total++;
                        if (item.status === 'approved') stats.approved++;
                        if (item.status === 'delivered' || item.status === 'completed') stats.delivered++;
                        // Assume delivered/completed are same category for counting
                    });
                    
                    // Add markers to map
                    map.addLayer(markers);
                    
                    // Update statistics
                    document.getElementById('totalMarkers').textContent = stats.total.toLocaleString('id-ID');
                     // Note: We might want to pass these counts from backend for accuracy, but client-side counting of fetching data works for visual
                    document.getElementById('approvedCount').textContent = stats.approved.toLocaleString('id-ID');
                    document.getElementById('deliveredCount').textContent = stats.delivered.toLocaleString('id-ID');
                    
                    // Fit bounds if there are markers
                    if (stats.total > 0) {
                        map.fitBounds(markers.getBounds(), { padding: [50, 50] });
                    }
                } else {
                    console.error('Failed to load map data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    // Event listeners
    document.getElementById('applyFilter').addEventListener('click', loadMapData);
    document.getElementById('resetFilter').addEventListener('click', function() {
        document.getElementById('filterProvince').value = '';
        document.getElementById('filterBPDAS').value = '';
        document.getElementById('filterSeedling').value = '';
        loadMapData();
    });
    
    // Load initial data
    loadMapData();
});
</script>
