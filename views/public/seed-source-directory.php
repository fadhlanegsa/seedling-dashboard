<?php
/**
 * Public: Seed Source Directory with Map
 */
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <style>
        #map { height: 500px; width: 100%; margin-bottom: 2rem; }
        .search-panel { background: white; padding: 1.5rem; margin-bottom: 1rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .source-card { background: white; padding: 1.5rem; margin-bottom: 1rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s; }
        .source-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .source-card h4 { color: #2c5530; margin-bottom: 0.5rem; }
        .source-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; }
        .info-item { display: flex; align-items: start; }
        .info-item i { color: #4a7c4e; margin-right: 0.5rem; margin-top: 3px; }
        .info-item strong { min-width: 120px; }
        .contact-highlight { background: #e8f5e9; padding: 0.5rem 1rem; border-radius: 4px; display: inline-block; margin-top: 0.5rem; }
        .contact-highlight i { color: #2e7d32; }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="page-header text-center">
        <h1><i class="fas fa-tree"></i> Direktori Sumber Benih Nasional</h1>
        <p class="lead">Temukan sumber benih resmi dan tersertifikasi di seluruh Indonesia</p>
    </div>
    
    <!-- Search Panel -->
    <div class="search-panel">
        <form id="searchForm">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_seedling_type">Jenis Pohon</label>
                        <select class="form-control" id="filter_seedling_type" name="seedling_type_id">
                            <option value="">Semua Jenis</option>
                            <?php foreach ($seedlingTypes as $type): ?>
                            <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_province">Provinsi</label>
                        <select class="form-control" id="filter_province" name="province_id">
                            <option value="">Semua Provinsi</option>
                            <?php foreach ($provinces as $prov): ?>
                            <option value="<?= $prov['id'] ?>"><?= htmlspecialchars($prov['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_seed_class">Kelas SB</label>
                        <input type="text" class="form-control" id="filter_seed_class" name="seed_class" placeholder="Contoh: TBT">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Cari
            </button>
            <button type="button" class="btn btn-secondary" id="resetBtn">
                <i class="fas fa-redo"></i> Reset
            </button>
        </form>
    </div>
    
    <!-- Map -->
    <div id="map"></div>
    
    <!-- Results -->
    <div id="results">
        <h3 class="mb-3">Hasil Pencarian (<?= count($seedSources) ?> sumber benih)</h3>
        <div id="resultsList">
            <?php if (empty($seedSources)): ?>
            <div class="alert alert-info">Belum ada data sumber benih</div>
            <?php else: ?>
                <?php foreach ($seedSources as $source): ?>
                <div class="source-card">
                    <h4><?= htmlspecialchars($source['seed_source_name']) ?></h4>
                    <?php if ($source['local_name']): ?>
                    <p class="text-muted"><em><?= htmlspecialchars($source['local_name']) ?></em></p>
                    <?php endif; ?>
                    
                    <div class="source-info">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Lokasi:</strong><br>
                                <?= htmlspecialchars($source['location'] ?? '-') ?>, <?= htmlspecialchars($source['province_name']) ?>
                            </div>
                        </div>
                        
                        <?php if ($source['seedling_type_name']): ?>
                        <div class="info-item">
                            <i class="fas fa-seedling"></i>
                            <div>
                                <strong>Jenis:</strong><br>
                                <?= htmlspecialchars($source['seedling_type_name']) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($source['seed_class']): ?>
                        <div class="info-item">
                            <i class="fas fa-certificate"></i>
                            <div>
                                <strong>Kelas SB:</strong><br>
                                <?= htmlspecialchars($source['seed_class']) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($source['owner_name']): ?>
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <div>
                                <strong>Pemilik:</strong><br>
                                <?= htmlspecialchars($source['owner_name']) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($source['owner_phone']): ?>
                    <div class="contact-highlight">
                        <i class="fas fa-phone"></i>
                        <strong>Kontak:</strong> 
                        <a href="tel:<?= $source['owner_phone'] ?>"><?= htmlspecialchars($source['owner_phone']) ?></a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <a href="<?= url('public/seed-source-detail/' . $source['id']) ?>" class="btn btn-sm btn-success">
                            <i class="fas fa-info-circle"></i> Lihat Detail
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
// Initialize map
const map = L.map('map').setView([-2.5, 118], 5); // Center of Indonesia

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Marker cluster group
const markers = L.markerClusterGroup();

// Load markers
function loadMarkers(filters = {}) {
    // Build query string
    const params = new URLSearchParams(filters);
    
    fetch('<?= url('public/get-seed-sources-map-data') ?>?' + params.toString())
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                markers.clearLayers();
                
                const geojson = data.data;
                
                geojson.features.forEach(feature => {
                    const props = feature.properties;
                    const coords = feature.geometry.coordinates;
                    
                    const popupContent = `
                        <div style="min-width: 200px;">
                            <h4>${props.name}</h4>
                            ${props.local_name ? `<p class="text-muted"><em>${props.local_name}</em></p>` : ''}
                            <p><strong>Lokasi:</strong> ${props.location || '-'}</p>
                            <p><strong>Provinsi:</strong> ${props.province}</p>
                            ${props.seedling_type ? `<p><strong>Jenis:</strong> ${props.seedling_type}</p>` : ''}
                            ${props.seed_class ? `<p><strong>Kelas:</strong> ${props.seed_class}</p>` : ''}
                            ${props.phone ? `<p><strong>Kontak:</strong> <a href="tel:${props.phone}">${props.phone}</a></p>` : ''}
                            <a href="<?= url('public/seed-source-detail/') ?>${props.id}" class="btn btn-sm btn-success">Detail</a>
                        </div>
                    `;
                    
                    const marker = L.marker([coords[1], coords[0]])
                        .bindPopup(popupContent);
                    
                    markers.addLayer(marker);
                });
                
                map.addLayer(markers);
                
                if (markers.getLayers().length > 0) {
                    map.fitBounds(markers.getBounds(), { padding: [50, 50] });
                }
            }
        });
}

// Initial load
loadMarkers();

// Search form
$('#searchForm').on('submit', function(e) {
    e.preventDefault();
    
    const filters = {
        seedling_type_id: $('#filter_seedling_type').val(),
        province_id: $('#filter_province').val(),
        seed_class: $('#filter_seed_class').val()
    };
    
    // Remove empty values
    Object.keys(filters).forEach(key => !filters[key] && delete filters[key]);
    
    // Update map
    loadMarkers(filters);
    
    // Update results list via AJAX
    const params = new URLSearchParams(filters);
    fetch('<?= url('public/search-seed-sources') ?>?' + params.toString())
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateResultsList(data.data);
            }
        });
});

