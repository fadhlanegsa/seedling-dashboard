<?php
/**
 * Request Seedling Form
 */
?>

<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Ajukan Permintaan Bibit</h1>
    <p>Isi formulir di bawah ini untuk mengajukan permintaan bibit</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="<?= url('public/submit-request') ?>" method="POST" id="requestForm" enctype="multipart/form-data">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                    
                    <div class="form-group">
                        <label class="form-label required">Provinsi</label>
                        <select name="province_id" id="province_id" class="form-control" required>
                            <option value="">-- Pilih Provinsi --</option>
                            <?php foreach ($provinces as $province): ?>
                                <option value="<?= $province['id'] ?>">
                                    <?= htmlspecialchars($province['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required">BPDAS</label>
                        <select name="bpdas_id" id="bpdas_id" class="form-control" required disabled>
                            <option value="">-- Pilih Provinsi Terlebih Dahulu --</option>
                        </select>
                        <small class="form-text text-muted">Pilih BPDAS yang akan Anda ajukan permintaan</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required">Jenis Bibit</label>
                        <select name="seedling_type_id" id="seedling_type_id" class="form-control" required disabled>
                            <option value="">-- Pilih BPDAS Terlebih Dahulu --</option>
                        </select>
                        <small class="form-text text-muted">Hanya menampilkan jenis bibit yang tersedia di BPDAS terpilih</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required">Jumlah Bibit</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" 
                               min="1" required placeholder="Masukkan jumlah bibit">
                        <small class="form-text text-muted" id="stockInfo"></small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required">Tujuan Penggunaan</label>
                        <textarea name="purpose" class="form-control" rows="4" required 
                                  placeholder="Jelaskan tujuan penggunaan bibit (contoh: penghijauan, reboisasi, dll)"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required">Luas Lahan (Ha)</label>
                        <input type="number" name="land_area" id="land_area" class="form-control" 
                               step="0.001" min="0.001" required placeholder="Contoh: 0.001 atau 1.5">
                        <small class="form-text text-muted">Wajib diisi - Luas lahan yang akan ditanami (minimal 0.001 Ha)</small>
                    </div>
                    
                    <!-- Proposal Upload (conditional, shown when quantity > 25) -->
                    <div class="form-group" id="proposalGroup" style="display: none;">
                        <label class="form-label required">Surat Pengajuan/Proposal</label>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>Perhatian:</strong> Permintaan bibit lebih dari 25 batang wajib melampirkan surat pengajuan/proposal dalam format PDF (maksimal 1 MB).
                        </div>
                        <input type="file" name="proposal" id="proposal" class="form-control" 
                               accept=".pdf" data-max-size="1048576">
                        <small class="form-text text-muted">
                            <i class="fas fa-file-pdf"></i> Format: PDF | Ukuran maksimal: 1 MB
                        </small>
                        <div id="proposalError" class="text-danger mt-2" style="display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required">Lokasi Rencana Tanam</label>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            Klik pada peta untuk menentukan lokasi rencana penanaman bibit, atau gunakan tombol GPS untuk lokasi saat ini.
                        </div>
                        
                        <!-- Map Container -->
                        <div id="map" style="height: 400px; border-radius: 8px; margin-bottom: 10px;"></div>
                        
                        <!-- GPS Button -->
                        <button type="button" id="useGPSBtn" class="btn btn-success btn-sm mb-2">
                            <i class="fas fa-location-arrow"></i> Gunakan Lokasi Saat Ini
                        </button>
                        
                        <!-- Hidden inputs for coordinates -->
                        <input type="hidden" name="latitude" id="latitude" required>
                        <input type="hidden" name="longitude" id="longitude" required>
                        
                        <!-- Display coordinates -->
                        <div id="coordinatesDisplay" class="text-muted" style="font-size: 0.9em;">
                            <i class="fas fa-map-marker-alt"></i> 
                            <span id="coordText">Belum ada lokasi dipilih</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Ajukan Permintaan
                        </button>
                        <a href="<?= url('public/dashboard') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi</h5>
            </div>
            <div class="card-body">
                <h6><strong>Persyaratan:</strong></h6>
                <ul>
                    <li>Memiliki akun terdaftar</li>
                    <li>Data profil lengkap dan valid</li>
                    <li>Tujuan penggunaan jelas</li>
                </ul>
                
                <h6 class="mt-3"><strong>Proses Persetujuan:</strong></h6>
                <ol>
                    <li>Permintaan diajukan</li>
                    <li>BPDAS meninjau permintaan</li>
                    <li>BPDAS menyetujui/menolak</li>
                    <li>Notifikasi dikirim ke email</li>
                    <li>Unduh surat persetujuan (jika disetujui)</li>
                </ol>
                
                <div class="alert alert-warning mt-3">
                    <small>
                        <i class="fas fa-exclamation-triangle"></i> 
                        Pastikan stok bibit tersedia sebelum mengajukan permintaan
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province_id');
    const bpdasSelect = document.getElementById('bpdas_id');
    const seedlingSelect = document.getElementById('seedling_type_id');
    const quantityInput = document.getElementById('quantity');
    const stockInfo = document.getElementById('stockInfo');
    
    // Initialize Leaflet Map
    // Center on Indonesia
    const map = L.map('map').setView([-2.5489, 118.0149], 5);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Marker for selected location
    let marker = null;
    
    // Function to update coordinates
    function updateCoordinates(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        document.getElementById('coordText').innerHTML = 
            `<strong>Lat:</strong> ${lat.toFixed(6)}, <strong>Lng:</strong> ${lng.toFixed(6)}`;
        
        // Remove existing marker
        if (marker) {
            map.removeLayer(marker);
        }
        
        // Add new marker
        marker = L.marker([lat, lng], {
            draggable: true
        }).addTo(map);
        
        // Update coordinates when marker is dragged
        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateCoordinates(pos.lat, pos.lng);
        });
    }
    
    // Click on map to set location
    map.on('click', function(e) {
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });
    
    // Use GPS location
    document.getElementById('useGPSBtn').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengambil lokasi...';
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Update coordinates
                    updateCoordinates(lat, lng);
                    
                    // Center map on location
                    map.setView([lat, lng], 15);
                    
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-location-arrow"></i> Gunakan Lokasi Saat Ini';
                },
                function(error) {
                    alert('Gagal mendapatkan lokasi: ' + error.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-location-arrow"></i> Gunakan Lokasi Saat Ini';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            alert('Browser Anda tidak mendukung GPS');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-location-arrow"></i> Gunakan Lokasi Saat Ini';
        }
    });
    
    // Load BPDAS when province changes
    provinceSelect.addEventListener('change', function() {
        const provinceId = this.value;
        bpdasSelect.innerHTML = '<option value="">-- Loading... --</option>';
        bpdasSelect.disabled = true;
        seedlingSelect.innerHTML = '<option value="">-- Pilih BPDAS Terlebih Dahulu --</option>';
        seedlingSelect.disabled = true;
        stockInfo.textContent = '';
        
        if (provinceId) {
            fetch('<?= url('public/get-bpdas-by-province') ?>?province_id=' + provinceId)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        bpdasSelect.innerHTML = '<option value="">-- Pilih BPDAS --</option>';
                        data.data.forEach(bpdas => {
                            bpdasSelect.innerHTML += `<option value="${bpdas.id}">${bpdas.name}</option>`;
                        });
                        bpdasSelect.disabled = false;
                    } else {
                        bpdasSelect.innerHTML = '<option value="">-- Tidak Ada BPDAS --</option>';
                    }
                });
        }
    });
    
    // Load seedlings when BPDAS changes
    bpdasSelect.addEventListener('change', function() {
        const bpdasId = this.value;
        seedlingSelect.innerHTML = '<option value="">-- Loading... --</option>';
        seedlingSelect.disabled = true;
        stockInfo.textContent = '';
        
        if (bpdasId) {
            fetch('<?= url('public/get-seedlings-by-bpdas') ?>?bpdas_id=' + bpdasId)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        seedlingSelect.innerHTML = '<option value="">-- Pilih Jenis Bibit --</option>';
                        data.data.forEach(stock => {
                            seedlingSelect.innerHTML += `<option value="${stock.seedling_type_id}" data-quantity="${stock.quantity}">${stock.seedling_name} (Stok: ${stock.quantity})</option>`;
                        });
                        seedlingSelect.disabled = false;
                    } else {
                        seedlingSelect.innerHTML = '<option value="">-- Tidak Ada Stok Tersedia --</option>';
                    }
                });
        }
    });
    
    // Check stock when seedling or quantity changes
    function checkStock() {
        const bpdasId = bpdasSelect.value;
        const seedlingId = seedlingSelect.value;
        const quantity = quantityInput.value;
        
        if (bpdasId && seedlingId) {
            fetch(`<?= url('public/check-stock-availability') ?>?bpdas_id=${bpdasId}&seedling_type_id=${seedlingId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.available) {
                        const available = data.quantity;
                        if (quantity && parseInt(quantity) > available) {
                            stockInfo.innerHTML = `<span class="text-danger">Stok tidak mencukupi! Tersedia: ${available} bibit</span>`;
                        } else {
                            stockInfo.innerHTML = `<span class="text-success">Stok tersedia: ${available} bibit (Update: ${data.last_update})</span>`;
                        }
                    } else {
                        stockInfo.innerHTML = '<span class="text-danger">Stok tidak tersedia</span>';
                    }
                });
        }
    }
    
    seedlingSelect.addEventListener('change', checkStock);
    quantityInput.addEventListener('input', checkStock);
    
    // ===== PROPOSAL UPLOAD LOGIC =====
    const proposalGroup = document.getElementById('proposalGroup');
    const proposalInput = document.getElementById('proposal');
    const proposalError = document.getElementById('proposalError');
    
    // Show/hide proposal field based on quantity
    quantityInput.addEventListener('input', function() {
        const quantity = parseInt(this.value);
        
        if (quantity > 25) {
            proposalGroup.style.display = 'block';
            proposalInput.required = true;
        } else {
            proposalGroup.style.display = 'none';
            proposalInput.required = false;
            proposalInput.value = ''; // Clear file input
            proposalError.style.display = 'none';
        }
    });
    
    // Validate proposal file
    proposalInput.addEventListener('change', function() {
        const file = this.files[0];
        proposalError.style.display = 'none';
        
        if (!file) return;
        
        // Check file type
        if (file.type !== 'application/pdf') {
            proposalError.textContent = '❌ File harus berformat PDF';
            proposalError.style.display = 'block';
            this.value = '';
            return;
        }
        
        // Check file size (max 1MB = 1048576 bytes)
        const maxSize = 1048576;
        if (file.size > maxSize) {
            const sizeMB = (file.size / 1048576).toFixed(2);
            proposalError.textContent = `❌ Ukuran file terlalu besar (${sizeMB} MB). Maksimal 1 MB`;
            proposalError.style.display = 'block';
            this.value = '';
            return;
        }
        
        // File valid
        const sizeMB = (file.size / 1048576).toFixed(2);
        proposalError.innerHTML = `<span class="text-success">✓ File valid: ${file.name} (${sizeMB} MB)</span>`;
        proposalError.style.display = 'block';
    });
    
    // Form validation before submit
    const requestForm = document.getElementById('requestForm');
    requestForm.addEventListener('submit', function(e) {
        const quantity = parseInt(quantityInput.value);
        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;
        const landArea = document.getElementById('land_area').value;
        
        // Validate land area
        if (!landArea || parseFloat(landArea) <= 0) {
            e.preventDefault();
            alert('Luas lahan wajib diisi dan harus lebih dari 0');
            return false;
        }
        
        // Validate coordinates
        if (!latitude || !longitude) {
            e.preventDefault();
            alert('Lokasi tanam harus ditentukan pada peta');
            return false;
        }
        
        // Validate proposal if quantity > 25
        if (quantity > 25 && !proposalInput.files[0]) {
            e.preventDefault();
            alert('Permintaan bibit lebih dari 25 batang wajib melampirkan surat pengajuan/proposal');
            return false;
        }
    });
});
</script>
