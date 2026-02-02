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
                    
                    <!-- Dynamic Items Section -->
                    <div class="form-group">
                        <label class="form-label required">Jenis Bibit yang Diminta</label>
                        <small class="form-text text-muted mb-2 d-block">
                            <i class="fas fa-info-circle"></i> Anda dapat memilih beberapa jenis bibit sekaligus
                        </small>
                        
                        <!-- Items Container -->
                        <div id="itemsContainer">
                            <!-- Item template will be added here -->
                        </div>
                        
                        <!-- Add Button -->
                        <button type="button" id="addItemBtn" class="btn btn-success btn-sm mt-2" disabled>
                            <i class="fas fa-plus"></i> Tambah Jenis Bibit Lain
                        </button>
                        
                        <!-- Total Display -->
                        <div class="alert alert-info mt-3" id="totalDisplay" style="display: none;">
                            <strong><i class="fas fa-calculator"></i> Total Permintaan:</strong> 
                            <span id="totalQuantity">0</span> bibit
                        </div>
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
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItemBtn');
    const totalDisplay = document.getElementById('totalDisplay');
    const totalQuantitySpan = document.getElementById('totalQuantity');
    
    let itemCounter = 0;
    let availableStocks = []; // Store available stock data
    
    // Initialize Leaflet Map
    const map = L.map('map').setView([-2.5489, 118.0149], 5);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    let marker = null;
    
    function updateCoordinates(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        document.getElementById('coordText').innerHTML = 
            `<strong>Lat:</strong> ${lat.toFixed(6)}, <strong>Lng:</strong> ${lng.toFixed(6)}`;
        
        if (marker) {
            map.removeLayer(marker);
        }
        
        marker = L.marker([lat, lng], {
            draggable: true
        }).addTo(map);
        
        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateCoordinates(pos.lat, pos.lng);
        });
    }
    
    map.on('click', function(e) {
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });
    
    document.getElementById('useGPSBtn').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengambil lokasi...';
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    updateCoordinates(lat, lng);
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
        
        // Clear items
        itemsContainer.innerHTML = '';
        addItemBtn.disabled = true;
        availableStocks = [];
        calculateTotal();
        
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
        
        // Clear items
        itemsContainer.innerHTML = '';
        addItemBtn.disabled = true;
        availableStocks = [];
        calculateTotal();
        
        if (bpdasId) {
            fetch('<?= url('public/get-seedlings-by-bpdas') ?>?bpdas_id=' + bpdasId)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        availableStocks = data.data;
                        addItemBtn.disabled = false;
                        
                        // Auto-add first item
                        addItem();
                    } else {
                        alert('Tidak ada stok bibit tersedia di BPDAS ini');
                    }
                });
        }
    });
    
    // Add item function
    function addItem() {
        itemCounter++;
        const itemId = `item_${itemCounter}`;
        
        const itemDiv = document.createElement('div');
        itemDiv.className = 'card mb-3';
        itemDiv.id = itemId;
        itemDiv.innerHTML = `
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label required">Jenis Bibit #${itemCounter}</label>
                        <select name="items[${itemCounter}][seedling_type_id]" class="form-control item-seedling" data-item-id="${itemId}" required>
                            <option value="">-- Pilih Jenis Bibit --</option>
                            ${availableStocks.map(stock => 
                                `<option value="${stock.seedling_type_id}" data-stock="${stock.quantity}">
                                    ${stock.seedling_name} (Stok: ${stock.quantity})
                                </option>`
                            ).join('')}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required">Jumlah</label>
                        <input type="number" name="items[${itemCounter}][quantity]" class="form-control item-quantity" 
                               data-item-id="${itemId}" min="1" required placeholder="Jumlah">
                        <small class="form-text stock-info-${itemId}"></small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-item-btn" data-item-id="${itemId}">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        itemsContainer.appendChild(itemDiv);
        
        // Attach event listeners
        const seedlingSelect = itemDiv.querySelector('.item-seedling');
        const quantityInput = itemDiv.querySelector('.item-quantity');
        const removeBtn = itemDiv.querySelector('.remove-item-btn');
        
        seedlingSelect.addEventListener('change', function() {
            validateItemStock(itemId);
            calculateTotal();
        });
        
        quantityInput.addEventListener('input', function() {
            validateItemStock(itemId);
            calculateTotal();
        });
        
        removeBtn.addEventListener('click', function() {
            removeItem(itemId);
        });
        
        // Show remove button only if more than 1 item
        updateRemoveButtons();
        calculateTotal();
    }
    
    // Remove item function
    function removeItem(itemId) {
        const itemDiv = document.getElementById(itemId);
        if (itemDiv) {
            itemDiv.remove();
            updateRemoveButtons();
            calculateTotal();
        }
    }
    
    // Update remove buttons visibility
    function updateRemoveButtons() {
        const items = itemsContainer.querySelectorAll('.card');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-item-btn');
            if (items.length === 1) {
                removeBtn.style.display = 'none';
            } else {
                removeBtn.style.display = 'block';
            }
        });
    }
    
    // Validate stock for individual item
    function validateItemStock(itemId) {
        const itemDiv = document.getElementById(itemId);
        if (!itemDiv) return;
        
        const seedlingSelect = itemDiv.querySelector('.item-seedling');
        const quantityInput = itemDiv.querySelector('.item-quantity');
        const stockInfo = itemDiv.querySelector(`.stock-info-${itemId}`);
        
        const selectedOption = seedlingSelect.options[seedlingSelect.selectedIndex];
        const availableStock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
        const requestedQty = parseInt(quantityInput.value) || 0;
        
        if (seedlingSelect.value && requestedQty > 0) {
            if (requestedQty > availableStock) {
                stockInfo.innerHTML = `<span class="text-danger">❌ Stok kurang! Tersedia: ${availableStock}</span>`;
                quantityInput.setCustomValidity('Jumlah melebihi stok');
            } else {
                stockInfo.innerHTML = `<span class="text-success">✓ Stok cukup</span>`;
                quantityInput.setCustomValidity('');
            }
        } else {
            stockInfo.innerHTML = '';
            quantityInput.setCustomValidity('');
        }
    }
    
    // Calculate total quantity
    function calculateTotal() {
        let total = 0;
        const quantityInputs = itemsContainer.querySelectorAll('.item-quantity');
        
        quantityInputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            total += qty;
        });
        
        totalQuantitySpan.textContent = total;
        
        if (total > 0) {
            totalDisplay.style.display = 'block';
        } else {
            totalDisplay.style.display = 'none';
        }
        
        // Show/hide proposal upload based on total
        const proposalGroup = document.getElementById('proposalGroup');
        const proposalInput = document.getElementById('proposal');
        
        if (total > 25) {
            proposalGroup.style.display = 'block';
            proposalInput.required = true;
        } else {
            proposalGroup.style.display = 'none';
            proposalInput.required = false;
            proposalInput.value = '';
        }
        
        return total;
    }
    
    // Add item button
    addItemBtn.addEventListener('click', addItem);
    
    // Proposal validation
    const proposalInput = document.getElementById('proposal');
    const proposalError = document.getElementById('proposalError');
    
    proposalInput.addEventListener('change', function() {
        const file = this.files[0];
        proposalError.style.display = 'none';
        
        if (!file) return;
        
        if (file.type !== 'application/pdf') {
            proposalError.textContent = '❌ File harus berformat PDF';
            proposalError.style.display = 'block';
            this.value = '';
            return;
        }
        
        const maxSize = 1048576;
        if (file.size > maxSize) {
            const sizeMB = (file.size / 1048576).toFixed(2);
            proposalError.textContent = `❌ Ukuran file terlalu besar (${sizeMB} MB). Maksimal 1 MB`;
            proposalError.style.display = 'block';
            this.value = '';
            return;
        }
        
        const sizeMB = (file.size / 1048576).toFixed(2);
        proposalError.innerHTML = `<span class="text-success">✓ File valid: ${file.name} (${sizeMB} MB)</span>`;
        proposalError.style.display = 'block';
    });
    
    // Form validation
    const requestForm = document.getElementById('requestForm');
    requestForm.addEventListener('submit', function(e) {
        const total = calculateTotal();
        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;
        const landArea = document.getElementById('land_area').value;
        
        // Validate at least one item
        if (total === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 jenis bibit dengan jumlah > 0');
            return false;
        }
        
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
        
        // Validate proposal if total > 25
        if (total > 25 && !proposalInput.files[0]) {
            e.preventDefault();
            alert('Permintaan bibit lebih dari 25 batang wajib melampirkan surat pengajuan/proposal');
            return false;
        }
    });
});
</script>