// Reset button
$('#resetBtn').on('click', function() {
    $('#searchForm')[0].reset();
    loadMarkers();
    location.reload(); // Reload to show all results
});

function updateResultsList(sources) {
    const resultsDiv = $('#resultsList');
    
    if (sources.length === 0) {
        resultsDiv.html('<div class="alert alert-info">Tidak ada hasil yang sesuai dengan filter</div>');
        return;
    }
    
    let html = '';
    sources.forEach(source => {
        html += `
            <div class="source-card">
                <h4>${source.seed_source_name}</h4>
                ${source.local_name ? `<p class="text-muted"><em>${source.local_name}</em></p>` : ''}
                <div class="source-info">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div><strong>Lokasi:</strong><br>${source.location || '-'}, ${source.province_name}</div>
                    </div>
                    ${source.seedling_type_name ? `
                    <div class="info-item">
                        <i class="fas fa-seedling"></i>
                        <div><strong>Jenis:</strong><br>${source.seedling_type_name}</div>
                    </div>` : ''}
                    ${source.seed_class ? `
                    <div class="info-item">
                        <i class="fas fa-certificate"></i>
                        <div><strong>Kelas SB:</strong><br>${source.seed_class}</div>
                    </div>` : ''}
                    ${source.owner_name ? `
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <div><strong>Pemilik:</strong><br>${source.owner_name}</div>
                    </div>` : ''}
                </div>
                ${source.owner_phone ? `
                <div class="contact-highlight">
                    <i class="fas fa-phone"></i>
                    <strong>Kontak:</strong> 
                    <a href="tel:${source.owner_phone}">${source.owner_phone}</a>
                </div>` : ''}
                <div class="mt-3">
                    <a href="<?= url('public/seed-source-detail/') ?>${source.id}" class="btn btn-sm btn-success">
                        <i class="fas fa-info-circle"></i> Lihat Detail
                    </a>
                </div>
            </div>
        `;
    });
    
    resultsDiv.html(html);
    $('#results h3').text(`Hasil Pencarian (${sources.length} sumber benih)`);
}
</script>

</body>
</html>
